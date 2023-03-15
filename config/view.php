<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        realpath(base_path('resources/views')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => realpath(storage_path('framework/views')),
    'page_numb' => 12, 
    'page_index' => 10,
    'carousel_limit' => 6,
    'limit_filter_carousel' => 42,
    'number_filter_recent' => 60,
    'number_filter_highlight' => 60,
    'number_recent_video' => 120,
    'number_game_video' => 120,
    'number_highlight_video' => 120,
];
