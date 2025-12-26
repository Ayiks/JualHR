<?php

// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Mark absent employees daily at 11:59 PM
        // This runs for the previous day to ensure all check-ins are captured
        $schedule->command('attendance:mark-absent')
            ->dailyAt('23:59')
            ->withoutOverlapping()
            ->onOneServer();

        // Alternative: Run early morning for previous day
        // $schedule->command('attendance:mark-absent')
        //     ->dailyAt('01:00')
        //     ->withoutOverlapping()
        //     ->onOneServer();

        // Optional: Send daily attendance summary to admins
        // $schedule->command('attendance:send-summary')
        //     ->dailyAt('18:00')
        //     ->weekdays();

        // Optional: Generate weekly attendance reports
        // $schedule->command('attendance:weekly-report')
        //     ->weekly()
        //     ->mondays()
        //     ->at('09:00');
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