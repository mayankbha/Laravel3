<?php

use Aws\Laravel\AwsServiceProvider;


return [

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. The minimum
    | required options are declared here, but the full set of possible options
    | are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */
    'credentials' => [
        'key'    => env('AWS_CREDENTIALS_KEY'),
        'secret' => env('AWS_CREDENTIALS_SECRET'),
    ],
    'region' => env('AWS_REGION', 'us-west-2'),
    'version' => 'latest',
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ],
    'bucket_upload_video' => 'boomtv-videos',
    'cloudfront'=>'https://d2540bljzu9e1.cloudfront.net',
    'links3' => 'https://s3-us-west-2.amazonaws.com/boomtv-videos',
    'sourceLink' => (env('USE_SOURCE_LINK')=='links3')? "https://s3-us-west-2.amazonaws.com/boomtv-videos":"https://d2540bljzu9e1.cloudfront.net",
    'folderServer' => env('SET_SERVER_NAME','beta-boomtv/'),
    'pipeline_id'=> (env('SERVER_TYPE')=='REAL')? "1480651505733-xx2mmp":"1486087161473-8bdsy0",
    'pipeline_id_montage' => "1493364552066-jted3a",
    'bucket_output_video'=>'boomtv-videos',
    'bucket_input_video'=>'boomtv-videos',
    'prefix_output_video'=>env('SET_SERVER_NAME','beta-boomtv/').'videos-hls/',
    'preset_id'=>[
        '8m'=>'1481289518593-37kz94',
        '5m'=>'1481289335026-2m4r5o',
        '3m'=>'1481289255017-ppoitf',
        '2m'=>'1351620000001-200010',
        /*  '1m'=>'1351620000001-200030',
        '600k'=>'1351620000001-200040',
        '2m_480p' => '1484044457515-ix8p2v',
        '2m_720p' => '1484043695577-a1havl',*/
        /*'1m'=>'1484816962538-c0g416',
        '600k'=>'1351620000001-200040',
        '2m_480p' => '1484817088798-9l67b2',
        '2m_720p' => '1484817188337-q96fay',*/
        '1m'=>'1484823577584-5tr3oj',
        '600k'=>'1351620000001-200040',
        '2m_480p' => '1484824038857-c80xb2',
        '2m_720p' => '1484824107160-rbflvp',
        '480p_360' => '1487924253326-2zigy0',
        '720p_360' => '1486633464531-xscahw',
        '1080p_360' => '1486633577619-z7wvvd',
        '1440p_360' => '1486633679981-p2km5m',
        '2048p_360' => '1486633748884-akm3jx',

    ],
    's3-input-folder' => 'videos',
    's3-video-360-folder' => 'videos-360',
    's3-thumb-folder' => 'thumb',
    'bucket_logs'=>'boomtv-logs',
    'folder_log' => env('SERVER_TYPE','LOCAL'),
    'links3_log' => 'https://s3-us-west-2.amazonaws.com/boomtv-logs',
    'padding_log' => 50,
    'folder_client' => env('SERVER_TYPE','LOCAL'),
    'folder_image' => 'images',
    'folders_s3_real' => [
                "videos" => "videos",
                "videos_hls" => "videos-hls",
                "videos_360" => "videos-360",
                "thumbnail" => "thumb",
                ],
    'folders_s3_beta' => [
                "videos" => "beta-boomtv/videos",
                "videos_hls" => "beta-boomtv/videos-hls",
                "videos_360" => "beta-boomtv/videos-360",
                "thumbnail" => "beta-boomtv/thumb",
                ],
    'bucket_contents' => "boomtv-contents",
    'folder_boom_meter' => 'boom_meter',
    'linkS3BoomMeter' => 'https://s3-us-west-2.amazonaws.com/boomtv-contents/boom_meter/',
    'bucket_admin_report' => 'boomtv-admin-report',
    'linkS3Report' => 'https://s3-us-west-2.amazonaws.com/boomtv-admin-report/',
    'folder_sponsorship_video' => 'sponsorship_video',
    'cloudfront_content' => 'https://dhfp6cbxih843.cloudfront.net',
    'bucket_boombot' => 'elasticbeanstalk-us-west-1-581621635724',
    'folder_log_boombot' => '/resources/environments/logs/publish/e-rieftg7wws/i-07c5e2e10aa4e6c3c/',
    'folder_team_banner' => 'team_banner',
];