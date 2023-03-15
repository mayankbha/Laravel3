<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'skipToCscrfRoutes' => 'api/*' ,
        'incview'=>'incview',
        'vote' => 'vote',
        'test'=>'test' ,
        'getview'=>'getview',
        'getlist_video'=>'getlist_video',
        'getvideos'=>'videos',
        'getlike'=>'getlike',
        'likevideo'=>'likevideo',
        'getshare'=>'getshare',
        'contact'=>'contact',
        'signup'=>'signup',
        'timezone'=>'set_userzone',
        'event/map'=>'event/map',
        'event/like' => 'event/like',
        'check_noitify_transcoder' => 'check_noitify_transcoder',
        'likeimage'=>'likeimage',
        'vrbeta' => 'vrbeta',
    ];
}
