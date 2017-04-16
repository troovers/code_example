<?php

namespace App\Console;

use App\Console\Commands\Deployments\Migrate;
use App\Console\Commands\Members\SaveMemberStats;
use App\Console\Commands\Members\SendBirthdayMail;
use App\Console\Commands\Tasks\SendNotification as SendTaskNotification;
use App\Console\Commands\Newsletters\SendNotification as SendNewsletterNotification;
use App\Console\Commands\App\Agenda\SendNotification as SendAgendaNotification;
use App\Console\Commands\App\Polls\SendNotification as SendPollNotification;
use App\Console\Commands\App\Users\SaveUserStats as SaveAppUsersStats;
use App\Console\Commands\Activities\SendNotification as SendActivityNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendAgendaNotification::class,
        SendPollNotification::class,
        SaveAppUsersStats::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Task to insert stats for the members
        $schedule->command('app:users:stats')->dailyAt('01:15');

        // Task to send notifications about an upcoming agenda item
        $schedule->command('agenda:notify')->dailyAt('12:00');

        // Task to send notifications about an upcoming poll deadline when people haven't voted yet
        $schedule->command('polls:notify')->dailyAt('11:30');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
