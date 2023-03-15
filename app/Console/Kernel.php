<?php

namespace App\Console;

use App\Console\Commands\UpdateViewWeek;
use Aws\Command;
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
        Commands\UpdateLinkCloud::class,
        Commands\ConvertOldVideos::class,
        Commands\FixThubnail::class,
        Commands\UpdateResolution::class,
        Commands\UpdateVideoFilter::class,
        Commands\UpdateUserProfile::class,
        Commands\FirstUpdateVideoFilter::class,
        Commands\UpdateViewWeek::class,
        Commands\MarkFilterRecent::class,
        Commands\RemoveInvalidVideoGame::class,
        Commands\SetAdminPassword::class,
        Commands\DebugOverlapping::class,
        Commands\UpdateDurationTrending::class,
        Commands\CreateMontage::class,
        Commands\UpdateStreamer::class,
        Commands\CreateCached::class,
        Commands\CreateAdminReportDaily::class,
        Commands\AdminUtil::class,
        Commands\SendAlertEmailMonday::class,
        Commands\VideoUtil::class,
        Commands\BuildListStreamersLive::class,
        Commands\ScanStreamerStatus::class,
        Commands\CreateCohortUserVideo::class,
        Commands\CreateListUserSubcriber::class,
        Commands\CreateAdminReportWeekly::class,
        Commands\CreateUserGameList::class,
        Commands\ExtractLogBoombot::class,
        Commands\CreateChurnUserReminderDaily::class,
        Commands\StartFollowListStreamer::class,
        Commands\RestartFollowStreamer::class,
        Commands\FollowMontage::class,
        Commands\AddSubscriberViewer::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        if (config('app.env') == 'local' || config('app.env') == 'boom-beta' || config('app.env') ==  'boom-admin'){
            $schedule->command('boomtv:update-video-filter')->everyThirtyMinutes();
            $schedule->command('boomtv:update-profile')->dailyAt('10:00');
            $schedule->command('boomtv:update-view-week')->daily();
            //$schedule->command('boomtv:create-cached')->daily();
            $schedule->command('boomtv:create-cohort-user-video')->dailyAt('12:00');
            $schedule->command('boomtv:create-admin-report-daily')->dailyAt('12:00');
            $schedule->command('boomtv:create-admin-report-weekly')->mondays()->dailyAt('12:00');
            $schedule->command('boomtv:create-admin-report-weekly')->wednesdays()->dailyAt('12:00');
            $schedule->command('boomtv:create-admin-report-weekly')->fridays()->dailyAt('12:00');
        }
        if (config('app.env') == 'boom-admin'){
            $schedule->command('boomtv:send-mail-follow-montage')->dailyAt('12:00');
            $schedule->command('boomtv:send-alert-email-monday')->weekly()->tuesdays()->at("8:00");
            $schedule->command('admin:create-churn-user-reminder-daily')->dailyAt('9:00');
        }
        if (config('app.env') == 'boom-job'){
            //$schedule->command('boomtv:scan-streamer-status')->everyFiveMinutes();
        }
        $schedule->command('boomtv:update-duration-trending')->daily();
        /*if(config('app.env') == 'local' || config('app.env') == 'create_montage')
        {
            $schedule->command('boomtv:create-montage')->everyTenMinutes();
        }*/
    }
}
