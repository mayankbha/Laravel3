<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
// twitter connect
Route::get('/connect_twitter', [
    'as' => 'connect_twitter', 'uses' => 'Auth\AuthController@connectTwitter'
]);
Route::get('/callback_twitter', [
    'as' => 'callback_twitter', 'uses' => 'Auth\AuthController@callbackTwitter'
]);

Route::post('/api/saveSetting', [
    'as' => 'saveSetting', 'uses' => 'ApiController@saveSetting'
]);

Route::post('/api/shareOnSocial', [
    'as' => 'shareOnSocial', 'uses' => 'ApiController@shareOnSocial'
]);

Route::group(['namespace' => 'Admin','prefix' => 'afkvr-admin','middleware' => ['admin']], function()
{

    Route::get('/', ['as' => 'admin','uses'=>'AdminController@index']);

    Route::get('/dailyActiveStreamers', ['as' => 'admin.dailyActiveStreamers','uses'=>'AdminController@dailyActiveStreamers']);

    Route::get('/event', ['as' => 'admin.event','uses' => 'EventController@index']);

    Route::post('/event', 'EventController@setEventStatus');

    Route::get('/setting', ['as' => 'admin.setting' , 'uses' => 'AdminController@showSetting']);

    Route::post('/setting', 'AdminController@saveSetting');

    Route::get('/boomMeterSetting', ['as' => 'admin.boomMeter' , 'uses' => 'BoomMeterController@getUploadImageBoomMeter']);
    
    Route::get('/customBoomMeter/{code}', ['as' => 'admin.customBoomMeter' , 'uses' => 'BoomMeterController@customBoomMeter']);
    Route::post('/uploadImageBoomMeter',  ['as' => 'admin.uploadBoomMeter' , 'uses' => 'BoomMeterController@uploadImageBoomMeter']);
    Route::get('/reviewBoomMeter/{code}', ['as' => 'admin.reviewBoomMeter' , 'uses' => 'BoomMeterController@review']);
    Route::post('/uploadCss', ['as' => 'admin.uploadCss' , 'uses' => 'BoomMeterController@uploadCss']);

    Route::get('/csv-viewer', ['as' => 'admin.csv-viewer' , 'uses' => 'AdminController@csvViewer']);
    Route::get('/csv-download', ['as' => 'admin.csv-download' , 'uses' => 'AdminController@csvDownload']);
    Route::get('/csv/difftool', ['as' => 'admin.csv-difftool' , 'uses' => 'AdminController@getUploadDiffTool']);
    Route::post('/csv/difftool', ['uses' => 'AdminController@postUploadDiffTool']);

    Route::get('/event/vod/list', ['as' => 'admin.event.vod.list','uses' => 'EventController@getListEventVod']);

    Route::get('/event/vod/edit', ['as' => 'admin.event.vod.edit','uses' => 'EventController@getEditVod']);

    Route::post('/event/vod/edit', ['uses' => 'EventController@postEditVod']);

    Route::get('/event/vod/add', ['as' => 'admin.event.vod.add','uses' => 'EventController@getAddVod']);

    Route::post('/event/vod/add', ['uses' => 'EventController@postAddVod']);

    Route::get('/event/vod/remove', ['as' => 'admin.event.vod.remove','uses' => 'EventController@removeVod']);

    Route::get('/event/vod/liveUrl/add', ['as' => 'admin.event.vod.liveUrl.add','uses' => 'EventController@getAddLiveUrl']);
    Route::post('/event/vod/liveUrl/add', ['uses' => 'EventController@postAddLiveUrl']);

    Route::get('/event/vod/liveUrl/edit', ['as' => 'admin.event.vod.liveUrl.edit','uses' => 'EventController@getEditLiveUrl']);
    Route::post('/event/vod/liveUrl/edit', ['uses' => 'EventController@postEditLiveUrl']);

    Route::get('/event/vod/liveUrl/remove', ['as' => 'admin.event.vod.liveUrl.remove','uses' => 'EventController@removeLiveUrl']);

    Route::get('/event/setOfLiveUrl', ['as' => 'admin.event.setOfUrl','uses' => 'EventController@showEventSetOfUrl']);
    Route::post('/event/setOfLiveUrl', ['uses' => 'EventController@postEventSetOfUrl']);

    // sponsorship
    Route::get('/setSponsorship', ['as' => 'admin.setSponsorship' , 'uses' => 'SponsorshipController@setSponsorship']);
    Route::post('/updateSponsorship', ['as' => 'admin.updateSponsorship' , 'uses' => 'SponsorshipController@uploadSponsorship']);
    Route::get('/deleteSponsorship', ['as' => 'admin.deleteSponsorship' , 'uses' => 'SponsorshipController@deleteSponsorship']);

    Route::get('/reminder/churnLog', ['as' => 'admin.reminder.churn','uses' => 'ReminderController@churnLog']);
    Route::get('/reminder/getEmailLog', ['as' => 'admin.reminder.getEmailLog','uses' => 'ReminderController@getEmailLog']);
    Route::get('/reminder/startReminder', ['as' => 'admin.reminder.start','uses' => 'ReminderController@startReminder']);
    Route::get('/reminder/userComeback', ['as' => 'admin.reminder.userComeback','uses' => 'ReminderController@reportUserComeback']);
    Route::get('/reminder/emailReport', ['as' => 'admin.reminder.emailReport','uses' => 'ReminderController@churnEmailReport']);

    Route::get('/team/index', ['as' => 'admin.team','uses' => 'TeamController@index']);
    Route::get('/team/addOrUpdate', ['as' => 'admin.team.addOrUpdateView','uses' => 'TeamController@addOrUpdateView']);
    Route::post('/team/addOrUpdate', ['as' => 'admin.team.addOrUpdate','uses' => 'TeamController@addOrUpdate']);
    Route::get('/team/delete', ['as' => 'admin.team.delete','uses' => 'TeamController@delete']);
    Route::post('/team/getUsersTeam', ['as' => 'admin.team.getUsersTeam','uses' => 'TeamController@getUsersTeam']);
    Route::get('/team/searchUser', ['as' => 'admin.team.searchUser','uses' => 'TeamController@searchUser']);
});

