<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // add your listeners (aka providers) here
            'SocialiteProviders\Twitch\TwitchExtendSocialite@handle',
            'SocialiteProviders\Discord\DiscordExtendSocialite@handle',
            'SocialiteProviders\YouTube\YouTubeExtendSocialite@handle',
        ],
        'Video.created' => [
            'App\Handlers\Events\VideoEvent@created',
        ],
        'Video.updated' => [
            'App\Handlers\Events\VideoEvent@updated',
        ],
        'Video.deleted' => [
            'App\Handlers\Events\VideoEvent@deleted',
        ],
        'VideoGame.created' => [
            'App\Handlers\Events\VideoEvent@createdVideoGame',
        ],
        'VideoGame.updated' => [
            'App\Handlers\Events\VideoEvent@updatedVideoGame',
        ],
        'VideoGame.updating' => [
            'App\Handlers\Events\VideoEvent@updatingVideoGame',
        ],
        'VideoGame.deleted' => [
            'App\Handlers\Events\VideoEvent@deletedVideoGame',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);


    }
}
