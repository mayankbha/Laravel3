<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook'=>[
        'client_id'=>env('FACEBOOK_CLIENT_ID'),
        'client_secret'=>env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('REDIRECT_URI'),
    ],
    'google'=>[
        'client_id'=>env('GOOGLE_CLIENT_ID'),
        'client_secret'=>env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('REDIRECT_URI'),
    ],
    'twitter'=>[
        'client_id'=>env('TWITTER_CLIENT_ID'),
        'client_secret'=>env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_CALLBACK_URL'),
    ],
    'twitch'=>[
        'client_id'=>env('TWITCH_KEY'),
        'client_secret'=>env('TWITCH_SECRET'),
        'redirect' => env('REDIRECT_URI'),
    ],
	'youtube'=>[
        'client_id'=>env('YOUTUBE_KEY'),
        'client_secret'=>env('YOUTUBE_SECRET'),
        'redirect' => env('YOUTUBE_REDIRECT_URI'),
    ],
    'discord' => [
        'client_id' => env('DISCORD_KEY'),
        'client_secret' => env('DISCORD_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI'),
        'bot_token' => env('BOT_TOKEN'),
        'api_url' => 'https://discordapp.com/api',
        'channel_replay_name' => 'boom-replay'
    ],

];
