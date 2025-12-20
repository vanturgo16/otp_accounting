<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;

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
        
        $now = Carbon::now()->format('YmdHis');
        $schedule->command('sync:accounting-daily')
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer()
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
