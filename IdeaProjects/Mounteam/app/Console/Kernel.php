<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Daily tasks
        $schedule->command('projects:check-deadlines')
                 ->daily()
                 ->at('09:00')
                 ->description('Check project deadlines and send notifications');

        $schedule->command('projects:update-overdue')
                 ->daily()
                 ->at('10:00')
                 ->description('Update overdue project statuses');

        // Weekly tasks
        $schedule->command('reports:weekly-summary')
                 ->weekly()
                 ->mondays()
                 ->at('08:00')
                 ->description('Send weekly project summary to admins');

        $schedule->command('cleanup:old-notifications')
                 ->weekly()
                 ->sundays()
                 ->at('02:00')
                 ->description('Clean up old notifications');

        // Monthly tasks
        $schedule->command('reports:monthly-analytics')
                 ->monthly()
                 ->description('Generate monthly analytics report');

        $schedule->command('cleanup:soft-deleted')
                 ->monthly()
                 ->description('Permanently delete old soft-deleted records');

        // Hourly tasks
        $schedule->command('queue:work --stop-when-empty')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->description('Process queued jobs');

        // Cache and optimization
        $schedule->command('cache:prune-stale-tags')
                 ->hourly()
                 ->description('Prune stale cache tags');

        $schedule->command('telescope:prune --hours=48')
                 ->daily()
                 ->description('Prune Telescope entries older than 48 hours');

        // Backup tasks (if using spatie/laravel-backup)
        $schedule->command('backup:clean')
                 ->daily()
                 ->at('01:00')
                 ->description('Clean old backups');

        $schedule->command('backup:run')
                 ->daily()
                 ->at('02:00')
                 ->description('Create daily backup');

        // Health checks
        $schedule->command('health:check')
                 ->everyFifteenMinutes()
                 ->description('Run application health checks');

        // Email reminders
        $schedule->command('emails:project-reminders')
                 ->dailyAt('16:00')
                 ->description('Send project reminder emails to clients');

        $schedule->command('emails:follow-up')
                 ->weekly()
                 ->wednesdays()
                 ->at('14:00')
                 ->description('Send follow-up emails for completed projects');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
