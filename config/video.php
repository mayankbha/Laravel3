<?php

return [
    'rate_time_limit' => 60*3 ,
    'vtime' => 18,
    'playHlsByJW' => env('PLAY_HLS_BY_JW', false),
    'useSourceLink' => env('USE_SOURCE_LINK', 'cloudfront'),  // links3, cloudfront 
    'hlsMode' => env('HLS_MODE', true),
    'trackingId' => env('TRACKING_ID', 'UA-91305389-1'),
    'playerType' => ["normal","hls","jwplayer","bitmovin_player"],
    'createMontageFor' => env('CREATE_MONTAGE_FOR', "local"),
    'top_montage_numb' => 5,
    'min_video_montage' => 3,
    'limit_time' => 15,
    'auto_quality' => env('AUTO_QUALITY', "1")
];
