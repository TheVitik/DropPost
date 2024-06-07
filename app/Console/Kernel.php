<?php

namespace App\Console;

use App\Console\Commands\PlanPostTomorrow;
use App\Console\Commands\UpdateChatInfo;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(PlanPostTomorrow::class)->dailyAt('23:00')->runInBackground();
        $schedule->command(UpdateChatInfo::class)->dailyAt('00:00')->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