Route::group(['namespace' => 'AdminAuth','prefix' => 'afkvr-admin','middleware' => []], function()
{
    //Login Routes...
    Route::get('/login',[
        'as' => "admin.login",
        'uses' => 'AuthController@showLoginForm'
    ]);
    Route::post('/login','AuthController@login');
    Route::get('/logout',[
        'as' => "admin.logout",
        'uses' => 'AuthController@logout'
    ]);
});
Route::get('/team/{name}', ['as' => 'team','uses' => 'TeamController@team']);
Route::get('/teamvideo/{name}', ['as' => 'teamvideo','uses' => 'TeamController@teamvideo']);
Route::post('/teamvideo/changeBanner', ['as' => 'changeBanner','uses' => 'TeamController@changeBanner']);
Route::get('/embed/{code}', 'HomeController@embedVideo')->name("embed");
Route::get('/relogin', 'HomeController@reLogin')->name('relogin');
//Route::get('/', ['as' => 'homepage', 'uses' => 'HomeController@landing']);
Route::get('/', ['as' => 'home', function() {
    return redirect()->route("videos");
}]);
Route::get('/videos', ['as' => 'videos', 'uses' => 'HomeController@index']);

Route::get('/home', ['as' => 'ahome', 'uses' => 'HomeController@index']);

Route::get('/subscriptions', ['as' => 'subscriptions', 'middleware' => 'auth', 'uses' => 'SubscriptionsController@index']);
Route::get('/subscriptions/unsubscribe', ['as' => 'subscriptionsunsubscribe', 'middleware' => 'auth', 'uses' => 'SubscriptionsController@unsubscribe']);
Route::get('/subscriptions/subscribe', ['as' => 'subscriptionssubscribe', 'middleware' => 'auth', 'uses' => 'SubscriptionsController@subscribe']);

Route::get('/home/{view}', [
    'as' => 'homepage', 'uses' => 'HomeController@index'
]);
Route::get('/contact', 'HomeController@showContact')->name('contact');

Route::get('/terms', 'HomeController@showTerm')->name('terms');
Route::get('/privacy','HomeController@showPrivacy')->name('privacy');

Route::get('/signup', [
    'as' => 'signup', 'uses' => 'HomeController@comingsoon'
]);

Route::get('/unsubscribe', 'HomeController@unsubscribe');

Route::post('/set_userzone', 'HomeController@setUserZone');
Route::post('/contact', 'HomeController@contact');

