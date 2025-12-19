<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MstRule;
use App\Models\MstAccountCodes;
use App\Models\ReportMonthly;
use App\Models\GeneralLedger;
use Carbon\Carbon;

class SyncAccountingDaily extends Command
{
    protected $signature = 'sync:accounting-daily';
    protected $description = 'Sync accounting data daily based on rule time';

    public function handle()
    {
        $this->log('--- Starting Sync Accounting Daily ---');

        // --- 1. Check sync time from rule ---
        $rule = MstRule::where('rule_name', 'Sync Time Accounting Daily')->first();

        if (!$rule || empty($rule->rule_value)) {
            $this->log('Sync time rule not found.');
            return Command::SUCCESS;
        }

        $syncTime = Carbon::createFromFormat('H:i', $rule->rule_value)->format('H:i');
        $now      = now()->format('H:i');

        if ($now !== $syncTime) {
            $this->log("Not sync time yet. Current time: {$now}, Sync time: {$syncTime}");
            return Command::SUCCESS;
        }

        // --- 2. Scheduler logic starts here ---
        $currentPeriod  = now()->format('Y-m');
        $previousPeriod = now()->subMonth()->format('Y-m');

        // 2a. First run? (report_monthly empty)
        if (!ReportMonthly::exists()) {
            $this->firstInitialization($currentPeriod);
            $this->log('Monthly report initialized for first run.');
            return Command::SUCCESS;
        }

        // 2b. First day of month? Close previous month and open current month
        $today = now();
        if ($today->day === 1) {
            $this->log("First day of month. Closing previous month: {$previousPeriod}");
            $this->closePreviousMonth($previousPeriod);

            $this->log("Opening current month: {$currentPeriod}");
            $this->openCurrentMonth($currentPeriod, $previousPeriod);
        }

        // 2c. Ensure new accounts mid-month
        $this->log("Checking for new accounts for current period: {$currentPeriod}");
        $this->ensureNewAccounts($currentPeriod);

        // 2d. Update current month closing balance daily
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

    private function closePreviousMonth($period)
    {
        $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $period)->endOfMonth();

        $reports = ReportMonthly::where('period', $period)->get();

        foreach ($reports as $report) {
            $balance = (float) $report->opening_balance;
            $type    = $report->opening_balance_type;

            $transactions = GeneralLedger::where('id_account_code', $report->id_account_code)
                ->whereBetween('date_transaction', [$start, $end])
                ->get();

            foreach ($transactions as $trx) {
                $incoming     = (float) $trx->amount;
                $incomingType = $trx->transaction;

                // if same type, just add
                if ($type === $incomingType) {
                    $balance += $incoming;
                } else {
                    // different type → subtract
                    $result = $balance - $incoming;
                    if ($result > 0) {
                        $balance = $result; // keep original type (it "wins")
                    } elseif ($result < 0) {
                        $balance = abs($result);
                        $type = $incomingType; // incoming "wins"
                    } else {
                        $balance = 0;
                        $type = "D"; // default when zero (or set to null if you prefer)
                    }
                }
            }

            $report->update([
                'closing_balance' => $balance,
                'closing_balance_type' => $type,
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
                $openingBalance = $account->balance;
                $openingType    = $account->balance_type;
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

    private function updateCurrentMonthClosing($period)
    {
        $start = Carbon::parse($period)->startOfMonth();
        $end   = now()->endOfDay();

        $reports = ReportMonthly::where('period', $period)->get();

        foreach ($reports as $report) {
            $balance = (float) $report->opening_balance;
            $type    = $report->opening_balance_type;

            $transactions = GeneralLedger::where('id_account_code', $report->id_account_code)
                ->whereBetween('date_transaction', [$start, $end])
                ->get();

            foreach ($transactions as $trx) {
                $incoming     = (float) $trx->amount;
                $incomingType = $trx->transaction;

                // if same type, just add
                if ($type === $incomingType) {
                    $balance += $incoming;
                } else {
                    // different type → subtract
                    $result = $balance - $incoming;
                    if ($result > 0) {
                        $balance = $result; // keep original type (it "wins")
                    } elseif ($result < 0) {
                        $balance = abs($result);
                        $type = $incomingType; // incoming "wins"
                    } else {
                        $balance = 0;
                        $type = "D"; // default when zero (or set to null if you prefer)
                    }
                }
            }

            $report->update([
                'closing_balance' => $balance,
                'closing_balance_type' => $type,
            ]);
        }
    }

    // --- Logging helper ---
    private function log($message)
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] ' . $message);
    }
}
