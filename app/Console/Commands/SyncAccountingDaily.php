<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MstRule;
use App\Models\MstAccountCodes;
use App\Models\ReportMonthly;
use Carbon\Carbon;

class SyncAccountingDaily extends Command
{
    protected $signature = 'sync:accounting-daily';
    protected $description = 'Sync accounting data daily based on rule time';

    public function handle()
    {
        $this->log('--- Starting Sync Accounting Daily ---');

        // --- 1. Scheduler logic starts here ---
        $currentPeriod  = now()->format('Y-m');
        $previousPeriod = now()->subMonth()->format('Y-m');

        // 1a. First run? (report_monthly empty)
        if (!ReportMonthly::exists()) {
            $this->firstInitialization($currentPeriod);
            $this->log('Monthly report initialized for first run.');
            return Command::SUCCESS;
        }

        // 1b. First day of month? open current month by get amount close prev month
        $today = now();
        if ($today->day === 1) {
            $this->log("First day of month. Opening current month: {$currentPeriod}");
            $this->openCurrentMonth($currentPeriod, $previousPeriod);
        }

        // 1c. Ensure new accounts mid-month
        $this->log("Checking for new accounts for current period: {$currentPeriod}");
        $this->ensureNewAccounts($currentPeriod);

        // 1d. Update current month closing balance daily
        $this->log("Updating current month closing balance: {$currentPeriod}");
        $this->updateCurrentMonthClosing($currentPeriod);

        $this->log('Accounting data synced successfully.');
        $this->log('--- End Sync Accounting Daily ---');

        return Command::SUCCESS;
    }

    // --- Helper methods ---

    private function firstInitialization($period)
    {
        $accounts = MstAccountCodes::all();

        foreach ($accounts as $account) {
            ReportMonthly::create([
                'id_account_code' => $account->id,
                'period' => $period,
                'opening_balance' => $account->opening_balance,
                'opening_balance_type' => $account->opening_balance_type,
                'closing_balance' => $account->balance,
                'closing_balance_type' => $account->balance_type,
            ]);
        }
    }

    private function openCurrentMonth($currentPeriod, $previousPeriod)
    {
        $accounts = MstAccountCodes::all();

        foreach ($accounts as $account) {
            $exists = ReportMonthly::where('period', $currentPeriod)
                ->where('id_account_code', $account->id)
                ->exists();

            if ($exists) continue;

            $lastMonth = ReportMonthly::where('period', $previousPeriod)
                ->where('id_account_code', $account->id)
                ->first();

            if ($lastMonth) {
                $openingBalance = $lastMonth->closing_balance;
                $openingType    = $lastMonth->closing_balance_type;
            } else {
                $openingBalance = $account->opening_balance;
                $openingType    = $account->opening_balance_type;
            }

            ReportMonthly::create([
                'id_account_code' => $account->id,
                'period' => $currentPeriod,
                'opening_balance' => $openingBalance,
                'opening_balance_type' => $openingType,
                'closing_balance' => $openingBalance,
                'closing_balance_type' => $openingType,
            ]);
        }
    }

    private function ensureNewAccounts($period)
    {
        $accounts = MstAccountCodes::all();

        foreach ($accounts as $account) {
            $exists = ReportMonthly::where('period', $period)
                ->where('id_account_code', $account->id)
                ->exists();

            if (!$exists) {
                ReportMonthly::create([
                    'id_account_code' => $account->id,
                    'period' => $period,
                    'opening_balance' => $account->opening_balance,
                    'opening_balance_type' => $account->opening_balance_type,
                    'closing_balance' => $account->balance,
                    'closing_balance_type' => $account->balance_type,
                ]);
            }
        }
    }

    // --- Daily closing snapshot ---
    private function updateCurrentMonthClosing($period)
    {
        $reports = ReportMonthly::where('period', $period)->get();

        foreach ($reports as $report) {
            $account = MstAccountCodes::find($report->id_account_code);

            if ($account) {
                $report->update([
                    'closing_balance'      => $account->balance,
                    'closing_balance_type' => $account->balance_type,
                ]);
            }
        }
    }

    // --- Logging helper ---
    private function log($message)
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] ' . $message);
    }
}