Route::post('/signup', 'HomeController@signup');
Route::post('/likevideo', 'HomeController@likeVideo');
Route::post('/getlike', 'HomeController@getLike');
Route::post('/incview', 'HomeController@incView');
Route::post('/getshare', 'HomeController@getShare');
Route::post('/getview', 'HomeController@getView');
Route::post('/getlist_video', 'HomeController@getListVideo');

Route::get('/w', [
    'as' => 'playvideo', 'uses' => 'HomeController@videoDetail'
]);

Route::get('/filter', [
    'as' => 'filter', 'uses' => 'HomeController@filterIndex'
]);

/*Route::get('/', function () {
    return redirect()->to(route('home', ['view' => 'popular']));
});*/
Route::get('/landing', function(){
	return redirect()->to(route('faq'));
});
Route::get('/about', [
    'as' => 'about', 'uses' => 'HomeController@about'
]);


Route::get('/oauth', [
    'as' => 'oauth', 'uses' => 'Auth\AuthController@redirectToProvider'
]);
Route::get('/login-to-afkvr-admin', [
    'as' => 'login-to-afkvr-admin', 'uses' => 'Auth\AuthController@loginToAfkvrAdmin'
]);
Route::get('/logout', [
    'as' => 'logout', 'uses' => 'Auth\AuthController@logout'
]);
Route::get('/oauth/callback', [
    'as' => 'oauth.callback', 'uses' => 'Auth\AuthController@handleProviderCallback'
]);
Route::post('/vote', [
    'as' => 'vote', 'uses' => 'HomeController@vote'
]);
Route::controller('filemanager', 'FilemanagerLaravelController');

Route::get('/testplayer', [
    'as' => 'testplayer', 'uses' => 'HomeController@player'
]);

Route::get('/share_facebook', 'HomeController@shareFacebook');
Route::get('/testplayer', 'HomeController@testplayer');
/*Route::get('/status/{token}', [
    'as' => 'status', 'uses' => 'HomeController@status']);*/
Route::get('/status2/{token}', [
    'as' => 'status2', 'uses' => 'HomeController@status2']);
Route::get('/status3', [
    'as' => 'status3', 'uses' => 'HomeController@status3']);

Route::get('/status_webm/{token}', [
    'as' => 'status_webm', 'uses' => 'HomeController@status_webm']);

Route::get('/faq', [
    'as' => 'faq', 'uses' => 'HomeController@faq'
]);
Route::get('/dmca', [
    'as' => 'dmca', 'uses' => 'HomeController@dmca'
]);

Route::post('/filter', 'HomeController@filter');
Route::get('/testq', 'HomeController@testQuery');

Route::get('/images', [
            'as' => 'image', 'uses' => 'ImageController@index']);
Route::get('/image', [
            'as' => 'image', 'uses' => 'ImageController@index']);
Route::get('/filterImage', [
            'as' => 'filterImage', 'uses' => 'ImageController@filterImage']);

Route::post('/likeimage', [
            'as' => 'likeimage', 'uses' => 'ImageController@like']);

//API
Route::post('/api/checkVideo', 'ApiController@checkVideoStatus');
Route::get('/api/getKey/{id}', [
    'as' => 'getkey', 'uses' => 'ApiController@getKey'
]);
Route::get('/api/listmap', 'ApiController@getListMapzip');
Route::get('/api/uploadvideo', 'ApiController@getUploadVideo');
Route::get('/api/uploadlog', 'ApiController@getUploadLog');
Route::get('/api/uploadClient', 'ApiController@getUploadClient');
Route::post('/api/clientVersion', 'ApiController@getClientVersion');
Route::post('/api/uploadfile', 'ApiController@postUploadFile');
Route::post('/api/uploadvideo', 'ApiController@uploadHLS');
Route::post('/api/uploadlog', 'ApiController@uploadLog');
Route::get('/list_log', [
    'as' => 'list_log', 'uses' => 'ApiController@logs'
]);
Route::post('/api/uploadClient', 'ApiController@postUploadClient');
// Route::post('/api/sendEmail', 'ApiController@sendEmail');

