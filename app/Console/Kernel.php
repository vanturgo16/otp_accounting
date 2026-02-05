<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\MstRule;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $logPath = storage_path('logs/log_sync_acc_daily_cron');
        // Ensure the directory exists
        if (!file_exists($logPath)) {
            mkdir($logPath, 0777, true);
        }

        $rule = MstRule::where('rule_name', 'Sync Time Accounting Daily')->first();
        if (!$rule || empty($rule->rule_value)) {
            // optional log biar ketahuan kalau rule kosong
            Log::warning('Sync Accounting Daily: rule not found or empty');
            return;
        }
        $time = $rule->rule_value ?? "21:00";
        
        $now = Carbon::now()->format('YmdHis');
        $schedule->command('sync:accounting-daily')
            ->dailyAt($time)
            ->sendOutputTo("storage/logs/log_sync_acc_daily_cron/response_at_" . $now . ".txt");;
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
