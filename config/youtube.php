<?php

return [

    /**
     * Application Name.
     */
    'application_name' => 'youtube',

    /**
     * Client ID.
     */
    'client_id' => env('YOUTUBE_KEY', null),

    /**
     * Client Secret.
     */
    'client_secret' => env('YOUTUBE_SECRET', null),

    /**
     * Access Type
     */
    'access_type' => 'offline',

    /**
     * Approval Prompt
     */
    'approval_prompt' => 'force',

    /**
     * Scopes.
     */
    'scopes' => [
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.upload',
        'https://www.googleapis.com/auth/youtube.readonly'
    ],

    /**
     * Developer key.
     */
    'developer_key' => env('GOOGLE_DEVELOPER_KEY', null),

    /**
     * Route URI's
     */
    'routes' => [

        /**
         * The prefix for the below URI's
         */
        'prefix' => '',

        /**
         * Redirect URI
         */
        'redirect_uri' => env('YOUTUBE_REDIRECT_URI', null),

        /**
         * The autentication URI
         */
        'authentication_uri' => 'auth',

    ]

];