Route::post('/api/uploadClient', [
    'as' => 'postUploadClient', 'uses' => 'ApiController@postUploadClient'
]);
Route::get('/api/uploadClient2', 'ApiController@getUploadClient2');
Route::post('/api/uploadClient2', [
    'as' => 'postUploadClient2', 'uses' => 'ApiController@postUploadClient2'
]);
//Route::post('/api/chat', 'ApiController@chatChannel');
Route::post('/api/followings', 'ApiController@followings');
Route::post('/api/getLoginInfo', 'ApiController@getLoginInfo');
//Route::post('/api/chat', 'ApiController@chatChannel');
Route::post('/api/followings', 'ApiController@followings');
Route::post('/check_noitify_transcoder', 'ApiController@check_noitify_transcoder');
Route::post('/api/addBotLog', [
    'as' => 'addBotLog', 'uses' => 'ApiController@addBotLog'
]);
Route::post('/api/updateVideoViewAndLike', [
    'as' => 'updateVideoViewAndLike', 'uses' => 'ApiController@updateVideoViewAndLike'
]);

Route::post('/api/uploadImage', 'ApiController@uploadImage');
Route::get('/api/uploadClient3', 'ApiController@getUploadClient3');

Route::get('/test/play360', [
    'as' => 'testplay360', 'uses' => 'HomeController@play360'
]);

Route::get('/event', [
    'as' => 'event', 'uses' => 'EventController@showEvent'
]);

Route::post('/event/map', [
    'uses' => 'EventController@checkMapChange'
]);

Route::post('/event/state', [
    'uses' => 'EventController@checkEventState'
]);

Route::post('/event/like', [
    'uses' => 'EventController@likeEvent'
]);
Route::post('/event/view', [
    'uses' => 'EventController@incView'
]);

Route::get('/esea/set/gamestatus/{status}', 'ApiController@setGameStatus');
Route::get('/esea/gamestatus', 'ApiController@gamestatus');
Route::get('/esea/vivemap', 'ApiController@vivemap');
Route::get('/esea/mobilemap', 'ApiController@mobilemap');

Route::post('/api/trending', ['uses' => 'ApiController@getCarouselTrendingVideo']);
Route::get('/api/trending/current', ['uses' => 'ApiController@getTrendingVideoAndCurrentPlay']);

Route::get('/download', [
    'as' => 'download', 'uses' => 'HomeController@showDownload'
]);

Route::get('/api/eventGameInfo', 'ApiController@getGameInfo');

Route::get('/api/eventComingsoonInfo', 'ApiController@getEventComingsoonInfo');

Route::get('/api/eventVod', 'ApiController@getEventVod');

Route::get('/api/eventSetOfVodUrl', 'ApiController@getEventSetOfVodUrl');

Route::get('/api/eventSetOfLiveUrl', 'ApiController@getEventSetOfVodUrl');

Route::post('/api/updateSessionStreamer', ['uses' => 'ApiController@updateSessionStreamer']);

Route::get('/api/next-event-date', 'ApiController@getNextEventDate');

Route::post('/api/streamerLiveStart', 'ApiController@streamerLiveStart');

Route::post('/api/streamerLiveStop', 'ApiController@streamerLiveStop');

Route::get('/api/checkChannelModerator/{channel}', [
    'as' => 'checkChannelModerator', 'uses' => 'ApiController@checkChannelModerator'
]);
    
Route::get('/api/getChannelModerator/{channel}', [
        'as' => 'getChannelModerator', 'uses' => 'ApiController@getChannelModerator'
]);

Route::post('/api/getSponsorshipVideoInfo', [
    'as' => 'getSponsorshipVideoInfo', 'uses' => 'ApiController@getSponsorshipVideoInfo'
]);
    
Route::post('/api/postTwitchMessage', 'ApiController@postTwitchMessage');

Route::get('/boom-meter', [
    'as' => 'boom_meter', 'uses' => 'BoomMeterUserController@boomMeter'
]);

Route::get('/d/skins', [
    'as' => 'skins', 'uses' => 'BoomMeterUserController@boomMeter'
]);

Route::get('/boom-meter/{action}/{boom_meter_id}', [
    'as' => 'action_boom_meter', 'uses' => 'BoomMeterUserController@actionBoomMeter'
]);

Route::get('/status/{token}', [
    'as' => 'status_boom_meter', 'uses' => 'BoomMeterUserController@status']);
/*Route::get('/status/{token}', [
    'as' => 'status', 'uses' => 'HomeController@status']);*/

