<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\MstRule;
use App\Models\GeneralLedger;
use App\Models\ReportMonthly;
use App\Models\MstAccountCodes;
use Illuminate\Support\Facades\Log;

class SyncAccountingDaily extends Command
{
    protected $signature    = 'sync:accounting-daily';
    protected $description  = 'Sync accounting data daily based on rule time';

    public function handle()
    {
        $this->log('--- Starting Sync Accounting Daily ---');
        $this->log('--------------------------------------');

        $rule = MstRule::where('rule_name', 'Recalculate All')->first();
        if ($rule && $rule->rule_value == 0) {
            // RECALCULATE LAST TWO MONTH
            $this->log('RECALCULATE REPORT FOR THE LAST TWO MONTHS.');
            $prevDate = \Carbon\Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d H:i:s');
            $this->recalculateReportInit($prevDate);
        } else {
            // RECALCULATE ALL TRANSACTION
            $this->log('RECALCULATE REPORT FROM FIRST TRANSACTION.');
            $minTransDate = GeneralLedger::min('date_transaction');
            if (!$minTransDate) { 
                $this->log('--- No Transaction Yet ---');
                $this->log('--------------------------------------');
                $this->log('--- End Sync Accounting Daily ---');
                return Command::SUCCESS;
            }
            $isAllSuccess = $this->recalculateReportInit($minTransDate);
            if ($isAllSuccess) {
                MstRule::updateOrCreate(
                    ['rule_name'  => 'Recalculate All'],
                    ['rule_value' => 0]
                );
            }
        }
        
        $this->log('--------------------------------------');
        $this->log('--- End Sync Accounting Daily ---');
        return Command::SUCCESS;
    }

    private function recalculateReportInit($minTransDate)
    {
        if (!$minTransDate) {
            return false;
        }

        $startDate   = \Carbon\Carbon::parse($minTransDate)->startOfMonth();
        $currentDate = \Carbon\Carbon::now()->startOfMonth();

        while ($startDate <= $currentDate) {
            $period = $startDate->format('Y-m');

            try {
                $this->log("PERIOD {$period} START");

                DB::transaction(function () use ($startDate, $period) {
                    $startMonth = $startDate->copy()->startOfMonth();
                    $endMonth   = $startDate->copy()->endOfMonth();

                    $accountCodes = MstAccountCodes::where('created_at', '<=', $endMonth)->get();

                    // GET ALL TRANSACTIONS THIS MONTH ONCE
                    $transactions = GeneralLedger::select('id_account_code', 'amount', 'transaction')
                        ->whereBetween('date_transaction', [$startMonth, $endMonth])
                        ->get()
                        ->groupBy('id_account_code');

                    // GET PREVIOUS REPORTS ONCE
                    $prevPeriod = $startDate->copy()->subMonth()->format('Y-m');
                    $prevReports = ReportMonthly::where('period', $prevPeriod)->get()->keyBy('id_account_code');

                    // GET ID ACCOUNT CODE THAT HAS REALLY RUNNING TRANSACTION IN PERIOD BEFORE
                    $accountHasTransactionBefore = GeneralLedger::where('date_transaction', '<', $startMonth)
                        ->distinct()
                        ->pluck('id_account_code')
                        ->toArray();

                    foreach ($accountCodes as $account) {
                        $isAnyTransactionBefore = in_array($account->id, $accountHasTransactionBefore);
                        if (!$isAnyTransactionBefore) {
                            // For Case: When In This Period User Edit Opening Balance Master Account Code (account code has not running in period before)
                            $openingBalance     = (float) $account->opening_balance;
                            $openingBalanceType = $account->opening_balance_type;
                        } else {
                            $prevReport         = $prevReports[$account->id] ?? null;
                            $openingBalance     = $prevReport ? (float) $prevReport->closing_balance : (float) $account->opening_balance;
                            $openingBalanceType = $prevReport ? $prevReport->closing_balance_type : $account->opening_balance_type;
                        }

                        $closingBalance     = $openingBalance;
                        $closingBalanceType = $openingBalanceType;

                        $transThisPeriods = $transactions[$account->id] ?? collect();

                        foreach ($transThisPeriods as $trans) {
                            if (!$trans->amount) {
                                continue;
                            }

                            $transAmount     = (float) $trans->amount;
                            $transAmountType = $trans->transaction;

                            if ($closingBalanceType === $transAmountType) {
                                $closingBalance += $transAmount;
                            } else {
                                $result = $closingBalance - $transAmount;
                                if ($result > 0) {
                                    $closingBalance = $result;
                                } elseif ($result < 0) {
                                    $closingBalance     = abs($result);
                                    $closingBalanceType = $transAmountType;
                                } else {
                                    $closingBalance     = 0;
                                    $closingBalanceType = 'D';
                                }
                            }
                        }

                        // Find existing monthly report or create new model instance (not saved yet)
                        $report = ReportMonthly::firstOrNew([
                            'id_account_code' => $account->id,
                            'period'          => $period,
                        ]);

                        $normalize = fn($v) => number_format((float)$v, 3, '.', '');
                        // New calculated values for this period
                        $newData = [
                            'opening_balance'      => $normalize($openingBalance),
                            'opening_balance_type' => $openingBalanceType,
                            'closing_balance'      => $normalize($closingBalance),
                            'closing_balance_type' => $closingBalanceType,
                        ];
                        // Current DB values (if record exists), If record is new (firstOrNew), these will be NULL/default values
                        $currentData = [
                            'opening_balance'      => $normalize($report->opening_balance),
                            'opening_balance_type' => $report->opening_balance_type,
                            'closing_balance'      => $normalize($report->closing_balance),
                            'closing_balance_type' => $report->closing_balance_type,
                        ];

                        // Compare FULL dataset
                        // - If record is new → values differ (NULL vs new values) → TRUE
                        // - If any field changes → TRUE
                        // - If exactly same → FALSE
                        $isChanged = $newData !== $currentData;

                        // Only insert/update when: record does not exist yet (new row) OR data has changed
                        if ($isChanged) {
                            // Fill model with new values
                            $report->fill($newData);
                            // Save will: INSERT if new record UPDATE if existing record
                            $report->save();

                            $this->log("- Update Report Account Code ID: {$account->id}");
                        }
                    }
                });

                $this->log("PERIOD {$period} RECALCULATED");

            } catch (\Throwable $e) {
                $this->log("PERIOD {$period} FAILED RECALCULATE");
                $this->log($e->getMessage());
                return false;
            }

            $startDate->addMonth();
        }

        return true;
    }

    // --- Logging helper ---
    private function log($message)
    {
        $formatted = '[' . now()->format('Y-m-d H:i:s') . '] ' . $message;
        $this->info($formatted);
        Log::info($formatted);
    }
}
