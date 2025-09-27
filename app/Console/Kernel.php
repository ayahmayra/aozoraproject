<?php

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
        // Generate monthly invoices on the 1st of each month at midnight
        $schedule->command('invoices:generate-monthly')
                 ->monthlyOn(1, '00:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Check for overdue invoices daily at 6 AM
        $schedule->command('invoices:check-overdue')
                 ->dailyAt('06:00')
                 ->withoutOverlapping();
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