Route::get('/custom-image-boom-meter', [
    'as' => 'custom_boom_meter', 'uses' => 'BoomMeterUserController@getUploadImage'
]);
Route::post('/upload-image-boom-meter', [
    'as' => 'upload_image_boom_meter', 'uses' => 'BoomMeterUserController@postUploadImage'
]);
Route::post('/upload-css-boom-meter', [
    'as' => 'upload_css_boom_meter', 'uses' => 'BoomMeterUserController@uploadCss'
]);

Route::get('/review-boom-meter', [
    'as' => 'review_boom_meter', 'uses' => 'BoomMeterUserController@review'
]);

Route::get('/d/skins/demo/{id}', [
    'as' => 'demo_boom_meter', 'uses' => 'BoomMeterUserController@demoBoomMeter'
]);

Route::post('/video/remove/{video}', [
    'middleware' => 'auth',
    'uses' => 'VideoController@remove_video'
]);

Route::get('/user/videos', [
    'as' => 'uservideos',
    'uses' => 'VideoController@my_video'
]);

Route::get('/newsletter/unsubscribe', [
    'as' => 'unsubscribe',
    'uses' => 'UserController@unsubscribeEmail'
]);

Route::get('/newsletter/link/{code}',function ($code){
    return \App\Models\UnsubscriberEmail::getUnsubscriberEmailLink($code);
});

Route::get('/vrbeta',[
    'as' => 'vrbeta' , 'uses' => 'HomeController@getVrbeta'
]);

Route::post('/vrbeta',[
    'uses' => 'HomeController@postVrbeta'
]);

/*Route::get('/discordapp/index', [
    'as' => 'discordapp.index', 'uses' => 'DiscordController@showLogin'
]);*/

Route::get('/discordapp/oauth', [
    'as' => 'discordapp.login', 'uses' => 'DiscordController@loginToDiscord'
]);

Route::get('/discordapp/handler', [
    'as' => 'discordapp.handler', 'uses' => 'DiscordController@redirectUriHanler'
]);


Route::post('api/getDiscordInfo',['uses'=>'ApiController@getDiscordInfo']);

Route::get('api/startFollowStreamer',['uses'=>'ApiController@startFollowStreamer']);

Route::get('api/stopFollowStreamer',['uses'=>'ApiController@stopFollowStreamer']);

Route::get('api/getStateOfStreamer',['uses'=>'ApiController@getStateOfStreamer']);

//Route::get('api/testSendSparkpostMail', ['uses' => 'ApiController@testSendSparkpostMail']);


//profile must end of files

Route::post('/api/uninstallInfo', [
    'as' => 'uninstallInfo', 'uses' => 'ApiController@uninstallInfo'
]);


Route::post('/myprofile', [
    'middleware' => 'auth',
    'uses' => 'UserController@saveProfile'
]);

Route::get('/profile/{name}', [
    'as' => 'v2-profile',
    'uses' => 'UserController@showProfile'
])->where('name','^([a-zA-Z0-9_-]+)$');

Route::get('/{name}', [
    'as' => 'profile',
    'uses' => 'UserController@showProfile'
])->where('name','^([a-zA-Z0-9_-]+)$');

Route::get('/{name}/{subscribe}', [
    'as' => 'profile.subscribe',
    'uses' => 'UserController@showProfile'
])->where('name','^([a-zA-Z0-9_-]+)$');

//profile must be palaced end of files
Route::post('/update-session-boom-meter', [
    'as' => 'update_session_boom_meter', 'uses' => 'BoomMeterUserController@updateSessionBoomMeter'
]);
Route::post('/api/refreshToken', [
    'as' => 'refreshToken', 'uses' => 'ApiController@refreshToken'
]);
Route::post('/api/removeConnections', [
    'as' => 'removeConnections', 'uses' => 'ApiController@removeConnections'
]);
Route::post('/api/sparkpostEventProcesss', [
    'uses' => 'ApiController@sparkpostEventProcesss'
]);
Route::post('/api/checkUploadSpeed', [
    'as' => 'checkUploadSpeed', 'uses' => 'ApiController@checkUploadSpeed'
]);
Route::get('/api/getVideosNewest/{username}', [
    'as' => 'getVideosNewest', 'uses' => 'ApiController@getVideosNewest'
]);
Route::get('api/getYoutubeToken/{channel_id}',['uses'=>'ApiController@getYoutubeToken']);