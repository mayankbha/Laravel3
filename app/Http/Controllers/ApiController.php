<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use ClassPreloader\Config;
use Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Exception;
use Log;
use App\Http\Requests;
use App;
use Swagger\Annotations as SWG;
use App\Models\User;
use App\Models\Token;
use App\Models\Video;
use App\Models\SessionStreamer;

use TwitchApi;
use Auth;
use Hash;
use App\Helpers\AWSHelper;
use App\Helpers\Helper;
use App\Helpers\MixerHelper;
use App\Helpers\TwitchHelper;
use App\Helpers\DiscordHelper;
use App\Models\SocialAccount;
use File;

use App\Models\Game;
use App\Models\VideoGame;
use App\Models\BotLog;
use App\Models\Image;
use App\Models\ImageChannel;
use App\Models\UserAppVersion;
use Mail;
use App\Models\Setting;
use App\Models\LiveStream;
use App\Traits\LiveStreamTrackingJobTrait;
use Lang;
use DB;
use GeoIP;
use App\Models\EventVod;
use App\Models\EventVodLiveUrl;
use App\Models\Sponsorship;
use App\Models\DiscordInfo;
use App\Models\SocialConnected;
use App\Models\UninstallInfo;
use Redis;
use GuzzleHttp;
use App\Models\UserReminderMailLog;

class ApiController extends Controller
{

    // public function sendEmail(Request $request){
    // 	try {
    // 		$email_input=$request->input('email');
    // 		$emails=explode(" ", $email_input);
    // 		foreach ($emails as $key => $value) {
    // 			$mail=$value;
    // 			Mail::later(18000,'emails.invite', [], function ($message) use ($mail){

    // 			    $message->from('contact.nphweb@gmail.com', 'NPHweb Contact');
    // 	          	$message->to($mail)->subject('Inviting Tester');
    // 			});
    // 		}
    // 		return json_encode(["code"=>0,"status"=>"Success"]);
    // 	} catch (\Exception $e) {

    // 		return json_encode(["code"=>1,"status"=>"Fail"]);
    // 	}


    //}
    use LiveStreamTrackingJobTrait;

    public function checkVideoStatus(Request $request)
    {
        $vcode = $request->input("vcode");
        if ($vcode != "") {
            $video = Video::where('code', $vcode)->first();
            if ($video != null) {
                $job_id = $video->job_id;
                if ($job_id == "")
                    return json_encode([
                        'status' => 1,
                        'message' => 'Video ready'

                    ]);

                $job_status = AWSHelper::checkJobStatus($job_id);
                if ($job_status == "Complete") {
                    Video::where('code', $vcode)->update(["job_status" => $job_status]);
                    return json_encode([
                        'status' => 1,
                        'amz_status' => $job_status

                    ]);
                } else
                    return json_encode([
                        'status' => 0,
                        'amz_status' => $job_status

                    ]);
            }
            return json_encode([
                'status' => 0,
                'error' => 'Video not exits'

            ]);
        } else
            return json_encode([
                'status' => 0,
                'error' => 'Vcode cannot be null'

            ]);


    }

    public function getKey(Request $request, $id)
    {
        $videoId = $request->input('video_id');
        $key = "AQEDAHh27I7ep8UA8h4ZkT7gjFnS50KkxJDdrviotvprqVDP9AAAAG4wbAYJKoZIhvcNAQcGoF8wXQIBADBYBgkqhkiG9w0BBwEwHgYJYIZIAWUDBAEuMBEEDKLj3Ax5fx9IpE53qgIBEIAr/nf6JghiUkJb0AyYDHXOk5VLmYxDgbuRvQLfksPJqgYT3wjNQIz2Kd/88A==";
        //$result = base64_encode(AWSHelper::decryptKey($key));
        $result = AWSHelper::decryptKey($key);
        $arr = array();
        $arr['status'] = 0;
        $arr['plaintext'] = base64_encode($result);
        if ($id == 2) {
            return "ajdkchdjakdgsnfk";
        }
        return $result;
    }

    /**
     * @SWG\Post(path="/api/uploadvideo",
     *   tags={"api"},
     *   summary="upload video",
     *   description="Return status. If success, return status = 0 and link. If fail, return status <> 0 and error",
     *   operationId="postUploadVideo",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="file",
     *     description="upload video",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="title",
     *     description="upload video",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="token",
     *     description="token",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="game",
     *     description="game name",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="type",
     *     description="type game",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="datetime",
     *     description="datetime record",
     *     required=false,
     *     type="datetime"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="views",
     *     description="views number of replay",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="likes",
     *     description="likes number of replay",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function uploadHLS(Request $request)
    {

        $game = $request->input('game');
        $type = $request->input('type');
        $datetime = $request->input('datetime');
        $title = htmlentities($request->input('title'));
        $token = $request->input('token');
        $views = Helper::returnInteger($request->input('views'));
        $likes = Helper::returnInteger($request->input('likes'));
        $requestedBy = $request->input('requested_by');
        $createFromMontageServer = $request->input('create_montage');
        $sessionId = $request->input('session_id');
        Log::info("session id" . $sessionId);
        if ($createFromMontageServer != 1) {
            $createFromMontageServer = 0;
        }
        $error = "";
        $validate = $this->checkVideoInput($token, $title, $datetime, $type, $game);
        if ($validate["status"] == 0) {

            $datetime = date('Y-m-d H:i:s', $datetime);
            $gameIds = Game::createOrUpdate($game);

            $userToken = Token::where("token", $token)->first();

            if ($userToken != null) {
                if ($request->hasFile('file')) {
                    $file = $request->file("file");
                    if ($file->isValid()) {
                        //validate input
                        $error = Helper::validateFile($file);
                        if ($error != "") {
                            return response()->json(array("status" => 4, "error" => $error));
                        }
                        $videoName = preg_replace("/[\'^£$%&*()}{@#~?><>,\/|=_+¬\s+]/u ", '_', $file->getClientOriginalName());
                        $realPath = $file->getRealPath();


                        try {
                            $path = storage_path('upload');
                            if (!is_dir($path)) {
                                mkdir($path);
                            }
                            $destinationPath = storage_path('upload/' . $userToken->user_id);
                            if (!is_dir($destinationPath)) {
                                mkdir($destinationPath);
                            }

                            $userId = $userToken->user_id;
                            $inputToVideoFolder = config("aws.s3-input-folder") . "/" . $userId . "/";

                            $linkInfo = AWSHelper::uploadToS3($realPath, $inputToVideoFolder, $videoName);
                            $links3 = $linkInfo["links3"];
                            $s3Name = $linkInfo["s3Name"];
                            $linkInfo["userId"] = $userId;

                            $convertInfo = $this->convertHls($type, $linkInfo, $createFromMontageServer);
                            $job_id = $convertInfo['job_id'];
                            $linkHls = $convertInfo['linkHls'];
                            $hlsType = $convertInfo['hlsType'];

                            $link = route('playvideo');
                            if (env('APP_ENV') == "upload_server") {
                                $link = preg_replace('/https:\/\/(.*?)boom\.tv/', 'https://boom.tv', $link);
                            }
                            // get thumnail from video
                            $file->move($destinationPath, $videoName);
                            $videoFile = $destinationPath . "/" . $videoName;
                            $thumbName = pathinfo($videoName, PATHINFO_FILENAME) . ".jpg";
                            $thumbVideo = $destinationPath . "/" . $thumbName;
                            Helper::createVideoThumbnail($videoFile, $thumbVideo, '1280x720', 3);
                            $inputToThumbFolder = config("aws.s3-thumb-folder") . "/" . $userId . "/";

                            $thumbnail = AWSHelper::uploadToS3($thumbVideo, $inputToThumbFolder, $thumbName)["links3"];

                            $video = Video::create(array(
                                "name" => $videoName,
                                "user_id" => $userToken->user_id,
                                "title" => $title,
                                "view_numb" => $views,
                                "like_numb" => $likes,
                                "link" => $link,
                                "links3" => $links3,
                                "thumbnail" => $thumbnail,
                                "type" => $type,
                                "datetime" => $datetime,
                                "job_id" => $job_id,
                                "hls_type" => $hlsType,
                                "link_hls" => $linkHls,
                                "requested_by" => $requestedBy,
                                "session_id" => $sessionId
                            ));
                            // videos belong to multi games
                            VideoGame::createRelations($gameIds, $video->id);
                            Video::updateViewById($video->id, $views, true);
                            $code = crypt($video->id . date("h") . date("i") . date("s"), '$1$');
                            //$random = rand(0,9);
                            $random = Helper::randomKey(1);
                            $code = str_replace([
                                "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "=", "\\", ".", ",", "/", "<", ">", "?", "+", "_", "[", "]", "{", "}", ";", ":"
                            ], $random, $code);

                            $video->code = $code;

                            $video->link = $link . "?v=" . $video->code;
                            // with boom.tv open, we don't need to replace server anymore
                            //$video->link=str_replace("beta.boom.tv", "boom.tv", $video->link);
                            unlink($thumbVideo);
                            unlink($videoFile);
                            $modeHls = config("video.hlsMode");
                            if ($modeHls) {
                                $i = 5;
                                do {
                                    sleep(3);
                                    Log::info("Check job status for id: " . $job_id);
                                    $job_status = AWSHelper::checkJobStatus($job_id);
                                    $i--;
                                } while ($job_status != "Error" && $job_status != "Complete" && $i > 0);
                                if ($job_status == "Complete") {
                                    $video->save();
                                    return response()->json(array("status" => 0, "link" => $video->link, "vcode" => $code));
                                } else {
                                    if ($job_status == "Error") {
                                        return response()->json(array("status" => 5, "error" => "Generate video fail on Amazon transcoder"));
                                    } else {
                                        // we out of trying not because the job is completed
                                        $video->save();
                                        return response()->json(array("status" => 0, "link" => $video->link, "vcode" => $code));
                                    }
                                }
                            } else {
                                $video->save();
                                Video::sendMailForVideoMontage($video->id);
                                return response()->json(array("status" => 0, "link" => $video->link, "vcode" => $code));
                            }
                        } catch (\Exception $e) {
                            Log::error("Upload file error by user : " . $userToken->user_id
                                . " filename : " . $file->getClientOriginalName() . "\n" . $e);
                            $status = 4;
                            $error = "Upload s3 fail";
                        }
                    } else {
                        $status = 3;
                        $error = "Verify fail";
                    }
                } else {
                    $status = 2;
                    $error = "The file is not present on the request";
                }
            } else {
                $status = 1;
                $error = "User do not exist";
            }
            return response()->json(array("status" => $status, "error" => $error));
        }

        return response()->json($validate);
    }

    public function convertHls($type, $linkInfo, $createFromMontageServer = 0)
    {
        $job_id = "";
        $linkHls = "";
        $hlsType = 1;
        $modeHls = config("video.hlsMode");
        $hlsFolder = config("aws.prefix_output_video") . $linkInfo["userId"]
            . "/" . $linkInfo["name"] . "/";

        $linkInfo["outputHlsFolder"] = $hlsFolder;
        $linkInfo["createFromMontageServer"] = $createFromMontageServer;
        if ($modeHls) {
            if ($type == 2) {
                $job_id = AWSHelper::convertHLS_360($linkInfo);
            } else {
                $job_id = AWSHelper::convertHLS_3D($linkInfo);
            }
            $hlsType = 3;
            $linkHls = "/" . $hlsFolder . $linkInfo['s3Name'] . '.m3u8';
        }
        return ["job_id" => $job_id, "linkHls" => $linkHls, "hlsType" => $hlsType];
    }

    public function convertUploadFile(Request $request)
    {
        if ($request->hasFile("file")) {
            $file = $request->file("file");
            if ($file->isValid()) {
                $fileName = $file->getClientOriginalName();
                $realPath = $file->getRealPath();

                try {

                    return AWSHelper::convertToHLS($realPath, $fileName, 1);

                } catch (Exception $e) {
                    Log::error($e);
                    return response()->json(array("status" => 3, "error" => "upload s3 fail"));
                }
            } else {
                return response()->json(array("status" => 2, "error" => "verify fail"));
            }
        } else {
            return response()->json(array("status" => 1, "error" => "the file is not present on the request"));
        }

    }

    public function getListMapzip(Request $request)
    {
        $client = App::make('aws')->createClient('s3');
        $bucket = config('aws.bucket_upload_video');
        $result = $client->listObjects([
            'Bucket' => $bucket, // REQUIRED
            'Prefix' => 'mapzip/',
        ]);
        $data = [];
        foreach ($result['Contents'] as $key => $value) {
            if ($value['Size'] > 0) {
                $data[$key]['link'] = config('aws.cloudfront') . '/' . $value['Key'];
                $data[$key]['name'] = str_replace('mapzip/', '', $value['Key']);
            }
        }
        $info = "Mapzip";
        return view('upload.list_mapzip', ['info' => $info, 'data' => $data]);

    }

    public function getUploadVideo()
    {
        $user = Auth::user();
        $token = "";
        if (Auth::check()) {
            $userToken = Token::where("user_id", $user->id)->first();
            $token = $userToken->token;
        }
        return view('upload.upload_file', ["token" => $token]);
    }

    public function getUploadLog()
    {
        return view('upload.upload_log');
    }

    /**
     * @SWG\Post(path="/api/uploadlog",
     *   tags={"api"},
     *   summary="upload log",
     *   description="Return status. If success, return status = 0 and message. If fail, return status <> 0 and message",
     *   operationId="uploadLog",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="file",
     *     description="upload file",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="description",
     *     description="description string",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="token",
     *     description="token user",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function uploadLog(Request $request)
    {
        $issue = $request->input('description');
        $token = $request->input('token');
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->isValid()) {
                $filename = preg_replace("/[\'^£$%&*()}{@#~?><>,\/|=_+¬\s+]/u ", '_', $file->getClientOriginalName());
                $realPath = $file->getRealPath();
                $bucket = config("aws.bucket_logs");
                $folder = strtolower(config("aws.folder_log")) . "/";
                $linkInfo = AWSHelper::uploadToS3($realPath, $folder, $filename, $bucket);
                $emailFrom = config("mail.mailFrom.sender");
                //$emailTo = config("mail.sendMailLog.sendTo");
                //$subject = config("mail.sendMailLog.subject");
                $info = array();
                $info["temp"] = "emails.send_log";
                $info["filename"] = $linkInfo["s3Name"];
                $info["link"] = config("aws.links3_log") . $linkInfo["links3"];
                $info["linkAll"] = route('list_log');
                $info["sender"] = config("mail.mailFrom.sendername");
                $info["issue"] = $issue;
                //Helper::sendMail($emailFrom, $emailTo, $subject, $info);

                $emailTo = config("mail.sendMailJira.sendTo");
                $emailCc = config("mail.sendMailJira.emailCc");
                $subject = config("mail.sendMailJira.subject");
                $temp_filename = explode('_', $filename);
                $client_name = isset($temp_filename[0]) ? $temp_filename[0] : "Unknown";

                $emailAddress = "";
                $numbFollow = -1;
                if (isset($token) && $token != "") {
                    $token = Token::where("token", $token)->first();
                    if ($token != null) {
                        $user = User::find($token->user_id);
                        if ($user != null && $user->email != "") {
                            $emailAddress = $user->email;
                        }
                        $social = SocialAccount::where("user_id", $token->user_id)->first();
                        if ($social != null) {
                            $numbFollow = $social->follower_numb;
                        }
                    }
                } else {
                    if ($client_name != "Unknown") {
                        $user = User::where("name", $client_name)->first();
                        if ($user != null && $user->email != "") {
                            $emailAddress = $user->email;
                        }
                    }
                }
                if ($emailAddress == "") {
                    $emailAddress = "Not found";
                }
                if ($numbFollow >= 0) {
                    $subject = $subject . $client_name . " - Follower number: " . $numbFollow;
                } else {
                    $subject = $subject . $client_name;
                }

                $info["emailAddress"] = $emailAddress;

                Helper::sendMailCc($emailFrom, $emailTo, $emailCc, $subject, $info);
                $result['status'] = 0;
                $result['message'] = "Upload Log Success";
            } else {
                $result['status'] = 1;
                $result['message'] = "Upload Error: " . $file->getErrorMessage();
            }
        } else {
            $result['status'] = 1;
            $result['message'] = "File not found or too large";
        }

        return response()->json($result);

    }

    public function logs(Request $request)
    {
        $max = $request->input('max');
        $folder = strtolower(config("aws.folder_log"));
        $bucket = config("aws.bucket_logs");
        $link = config("aws.links3_log");
        $range = config("aws.padding_log");
        $max = $max + $range;
        $data = AWSHelper::getFilesInFolderS3($folder, $bucket, $link, $max);
        $info = "Logs";
        return view('upload.list_log', ['info' => $info, 'data' => $data, 'max' => $max]);
    }
    /*public function uploadLog(Request $request){
        if($request->hasFile('file'))
        {
            $file=$request->file('file');
            if($file->isValid())
            {
                $path=public_path('/filemanager/userfiles/logs');
                $fileName=$request->file('file')->getClientOriginalName();
                $request->file('file')->move($path, $fileName);

                $result['status']=0;
                $result['message']="Upload Log Success";


            }
            else
            {
                $result['status']=1;
                $result['message']="Upload Error: ".$file->getErrorMessage();
            }
        }
        else{
            $result['status']=1;
            $result['message']="File not found or too large";
        }

        return response()->json($result);

    }*/
    /**
     * @SWG\Post(path="/api/uploadfile",
     *   tags={"api"},
     *   summary="upload file",
     *   description="Return status. If success, return status = 0 and link. If fail, return status <> 0 and error",
     *   operationId="postUploadFile",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="file",
     *     description="upload file",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function postUploadFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file("file");
            if ($file->isValid()) {
                $fileName = $file->getClientOriginalName();
                $realPath = $file->getRealPath();

                try {
                    $link = AWSHelper::uploadToS3($realPath, "", $fileName);
                    $link = AWSHelper::getLinkPlay($link);
                    return response()->json(array("status" => 0, "link" => $link));
                } catch (Exception $e) {
                    Log::error($e);
                    return response()->json(array("status" => 3, "error" => "upload s3 fail"));
                }
            } else {
                return response()->json(array("status" => 2, "error" => "verify fail"));
            }
        } else {
            return response()->json(array("status" => 1, "error" => "the file is not present on the request"));
        }

    }


    public function checkVideoInput($token, $title, $datetime, $type, $game)
    {

        $error = "";
        $status = 0;
        $list = array("game" => $game, "datetime" => $datetime,
            "type" => $type, "token" => $token
        );
        foreach ($list as $key => $value) {
            if ($value == "" || $value == null) {
                $status = 1;
                $error .= "Missing " . $key . ".";
            }
        }

        $temp = in_array($type, ["0", "1", "2", "3"]);
        if ($temp == false) {
            $status = 1;
            $error .= "Invaild type";
        }

        if (!Helper::is_timestamp($datetime)) {
            $status = 1;
            $error .= "Invaild datetime";
        }
        return ["status" => $status, "error" => $error];

    }
    /*public function uploadVideo(Request $request)
    {
        Log::info($request);
        $game=$request->input('game');
        $type=$request->input('type');
        $datetime=$request->input('datetime');
        $title = $request->input('title');
        $token = $request->input('token');
        Log::info($token."-".$title."-".$datetime."-".$type."-".$game);

        $error="";
        $validate = $this->checkVideoInput($token,$title,$datetime,$type,$game);
        if($validate["status"]==0)
        {

            $datetime=date('Y-m-d H:i:s',$datetime);
            $temp=Game::getGamebyAlias($game);
            if(!$temp)
            {
                $new_game=new Game();
                $new_game->name=$game;
                $new_game->alias=Game::getAlias($game);
                $new_game->save();
                $game=$new_game->id;
            }
            else
                $game=$temp->id;

            $userToken = Token::where("token", $token)->first();

            if($userToken != null)
            {
                if ($request->hasFile('file')) {
                    $file = $request->file("file");
                    if ($file->isValid()) {
                        //validate input
                        $error = Helper::validateFile($file);
                        if($error != "")
                        {
                            return response()->json(array("status" => 4, "error" => $error));
                        }
                        $videoName = preg_replace("/[\'^£$%&*()}{@#~?><>,\/|=_+¬\s+]/u",'_',
                        $file->getClientOriginalName());
                        $realPath = $file->getRealPath();
                        try
                        {
                            $path = storage_path('upload');
                            if (!is_dir($path)) {
                                mkdir($path);
                            }
                            $destinationPath = storage_path('upload/' . $userToken->user_id);
                            if (!is_dir($destinationPath)) {
                                mkdir($destinationPath);
                            }
                            $links3=AWSHelper::uploadToS3($realPath, "videos/", $videoName);

                            $link = route('playvideo');
                            // get thumnail from video
                            $file->move($destinationPath, $videoName);
                            $videoFile = $destinationPath ."/". $videoName;
                            $thumbName = pathinfo($videoName,PATHINFO_FILENAME) . ".jpg";
                            $thumbVideo = $destinationPath ."/". $thumbName;
                            Helper::createVideoThumbnail($videoFile, $thumbVideo, '640x360', 0);
                            $thumbnail = AWSHelper::uploadToS3($thumbVideo, "thumb/", $thumbName);

                            $video = Video::create(array(
                                                "name" => $videoName,
                                                "user_id" => $userToken->user_id,
                                                "title" => $title,
                                                "view_numb" => 0,
                                                "link" => $link,
                                                "links3" => $links3,
                                                "thumbnail" => $thumbnail,
                                                "game_id"=>$game,
                                                "type"=>$type,
                                                "datetime"=>$datetime
                                                ));
                            $video->save();

                            $code= crypt($video->id.date("h").date("i").date("s"), 'rl');
                            $random = rand(0,9);
                            $code= str_replace([
                                "!","@","#","$","%","^","&","*","(",")","-","=","\\",".",",","/","<",">","?","+","_","[","]","{","}",";",":"
                                ], $random, $code);

                            $video->code = $code;

                            $video->link = $link . "?v=" . $video->code;
                            $video->link=str_replace("beta.boom.tv", "boom.tv", $video->link);
                            $video->save();
                            unlink($thumbVideo);
                            unlink($videoFile);
                            return response()->json(array("status" => 0, "link" =>$video->link));
                        }
                        catch(\Exception $e)
                        {
                            Log::error("Upload file error by user : " . $userToken->user_id
                            . " filename : " .$file->getClientOriginalName().  "\n" . $e);
                            $status = 4;
                            $error = "Upload s3 fail";
                        }
                    }
                    else
                    {
                        $status = 3;
                        $error = "Verify fail";
                    }
                }
                else
                {
                    $status = 2;
                    $error = "The file is not present on the request";
                }
            }
            else
            {
                $status = 1;
                $error = "User do not exist";
            }
        }

        return response()->json($validate);
    }
*/
    /**
     * @SWG\Post(path="/api/followings",
     *   tags={"api"},
     *   summary="Get list of who the user is following",
     *   description="Return status. If success, return status = 0 and channels list. If fail, return status <> 0 and error",
     *   operationId="followings",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="token",
     *     description="token",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function followings(Request $request)
    {
        $token = $request->input('token');
        $userToken = Token::where("token", $token)->first();
        if ($userToken != null) {
            $options = [
                'limit' => 20,
                'offset' => 0,
                'direction' => 'DESC',
                'sortby' => 'created_at',
            ];
            $followings = TwitchApi::followings($userToken->user->name, $options);
            $listChannel = [];
            $chanels = $followings['follows'];
            if (count($chanels) > 0) {
                foreach ($chanels as $c) {
                    array_push($listChannel, $c['channel']['name']);
                }
            }
            return response()->json(array("status" => 0, "listChannel" => $listChannel));

        } else {
            return response()->json(array("status" => 1, "error" => "user do not exist"));
        }
    }

    /**
     * @SWG\Post(path="/api/getLoginInfo",
     *   tags={"api"},
     *   summary="get login info to chat room",
     *   description="Return status. If success, return status = 0 and access_token and nick name. If fail, return status <> 0 and error",
     *   operationId="getLoginInfo",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="token",
     *     description="token",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="version",
     *     description="version",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getLoginInfo(Request $request)
    {

        $token = $request->input('token');
        $version = $request->input('version');
        if ($version == null) {
            $version = "old-version";
        }

        $check_token = Token::where("token", $token)->first();
        if ($check_token != null) {
            $user_ip = geoip()->getClientIP();
            $ip_key_cached = Lang::get('cached.ipSaveTimezone', ['ip' => $user_ip, 'user_id' => $check_token->user_id]);
            $content_ip = Cache::get($ip_key_cached);
            if (!$content_ip) {
                Cache::put($ip_key_cached, 1, 24 * 60);
                try {
                    $location = geoip()->getLocation();
                    $timezone = $location->timezone;
                } catch (\Exception $exception) {
                    $timezone = config("geoip.default_location")['timezone'];
                }

                $user = User::where('id', $check_token->user_id)->first();
                $user->timezone = $timezone;
                $user->save();
                User::flushLoginInfo($check_token->user_id);
                Log::info("[getLoginInfo] update user timezone {$timezone} {$user->id}");
            }

            $current_date = Carbon::now()->toDateString();
            if ($version == "old-version") {
                $user_app_version_obj = UserAppVersion::where('user_id', $check_token->user_id)->orderBy("created_at", 'desc')->first();
                if (!$user_app_version_obj) {
                    Log::info("[getLoginInfo] first update user app version {$check_token->user_id} Version: {$version}");
                    $user_app_version_obj = new UserAppVersion(['user_id' => $check_token->user_id, 'version' => $version]);
                    $user_app_version_obj->save();
                } else {
                }
            } else {
                $user_app_version_obj = UserAppVersion::where('user_id', $check_token->user_id)->where('version', '!=', 'old-version')->orderBy("created_at", 'desc')->first();
                if ($user_app_version_obj) {
                    if ($version != $user_app_version_obj->version) {
                        Log::info("[getLoginInfo] update new user app version {$check_token->user_id} Version: {$version} , {$user_app_version_obj->version}");
                        $user_app_version_obj = new UserAppVersion(['user_id' => $check_token->user_id, 'version' => $version]);
                        $user_app_version_obj->save();
                    } else {
                    }
                } else {
                    Log::info("[getLoginInfo] first update user app version {$check_token->user_id} Version: {$version}");
                    $user_app_version_obj = new UserAppVersion(['user_id' => $check_token->user_id, 'version' => $version]);
                    $user_app_version_obj->save();
                }
            }
            $key_cached = Lang::get('cached.apiGetLoginInfo', ['id' => $check_token->user_id]);
            $res_content = Cache::get($key_cached);
            if ($res_content != null) {
                //Log::info('api/getLoginInfo cached');
                return $res_content;
            }

            $res_content = User::apiGetLoginInfo($check_token->user_id);
            //Log::info('api/getLoginInfo normal');
            Cache::put($key_cached, $res_content, 24 * 60);
            return $res_content;
        } else {
            return response()->json(array("status" => 1, "error" => "user do not exist"));
        }
    }


    /**
     * @SWG\Post(path="/api/client_version",
     *   tags={"api"},
     *   summary="get client version",
     *   description="Return status = 0 if current version and link;status = 1 if not found client file",
     *   operationId="getClientVersion",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getClientVersion(Request $request)
    {

        $fileClient = public_path("/client/version.txt");
        $current = "";
        if (File::exists($fileClient)) {
            $current = File::get($fileClient);
        }
        $info = explode("*****", $current);
        if (count($info) > 1) {
            return response()->json(array("status" => 0, "ver" => $info[0], "link" => $info[1]));
        } else {
            return response()->json(array("status" => 1, "error" => "not found client file"));
        }

    }

    public function getUploadClient()
    {
        $data = AWSHelper::getS3Details();
        return view('upload.upload_client', ["s3FormDetails" => $data]);
    }

    /**
     * @SWG\Post(path="/api/uploadClient",
     *   tags={"api"},
     *   summary="upload client",
     *   description="Return status. If success, return status = 0 and message. If fail, return status <> 0 and message",
     *   operationId="postUploadClient",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="file",
     *     description="upload file",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function postUploadClient(Request $request)
    {
        $version = $request->input('version');
        $key = $request->input('upload_original_name');
        if ($version != "") {
            $path = public_path("/client");
            if (!is_dir($path)) {
                mkdir($path);
            }
            File::put($path . "/version.txt", $version . "*****" . $key);
            $result['status'] = 0;
            $result['message'] = "Upload Success";
        } else {
            $result['status'] = 1;
            $result['message'] = "Upload Error";
        }
        return response()->json($result);
    }

    public function getUploadClient2()
    {
        $data = AWSHelper::getS3Details();
        $folderSetup = "setup";
        $folderServer = strtolower(config("aws.folder_client"));
        return view('upload.upload_client2',
            ["s3FormDetails" => $data, "folderSetup" => $folderSetup,
                "folderServer" => $folderServer]);
    }

    public function getUploadClient3()
    {
        $data = AWSHelper::getS3Details();
        $folderSetup = "setup";
        $folderServer = strtolower(config("aws.folder_client"));
        return view('upload.upload_client3',
            ["s3FormDetails" => $data, "folderSetup" => $folderSetup,
                "folderServer" => $folderServer]);
    }

    /**
     * @SWG\Post(path="/api/uploadClient2",
     *   tags={"api"},
     *   summary="upload client",
     *   description="Return status. If success, return status = 0 and message. If fail, return status <> 0 and message",
     *   operationId="postUploadClient",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="file",
     *     description="upload file",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function postUploadClient2(Request $request)
    {
        $version = $request->input('version');
        $key = $request->input('upload_original_name');
        $status = "";
        $message = "";
        if ($request->hasFile('update')) {
            $file = $request->file("update");
            if ($file->isValid()) {
                $fileName = $file->getClientOriginalName();
                $realPath = $file->getRealPath();

                try {
                    /*$path = public_path("download/");
                    if (!is_dir($path)) {
                        mkdir($path);
                    }
                    $file->move($path, $fileName);*/
                    //upload to s3
                    $folderSetup = "setup/";
                    $folderServer = strtolower(config("aws.folder_client"));
                    $bucket = config('aws.bucket_upload_video');
                    $info = AWSHelper::uploadToBucket($realPath, $folderSetup . $folderServer . "/", $fileName, $bucket);
                    $status = 0;
                    $message = "Success.";
                } catch (Exception $e) {
                    Log::info("upload client fail.");
                    Log::error($e);
                    $status = 1;
                    $message = "upload client fail.";
                }
            } else {
                $status = 2;
                $message = "verify fail.";
            }
        } else {
            $status = 3;
            $message = "the file is not present on the request.";
        }
        return response()->json(["status" => $status, "message" => $message]);
    }

    public function check_noitify_transcoder(Request $request)
    {
        Log::info("check notify transcoder start");
        $jobId = AWSHelper::getNotify();
        Log::info("[check_notify] {$jobId}");
        $video = Video::where("job_id", $jobId)->first();
        $videoId = "";
        if ($video != null) {
            $videoId = $video->id;
        }
        Log::info("[check_notify] {$videoId}");
        Video::sendMailForVideoMontage($videoId);
    }

    /**
     * @SWG\Post(path="/api/updateVideoViewAndLike",
     *   tags={"api"},
     *   summary="Update video view num and like num",
     *   description="Return status. If success, return status = 0. If fail, return status <> 0 and error",
     *   operationId="updateVideoViewAndLike",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="code",
     *     description="video code on boom",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="view",
     *     description="new number of view",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="like",
     *     description="new number of like",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function updateVideoViewAndLike(Request $request)
    {
        $result = array();
        if ($request->has('code') && $request->has('view') && $request->has('like')) {
            $code = $request->input('code');
            $video = Video::where("code", $code)->first();
            if ($video != null) {
                $video->like_numb = intval($request->input('like'));
                $video->save();
                Video::updateView($code, intval($request->input('view')), true);
                $result['status'] = 0;
            } else {
                $result['status'] = 2;
                $result['message'] = "Video code not existed!";
            }
        } else {
            $result['status'] = 1;
            $result['message'] = "Not enough data!";
        }
        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/api/addBotLog",
     *   tags={"api"},
     *   summary="get login info to chat room",
     *   description="Return status. If success, return status = 0. If fail, return status <> 0 and error",
     *   operationId="addBotLog",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="log",
     *     description="log string",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function addBotLog(Request $request)
    {
        $log = $request->input('log');
        if ($log != "") {
            $botlog = BotLog::create(array("log" => $log));
            $result['status'] = 0;
            $result['message'] = "Add botlog success";
        } else {
            $result['status'] = 1;
            $result['message'] = "Log string empty";
        }
        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/api/uploadImage",
     *   tags={"api"},
     *   summary="upload image",
     *   description="Return status. If success, return status = 0 and link. If fail, return status <> 0 and error",
     *   operationId="uploadImage",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="file",
     *     description="upload image file",
     *     required=true,
     *     type="file"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="description",
     *     description="description string",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="token",
     *     description="token",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="channel",
     *     description="channel choosing",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="channel_message",
     *     description="channel choosing",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function uploadImage(Request $request)
    {
        $description = $request->input('description');
        $token = $request->input('token');
        $channel = $request->input('channel');
        $userToken = Token::where("token", $token)->first();
        $channel_message = $request->input('channel_message');
        if ($channel_message == null) {
            $channel_message = "";
        }
        $status = 0;
        if ($userToken != null) {
            if ($request->hasFile('file')) {
                $file = $request->file("file");
                if ($file->isValid()) {
                    //validate input
                    $error = Helper::validateImageFile($file);
                    if ($error != "") {
                        return response()->json(array("status" => 4, "error" => $error));
                    }
                    $imageName = preg_replace("/[\'^£$%&*()}{@#~?><>,\/|=_+¬\s+]/u ", '_', $file->getClientOriginalName());
                    $realPath = $file->getRealPath();
                    try {
                        // upload to s3
                        $userId = $userToken->user_id;
                        $inputToVideoFolder = config("aws.folder_image") . "/" . $userId . "/";

                        $linkInfo = AWSHelper::uploadToS3($realPath, $inputToVideoFolder, $imageName);
                        $paths3 = $linkInfo["links3"];
                        //create row db
                        $channelObj = ImageChannel::createOrUpdate($channel);
                        $channelId = 0;
                        if ($channelObj != null) $channelId = $channelObj->id;
                        $image = Image::create(array(
                            "name" => $imageName,
                            "user_id" => $userToken->user_id,
                            "paths3" => $paths3,
                            "description" => $description,
                            "channel_id" => $channelId,
                        ));
                        $code = crypt($image->id . date("h") . date("i") . date("s"), 'rl');
                        $random = rand(0, 9);
                        $code = str_replace([
                            "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "=", "\\", ".", ",", "/", "<", ">", "?", "+", "_", "[", "]", "{", "}", ";", ":"
                        ], $random, $code);
                        $image->code = $code;
                        $image->save();
                        $link = route('image') . '?i=' . $code;
                        $this->chatTwitchWithBot($channel, $link, $channel_message);
                        return response()->json(array("status" => $status, "link" => $link));
                    } catch (\Exception $e) {
                        Log::error("Upload image file error by user : " . $userToken->user_id
                            . " filename : " . $file->getClientOriginalName() . "\n" . $e);
                        $status = 4;
                        $error = "Upload s3 fail";
                    }
                } else {
                    $status = 3;
                    $error = "Verify fail";
                }
            } else {
                $status = 2;
                $error = "The file is not present on the request";
            }
        } else {
            $status = 1;
            $error = "User do not exist";
        }
        return response()->json(array("status" => $status, "error" => $error));
    }

    public function chatTwitchWithBot($channel, $link, $channel_message)
    {
        $boomtv_user = User::where('name', 'boomtvmod')->first();
        $boomtv_social = SocialAccount::where("user_id", $boomtv_user->id)
            ->first();
        $access_token = $boomtv_social->access_token;
        $nickname = $boomtv_user->name;
        $sock = fsockopen('irc.twitch.tv', 6667);
        if ($sock) {
            fwrite($sock, "PASS oauth:" . $access_token . "\r\n");
            fwrite($sock, "NICK " . $nickname . "\r\n");
            fwrite($sock, "JOIN #" . $channel . "\r\n");
            fwrite($sock, "PRIVMSG #" . $channel . " : " . $channel_message . $link . "\r\n");
            Log::info("PRIVMSG #" . $channel . " : " . $channel_message . $link . "\r\n");
            Log::info("Upload image file: chat link success");
        } else {
            Log::error("Upload image file error: chat twich fiel");
        }
        fclose($sock);
    }

    /**
     * @SWG\Post(path="/api/postTwitchMessage",
     *   tags={"api"},
     *   summary="Post a message to twitch chat",
     *   description="Return status. If success, return status = 0 If fail, return status <> 0 and error",
     *   operationId="postTwitchMessage",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="token",
     *     description="token",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="channel",
     *     description="channel to post",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="message",
     *     description="the message to post",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function postTwitchMessage(Request $request)
    {
        $token = $request->input('token');
        $channel = $request->input('channel');
        $userToken = Token::where("token", $token)->first();
        $message = $request->input('message');
        if ($message == null) {
            $message = "";
        }
        $status = 0;
        if ($userToken != null) {
            TwitchHelper::postMessageToChannel($channel, $message);
            return response()->json(array("status" => $status, "message" => "OK"));
        } else {
            $status = 1;
            $error = "User do not exist!";
        }
        return response()->json(array("status" => $status, "error" => $error));
    }

    public function chatMixerWithBot($channel, $link, $channel_message)
    {
        $boomtvUser = User::where('name', 'boomtvmod')->
        where("type", User::USER_TYPE_MIXER)->first();
        $boomtvSocial = SocialAccount::where("user_id", $boomtvUser->id)
            ->first();
        $chat = MixerHelper::chatMixerWithBot($channel, $boomtvSocial, $channel_message . $link);
    }

    /**
     * @SWG\Get(path="/esea/gamestatus",
     *   tags={"api"},
     *   summary="gamestatus",
     *   description="Return status",
     *   operationId="gamestatus",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */

    public function gamestatus(Request $request)
    {
        if ($this->boom_setting->get('game_status')) {
            $game_status = $this->boom_setting->get('game_status')->value;
        } else {
            $game_status = 1000;
        }

        return $game_status;
    }

    /**
     * @SWG\Get(path="/api/next-event-date",
     *   tags={"api"},
     *   summary="get next event date",
     *   description="return next event date {'status'=>0,'msg'=>'success','    nextEventDate'=>'NextEventDate'}",
     *   operationId="next-event-date",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getNextEventDate(Request $request)
    {
        $next_event_date = $this->boom_setting->get('next_event_date');

        if ($next_event_date) {
            return response()->json(['status' => 0, 'msg' => 'success', 'nextEventDate' => $next_event_date->value]);
        } else {
            return response()->json(['status' => 1, 'msg' => 'error', 'nextEventDate' => '']);
        }
    }

    /**
     * @SWG\Get(path="/api/eventGameInfo",
     *   tags={"api"},
     *   summary="get event game info",
     *   description="return game info include: game_name & team_name",
     *   operationId="get event game info",
     *   produces={"application/json"},
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getGameInfo(Request $request)
    {
        return response()->json(['status' => 0, 'msg' => 'success',
            'data' => ['game_name' => $this->boom_setting->get('game_name')->value, 'team_name' => $this->boom_setting->get('team_name')->value]
        ]);
    }

    /**
     * @SWG\Post(path="/api/getDiscordInfo",
     *   tags={"api"},
     *   summary="get discord info",
     *   description="return discord full info of streamer",
     *   operationId="api/getDiscordInfo",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="query",
     *     name="token",
     *     description="token boom",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="query",
     *     name="name",
     *     description="twitch, mixer username",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="query",
     *     name="type",
     *     description="type account: twitch (default), mixer",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getDiscordInfo(Request $request)
    {
        $token = $request->input('token');
        $userToken = Token::where("token", $token)->first();
        $name = $request->input('name');
        $type = $request->input('type');
        $userId = "";

        if (!$userToken) {
            if ($name != null && $name != "") {
                $typeCode = User::USER_TYPE_TWITCH;
                if ($type == "mixer") {
                    $typeCode = User::USER_TYPE_MIXER;
                }
                if ($type == "youtube") {
                    $typeCode = User::USER_TYPE_YOUTUBE;
                }
                $user = User::where("type", $typeCode)
                    ->where("name", $name)->first();
                if (!$user) {
                    return response()->json(['status' => 1, 'msg' => 'User is not exist']);
                } else $userId = $user->id;
            } else {
                return response()->json(['status' => 1, 'msg' => 'User is not exist']);
            }
        } else {
            $userId = $userToken->user_id;
        }
        $discord_info = DiscordInfo::where('user_id', $userId)->first();
        if (!$discord_info) {
            return response()->json(['status' => 2, 'msg' => 'Streamer discord info is not found']);
        }
        $now = Carbon::now();
        $expire_in = Carbon::instance(new \DateTime($discord_info->expire_in));
        try {
            if ($expire_in < $now) {
                $client_id = config('services.discord.client_id');
                $secret_key = config('services.discord.client_secret');
                $provider = new \Discord\OAuth\Discord([
                    'clientId' => $client_id,
                    'clientSecret' => $secret_key,
                    'redirectUri' => "",
                    'scope' => 'bot identify guilds email'
                ]);
                $token_object = $provider->getAccessToken('refresh_token', ['refresh_token' => $discord_info->refresh_token]);
                $discord_info->access_token = $token_object->getToken();
                $discord_info->expire_in = Carbon::createFromTimestamp($token_object->getExpires())->toDateTimeString();
                $discord_info->save();
            }
            unset($discord_info->user_id);
            unset($discord_info->id);
            unset($discord_info->created_at);
            unset($discord_info->updated_at);
            if ($discord_info->replay_channel_id == null) {
                $idChannel = DiscordHelper::createChannel($discord_info->guild_id);
                if ($idChannel > 0) {
                    $discord_info->replay_channel_id = $idChannel;
                    $discord_info->save();
                }
            }
            return response()->json(['status' => 0, 'msg' => 'success', 'data' => $discord_info]);
        } catch (\Exception $exception) {
            Log::info($exception->getTraceAsString());
            return response()->json(['status' => 2, 'msg' => 'Unknown error']);
        }

    }

    /**
     * @SWG\Get(path="/api/eventVod",
     *   tags={"api"},
     *   summary="get event vod",
     *   description="return even vod ",
     *   operationId="get event vod",
     *   produces={"application/json"},
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getEventVod(Request $request)
    {
        $data = [];
        $list_vod = EventVod::all();
        $quality = $request->input('quality');
        if (!in_array($quality, ['high', 'mid', 'low'])) {
            $quality = "";
        }

        foreach ($list_vod as $item) {
            $temp_data = [];
            $temp_data['name'] = $item->name;
            $temp_data['team_name'] = $item->team_name;
            $temp_data['game_name'] = $item->game_name;
            $temp_data['map_name'] = $item->map_name;
            $temp_data['thumbnail'] = config("content.cloudfront_f") . $item->thumbnail;
            $temp_data['map_id'] = $item->map_id;
            $temp_data['date'] = Carbon::instance(new \DateTime($item->vod_date))->format("d M Y");
            $temp_data['jumbotron'] = Helper::event_stream_generate_quality($item->jumbotron_url, $quality);;
            $list_360_vod = explode(',', $item->vod_360_url);
            $temp_data['live_1'] = isset($list_360_vod[0]) ? Helper::event_stream_generate_quality(trim($list_360_vod[0]), $quality) : '';
            $temp_data['live_2'] = isset($list_360_vod[1]) ? Helper::event_stream_generate_quality(trim($list_360_vod[1]), $quality) : '';
            $temp_data['live_3'] = isset($list_360_vod[2]) ? Helper::event_stream_generate_quality(trim($list_360_vod[2]), $quality) : '';
            $temp_data['live_4'] = isset($list_360_vod[3]) ? Helper::event_stream_generate_quality(trim($list_360_vod[3]), $quality) : '';
            $temp_data['set_live'] = EventVodLiveUrl::getLiveUrlByVodId($item->id);
            $data[] = $temp_data;
        }
        return response()->json(['status' => 0, 'msg' => 'success',
            'data' => $data
        ]);
    }

    /**
     * @SWG\Get(path="/api/eventComingsoonInfo",
     *   tags={"api"},
     *   summary="get eventComingsoonInfo",
     *   description="return eventComingsoonInfo ",
     *   operationId="get eventComingsoonInfo",
     *   produces={"application/json"},
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getEventComingsoonInfo(Request $request)
    {
        $data['team_name'] = $this->boom_setting->get('comingsoon_team_name')->value;
        $data['game_name'] = $this->boom_setting->get('comingsoon_game_name')->value;
        $data['date'] = Carbon::instance(new \DateTime($this->boom_setting->get('comingsoon_date')->value))->format("d M Y");
        return response()->json(['status' => 0, 'msg' => 'success',
            'data' => $data
        ]);
    }

    /**
     * @SWG\Get(path="/api/eventSetOfVodUrl",
     *   tags={"api"},
     *   summary="get event set vod url",
     *   description="return event set vod url",
     *   operationId="return event set vod ur",
     *   produces={"application/json"},
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getEventSetOfVodUrl(Request $request)
    {
        $quality = $request->input('quality');
        if (!in_array($quality, ['high', 'mid', 'low'])) {
            $quality = "";
        }
        $a = [];
        $tmp = explode(",", $this->boom_setting->get('set_url_a')->value);
        $a['jumbotron_url'] = isset($tmp[0]) ? Helper::event_stream_generate_quality(trim($tmp[0]), $quality) : "";
        $a['caster_url'] = isset($tmp[1]) ? Helper::event_stream_generate_quality(trim($tmp[1]), $quality) : "";
        $a['live_1_url'] = isset($tmp[2]) ? Helper::event_stream_generate_quality(trim($tmp[2]), $quality) : "";
        $a['live_2_url'] = isset($tmp[3]) ? Helper::event_stream_generate_quality(trim($tmp[3]), $quality) : "";
        $a['live_3_url'] = isset($tmp[4]) ? Helper::event_stream_generate_quality(trim($tmp[4]), $quality) : "";
        $a['live_4_url'] = isset($tmp[5]) ? Helper::event_stream_generate_quality(trim($tmp[5]), $quality) : "";
        $set_vod['A'] = $a;
        $b = [];
        $tmp = explode(",", $this->boom_setting->get('set_url_b')->value);
        $b['jumbotron_url'] = isset($tmp[0]) ? Helper::event_stream_generate_quality(trim($tmp[0]), $quality) : "";
        $b['caster_url'] = isset($tmp[1]) ? Helper::event_stream_generate_quality(trim($tmp[1]), $quality) : "";
        $b['live_1_url'] = isset($tmp[2]) ? Helper::event_stream_generate_quality(trim($tmp[2]), $quality) : "";
        $b['live_2_url'] = isset($tmp[3]) ? Helper::event_stream_generate_quality(trim($tmp[3]), $quality) : "";
        $b['live_3_url'] = isset($tmp[4]) ? Helper::event_stream_generate_quality(trim($tmp[4]), $quality) : "";
        $b['live_4_url'] = isset($tmp[5]) ? Helper::event_stream_generate_quality(trim($tmp[5]), $quality) : "";
        $set_vod['B'] = $b;

        return response()->json(['status' => 0, 'msg' => 'success',
            'data' => $set_vod
        ]);
    }


    public function setGameStatus($status)
    {
        $user_ip = geoip()->getClientIP();
        $allow_list = config('esea.allowIpAddress');
        $db_list = explode(',', $this->boom_setting->get('allow_ip_list')->value);
        Log::info("[setGameStatus] api has been call from {$user_ip}");
        foreach ($db_list as $item) {
            if (trim($item) != "" && !in_array($item, $allow_list)) {
                $allow_list[] = $item;
            }
        }
        if (!in_array($user_ip, $allow_list)) {
            return response()->json(['status' => 1, "msg" => "Permission denied"]);
        }

        if ($this->boom_setting->get('event_map_change')->value == "manual") {
            return response()->json(['status' => 2, "msg" => "Server active with manual mode"]);
        }

        $id = Setting::firstCreate([
            'name' => "game_status",
            'title' => "Game status",
            'value' => '',
        ]);
        $setting_object = Setting::where('id', $id)->first();
        $setting_object->value = $status;
        $setting_object->save();
        return response()->json(['status' => 0, "msg" => "Success", 'game_status' => $status]);

    }

    /**
     * @SWG\Get(path="/esea/vivemap",
     *   tags={"api"},
     *   summary="vivemap",
     *   description="Return xml map file",
     *   operationId="vivemap",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="mapid",
     *     description="mapid",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */

    public function vivemap(Request $request)
    {
        $xmls = config("esea.files");
        $mapid = $request->input('mapid');
        if (isset($xmls[$mapid])) {
            $path = config("esea.paths.xml-vive") . $xmls[$mapid] . ".xml";
            if (File::exists($path)) {
                $xmlContent = File::get($path);
                return response($xmlContent)
                    ->header('Content-Type', "text/xml");
            }
        }

        return null;

    }

    /**
     * @SWG\Get(path="/api/mobilemap",
     *   tags={"api"},
     *   summary="mobilemap",
     *   description="Return xml map file",
     *   operationId="mobilemap",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="mapid",
     *     description="mapid",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */

    public function mobilemap(Request $request)
    {
        $xmls = config("esea.files");
        $mapid = $request->input('mapid');
        if (isset($xmls[$mapid])) {
            $path = config("esea.paths.xml-mobile") . $xmls[$mapid] . ".xml";
            if (File::exists($path)) {
                $xmlContent = File::get($path);
                return response($xmlContent)
                    ->header('Content-Type', "text/xml");
            }
        }
        return null;
    }

    /**
     * @SWG\Post(path="/api/trending",
     *   tags={"api"},
     *   summary="get trending video",
     *   description="Return list of trending video",
     *   operationId="getCarouselTrendingVideo",
     *   produces={"application/json"},
     *   @SWG\Response()
     * )
     */
    public function getCarouselTrendingVideo(Request $request)
    {
        /*$token = $request->input('token');
        $userToken = Token::where("token", $token)->first();
        $status = 1;
        if (!$userToken){
            $status = 0;
            $error = "User do not exist";
            return response()->json(['status'=>$status,"msg"=>$error,"content"=>[]]);
        }
        else{*/
        $return_data = Video::getTrendingCarousel();
        $status = 1;
        $error = "Success";
        return response()->json(['status' => $status, "msg" => $error, "content" => $return_data]);

        /*}*/
    }

    /**
     * @SWG\GET(path="/api/trending/current",
     *   tags={"api"},
     *   summary="get trending video & current play video",
     *   description="Return list of trending video & current video play index",
     *   operationId="getCarouselTrendingVideo",
     *   produces={"application/json"},
     *   @SWG\Response()
     * )
     */
    public function getTrendingVideoAndCurrentPlay(Request $request)
    {

        $trending_video = File::get(storage_path() . "/trending/videos.json");
        $return_data = json_decode($trending_video, true);

        $return_data = new Collection($return_data);

        $total_duration = (int)$return_data->sum('duration');
        $status = 1;
        $current_play = [];
        $current_time = time();
        $current_time_diff_second = $current_time % (24 * 3600);
        $current_time_diff = $current_time_diff_second % $total_duration;
        $total_play = 0;
        foreach ($return_data as $key => $item) {
            if ($current_time_diff - $total_play < $item['duration']) {
                $current_play = $item;
                $current_play['start_second'] = floor($current_time_diff - $total_play);
                break;
            } else {
                $total_play = $total_play + $item['duration'];
            }
        }
        if (!$current_play) {
            $current_play = $return_data->first();
        }
        $error = "Success";
        return response()->json(['status' => $status, "msg" => $error, 'current_play' => $current_play]);
    }

    /**
     * @SWG\POST(path="/api/updateSessionStreamer",
     *   tags={"api"},
     *   summary="updateSessionStreamer",
     *   description="Return xml map file",
     *   operationId="updateSessionStreamer",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="streamname",
     *     description="streamer name",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="starttime",
     *     description="starttime is timestamp format",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="stoptime",
     *     description="stoptime is timestamp format",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="numberBoom",
     *     description="number of boom cmd in session",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function updateSessionStreamer(Request $request)
    {
        $name = $request->input('streamname');
        $starttime = $request->input('starttime');
        $stoptime = $request->input('stoptime');

        $starttime = Carbon::createFromTimestamp($starttime);
        $stoptime = Carbon::createFromTimestamp($stoptime);

        $numberBoom = $request->input('numberBoom');
        if ($numberBoom == null) {
            $numberBoom = 0;
        }
        Log::info("[UpdateSessionStreamer] name: {$name}, start_time: {$starttime}, stop_time: {$stoptime}");

        $type = $request->input('type');
        if (!in_array($type, config('follow-streamer.type'))) {
            $type = "twitch";
        }
        $user = $this->validateUser($name,$type);
        if (!$user){
            return response()->json(['status' => 100, 'msg' => "User is not exists"]);
        }

        $status = 0;
        $message = "Success";

        if ($stoptime <= $starttime) {
            $status = 1;
            $message = "Input errors. Stoptime > Starttime";
            return response()->json(['status' => $status, "msg" => $message]);
        }

        if ($user == null) {
            $status = 1;
            $message = "User not exist";
            return response()->json(['status' => $status, "msg" => $message]);
        }

        $last_user_session = SessionStreamer::where('user_id', $user->id)->orderBy('stoptime', 'desc')->first();

        if (!$last_user_session) {
            $session = new SessionStreamer();
            $session->user_id = $user->id;
            $session->starttime = $starttime;
            $session->stoptime = $stoptime;
            $session->number_boom = $numberBoom;
            $session->save();
            Log::info("[UpdateSessionStreamer] Create new session {$session->id}");
        } else {
            $last_user_session_start_date = Carbon::instance(new \DateTime($last_user_session->starttime));
            $last_user_session_stop_date = Carbon::instance(new \DateTime($last_user_session->stoptime));

            $current_rq_start_date = Carbon::instance($starttime);
            $current_rq_stop_date = Carbon::instance($stoptime);

            if ($current_rq_start_date < $last_user_session_stop_date) {
                if ($current_rq_stop_date->diffInSeconds($last_user_session_stop_date) < 180) {
                    Log::info("[UpdateSessionStreamer] has a same session. Do nothing");
                } elseif ($current_rq_stop_date->diffInSeconds($last_user_session_stop_date) < config('live-stream.time_between_session')) {
                    $last_user_session->stoptime = $stoptime;
                    $last_user_session->starttime = Carbon::instance(new \DateTime($last_user_session->starttime));
                    $last_user_session->number_boom = $numberBoom;
                    $last_user_session->save();
                    Log::info("[UpdateSessionStreamer] have a older session, updated this session : {$last_user_session->id}");
                } else {
                    Log::info("[UpdateSessionStreamer] has a same session. Do nothing");
                }

            } else {
                //merge with old session or create
                if ($current_rq_start_date->diffInSeconds($last_user_session_stop_date) < config('live-stream.time_between_session')) {
                    $last_user_session->stoptime = $stoptime;
                    $last_user_session->starttime = Carbon::instance(new \DateTime($last_user_session->starttime));
                    $last_user_session->number_boom = $last_user_session->number_boom + $numberBoom;
                    $last_user_session->save();
                    Log::info("[UpdateSessionStreamer] have a older session, updated this session : {$last_user_session->id}");
                } else {
                    $session = new SessionStreamer();
                    $session->user_id = $user->id;
                    $session->starttime = $starttime;
                    $session->stoptime = $stoptime;
                    $session->number_boom = $numberBoom;
                    $session->save();
                    Log::info("[UpdateSessionStreamer] Create new session {$session->id}");
                }
            }

        }
        if (isset($session)) {
            Log::info("[UpdateSessionStreamer] dispatch CreateUserVideoMontage & delay for new session {$session->id}");
            Video::dispatchCreateUserVideoMontageJob($session);
        }

        /*else
            Video::dispatchCreateUserVideoMontageJob($last_user_session);*/
        return response()->json(['status' => $status, "msg" => $message]);
    }

    /**
     * @SWG\POST(path="/api/streamerLiveStart",
     *   tags={"api"},
     *   summary="streamer live start",
     *   description="streamer live start",
     *   operationId="StreamerLiveStart",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="username",
     *     description="streamer name",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function streamerLiveStart(Request $request)
    {
        $name = $request->input('username');
        $type = $request->input('type');
        if (!in_array($type, config('follow-streamer.type'))) {
            $type = "twitch";
        }
        if ($type == "twitch") {
            $user = User::where("name", $name)->where('type', User::USER_TYPE_TWITCH)->first();
        } elseif ($type == "mixer") {
            $user = User::where("name", $name)->where('type', User::USER_TYPE_MIXER)->first();
        }
        elseif($type == "youtube"){
            $user = User::where("name", $name)->where('type', User::USER_TYPE_YOUTUBE)->first();
        }
        if (!$user) {
            return response()->json(['status' => 100, 'msg' => "User is not exists"]);
        }
        $last_stream_time = Carbon::now()->subSecond(config('live-stream.time_bettween_live_stream'));
        $live_exist = LiveStream::where('is_live', 1)->where('user_id', $user->id)->where('created_at', '>=', $last_stream_time)->first();
        Log::info("[live-stream] request new live stream: {$name}");
        if ($live_exist) {
            Log::info("[live-stream] live stream {$name} exists");
            return response()->json(['status' => 1, 'msg' => "Live stream exists", 'live_stream_id' => $live_exist->id]);
        }

        //check nearly live stream & merge
        $nearly_time = Carbon::now()->subMinutes(30);
        $nearly_live_stream = LiveStream::where('is_live', 0)->where('user_id', $user->id)->where('stopped_time', '>=', $nearly_time)->first();
        if ($nearly_live_stream) {
            $nearly_live_stream->is_live = 1;
            $nearly_live_stream->stopped_time = "0000-00-00 00:00:00";
            $nearly_live_stream->save();
            $this->createStreamTrackingJob($nearly_live_stream, true);
            Log::info("[live-stream] Restart live stream {$nearly_live_stream->id}");
            return response()->json(['status' => 2, 'msg' => "Restart live stream", 'live_stream_id' => $nearly_live_stream->id]);
        }

        //stop other live stream
        $other_live_stream = LiveStream::where('is_live', 1)->where('user_id', $user->id)->get();
        if (count($other_live_stream)) {
            Log::info("[live-stream] stop other live stream [$name]");
            foreach ($other_live_stream as $item) {
                $item->is_live = 0;
                $item->stopped_time = Carbon::now();
                $item->save();
                $this->createLiveStreamStopJob($item);
            }
        }


        $live_stream = new LiveStream();
        $live_stream->user_id = $user->id;
        $live_stream->is_live = 1;
        $live_stream->started_time = Carbon::now();
        $live_stream->save();
        Log::info("[live-stream] create live stream & dispatch LiveStreamStart {$live_stream->id}");
        $this->createStreamTrackingJob($live_stream);

        return response()->json(['status' => 0, 'msg' => "Start success", 'live_stream_id' => $live_stream->id, 'name' => $name]);
    }

    /**
     * @SWG\POST(path="/api/streamerLiveStop",
     *   tags={"api"},
     *   summary="streamer live stop",
     *   description="streamer live stop",
     *   operationId="StreamerLiveStop",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="username",
     *     description="streamer name",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function streamerLiveStop(Request $request)
    {
        $name = $request->input('username');
        $type = $request->input('type');
        if (!in_array($type, config('follow-streamer.type'))) {
            $type = "twitch";
        }
        if ($type == "twitch") {
            $user = User::where("name", $name)->where('type', User::USER_TYPE_TWITCH)->first();
        } elseif ($type == "mixer") {
            $user = User::where("name", $name)->where('type', User::USER_TYPE_MIXER)->first();
        }
        elseif($type == "youtube"){
            $user = User::where("name", $name)->where('type', User::USER_TYPE_YOUTUBE)->first();
        }
        if (!$user) {
            return response()->json(['status' => 100, 'msg' => "User is not exists"]);
        }

        $live_streams = LiveStream::where('is_live', 1)->where('user_id', $user->id)->get();
        if (count($live_streams)) {
            foreach ($live_streams as $item) {
                $item->is_live = 0;
                $item->stopped_time = Carbon::now();
                $item->save();
                Log::info("[live-stream] stop live stream & dispatch LiveStreamStop {$item->id}");
                $this->createLiveStreamStopJob($item);
            }
        }
        return response()->json(['status' => 0, 'msg' => "Stop success"]);
    }

    /**
     * @SWG\GET(path="/api/checkChannelModerator/{channel}",
     *   tags={"api"},
     *   summary="check channel have boomtvmod moderator ",
     *   description="check channel have boomtvmod moderator",
     *   operationId="checkChannelModerator",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Response(response="default", description="status and message")
     * )
     */

    public function checkChannelModerator(Request $request, $channel)
    {
        $type = $request->input('type');
        if ($type == "mixer") {
            $boomtv_user = User::where('name', 'boomtvmod')->where("type", User::USER_TYPE_MIXER)->first();
            $boomtv_social = SocialAccount::where("user_id", $boomtv_user->id)->first();
            $result = MixerHelper::checkChannelModerator($channel, $boomtv_social);
            $status = 0;
            $moderator = "boomtvmod";
            $message = "Haven't " . $moderator . " moderator";
            if ($result) {
                $status = 1;
                $message = "Have " . $moderator . " moderator";
            }
            return response()->json(['status' => $status, 'msg' => $message]);
        } else {
            $boomtv_user = User::where('name', 'boomtvmod')->where("type", User::USER_TYPE_TWITCH)->first();
            $boomtv_social = SocialAccount::where("user_id", $boomtv_user->id)
                ->first();
            $access_token = $boomtv_social->access_token;
            $nickname = $boomtv_user->name;
            $sock = fsockopen('irc.twitch.tv', 6667);
            if ($sock) {
                fwrite($sock, "PASS oauth:" . $access_token . "\r\n");
                fwrite($sock, "NICK " . $nickname . "\r\n");

                fwrite($sock, "JOIN #" . $channel . "\r\n");
                fwrite($sock, "CAP REQ :twitch.tv/commands" . "\r\n");
                fwrite($sock, "PRIVMSG #" . $channel . " :.mods \r\n");

                $string = "The moderators of this room are";
                $stringNotFound = "There are no moderators of this room";
                $status = 0;
                $moderator = "boomtvmod";
                $message = "Haven't " . $moderator . " moderator";
                while ($content = fgets($sock)) {
                    $result = strpos($content, $string);
                    if ($result) {

                        $resModerator = strpos($content, $moderator);
                        if ($resModerator) {
                            $status = 1;
                            $message = "Have " . $moderator . " moderator";
                        }
                        break;
                    }
                    $resultNotFound = strpos($content, $stringNotFound);
                    if ($resultNotFound) {
                        break;
                    }
                }
                Log::info("checkChannelModerator success");
            } else {
                Log::error("checkChannelModerator error: chat twich fiel");
            }
            fclose($sock);
            return response()->json(['status' => $status, 'msg' => $message]);
        }
    }

    /**
     * @SWG\GET(path="/api/getChannelModerator/{channel}",
     *   tags={"api"},
     *   summary="get list of moderators from this channel",
     *   description="get list of moderators from this channel",
     *   operationId="getChannelModerator",
     *   produces={"application/json"},
     *   @SWG\Response(response="default", description="status and message")
     * )
     */

    public function getChannelModerator(Request $request, $channel)
    {
        $type = $request->input('type');
        if ($type == "mixer") {
            return response()->json(['status' => 0, 'msg' => 'Not implemented yet!!!']);
        } else {
            return response()->json(TwitchHelper::getModListForChannel($channel));
        }
    }

    /**
     * @SWG\POST(path="/api/getSponsorshipVideoInfo",
     *   tags={"api"},
     *   summary="get infomation for sponsorship video",
     *   description="get infomation for sponsorship video",
     *   operationId="getSponsorshipVideoInfo",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="token",
     *     description="token",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function getSponsorshipVideoInfo(Request $request)
    {
        $token = $request->input('token');
        $userToken = Token::where("token", $token)->first();
        if (!$userToken) {
            return response()->json(['status' => 2, 'msg' => "User is not exists"]);
        } else {
            $info = array();
            $userId = $userToken->user_id;
            $currenttime = Carbon::now();
            $currenttime = $currenttime->toDateTimeString();
            $sponsorship = Sponsorship::where("user_id", $userId)
                ->where("starttime", "<=", $currenttime)
                ->where("expiredtime", ">=", $currenttime)
                ->where("status", "!=", Sponsorship::DELETED_STATUS)
                ->first();
            if ($sponsorship != null) {
                return response()->json(['status' => 0, 'id' => $sponsorship->id, 'video_link' => $sponsorship->video_link,
                    'starttime' => $sponsorship->starttime,
                    'expiretime' => $sponsorship->expiredtime,
                    'duration' => $sponsorship->duration]);
            } else {
                return response()->json(['status' => 1, 'msg' => "Not found sponsorship"]);
            }
        }
    }

    public function startFollowStreamer(Request $request)
    {
        $redis = Redis::connection("boombot");
        $name = $request->input('name');
        $type = $request->input('type');
        if ($type == null) {
            $type = "twitch";
        }
        if (!in_array($type, config('follow-streamer.type'))) {
            $type = config('follow-streamer.type')[0];
        }
        $user = $this->validateUser($name,$type);

        if (!$user) {
            return response()->json(['status' => 100, 'msg' => "User is not exists"]);
        }
        Log::info("[startFollowStreamer] start follow stream {$user->name} {$type} ");
        $key_list_all_streamer = Lang::get("cached.followListAllStreamer");
        $redis->sadd($key_list_all_streamer, $user->id);
        Log::info("[startFollowStreamer] push to followListAllStreamer {$user->name} {$type} ");

        if ($type == "mixer") {
            $key_cached = Lang::get("cached.streamerStateType", ['name' => $user->name, 'type' => $user->type]);
            $user_exist = $redis->get($key_cached);
            $data = [];
            if ($user_exist) {
                $user_exist = json_decode($user_exist, true);
                $data['followStatus'] = 1;
                $data['totalViewer'] = $user_exist['totalViewer'];
                $data['status'] = $user_exist['status'];
                $data['jobCount'] = isset($user_exist['jobCount']) ? $user_exist['jobCount'] : 0;
            } else {
                $data['followStatus'] = 1;
                $data['totalViewer'] = 0;
                $data['status'] = 0;
                $data['jobCount'] = 0;
            }

            User::dispatchFollowMixerStreamerJob($user, 0);
            $data['jobCount'] = 1;
            $redis->setex($key_cached, config("follow-streamer.cached.startTimeout"), json_encode($data));
            return response()->json(['status' => 0, 'msg' => "Success start follow stream"]);
        } elseif ($type == "twitch") {
            $key_cached = Lang::get("cached.streamerState", ['name' => $user->name]);
            $user_exist = $redis->get($key_cached);
            $data = [];
            if ($user_exist) {
                $user_exist = json_decode($user_exist, true);
                $data['followStatus'] = 1;
                $data['totalViewer'] = isset($user_exist['totalViewer']) ? $user_exist['totalViewer'] : 0;
                $data['status'] = $user_exist['status'];
                $data['jobCount'] = isset($user_exist['jobCount']) ? $user_exist['jobCount'] : 0;
            } else {
                $data['followStatus'] = 1;
                $data['totalViewer'] = 0;
                $data['status'] = 0;
                $data['jobCount'] = 0;
            }

            User::dispatchFollowStreamerJob($user, 0);
            $data['jobCount'] = 1;

            $redis->setex($key_cached, config("follow-streamer.cached.startTimeout"), json_encode($data));
            return response()->json(['status' => 0, 'msg' => "Success start follow stream"]);
        }
        elseif ($type == "youtube") {
            $key_cached = Lang::get("cached.streamerStateType", ['name' => $user->name, 'type' => $user->type]);
            $user_exist = $redis->get($key_cached);
            $data = [];
            if ($user_exist) {
                $user_exist = json_decode($user_exist, true);
                $data['followStatus'] = 1;
                $data['totalViewer'] = isset($user_exist['totalViewer']) ? $user_exist['totalViewer'] : 0;
                $data['status'] = $user_exist['status'];
                $data['jobCount'] = isset($user_exist['jobCount']) ? $user_exist['jobCount'] : 0;
            } else {
                $data['followStatus'] = 1;
                $data['totalViewer'] = 0;
                $data['status'] = 0;
                $data['jobCount'] = 0;
            }

            User::dispatchFollowStreamerJob($user, 0);
            $data['jobCount'] = 1;

            $redis->setex($key_cached, config("follow-streamer.cached.startTimeout"), json_encode($data));
            return response()->json(['status' => 0, 'msg' => "Success start follow stream"]);
        }

    }

    private function validateUser($name,$type){

        if ($type == "mixer") {
            $user = User::where('name', $name)->where('type', User::USER_TYPE_MIXER)->first();
        } elseif ($type == "twitch") {
            $user = User::where('name', $name)->where('type', User::USER_TYPE_TWITCH)->first();
        }
        elseif ($type == "youtube") {
            $user = User::where('name', $name)->where('type', User::USER_TYPE_YOUTUBE)->first();
        }
        return $user;
    }

    public function stopFollowStreamer(Request $request)
    {
        $redis = Redis::connection("boombot");
        $name = $request->input('name');
        $type = $request->input('type');
        if ($type == null) {
            $type = "twitch";
        }
        if (!in_array($type, config('follow-streamer.type'))) {
            $type = config('follow-streamer.type')[0];
        }
        $user = $this->validateUser($name,$type);
        if (!$user) {
            return response()->json(['status' => 100, 'msg' => "User is not exists"]);
        }
        if ($type == "mixer") {
            $key_cached = Lang::get("cached.streamerStateType", ['name' => $user->name, 'type' => $user->type]);
        } elseif ($type == "twitch") {
            $key_cached = Lang::get("cached.streamerState", ['name' => $user->name]);
        }
        elseif ($type == "youtube") {
            $key_cached = Lang::get("cached.streamerStateType", ['name' => $user->name, 'type' => $user->type]);
        }

        $user_exist = $redis->get($key_cached);
        $data = [];
        if ($type == "twitch") {
            if ($user_exist) {
                $user_exist = json_decode($user_exist, true);
                $data['followStatus'] = 0;
                $data['totalViewer'] = isset($user_exist['totalViewer']) ? $user_exist['totalViewer'] : 0;;
                $data['status'] = $user_exist['status'];
                $data['jobCount'] = 0;
                $redis->setex($key_cached, config("follow-streamer.cached.stopTimeout"), json_encode($data));
            }
        } elseif ($type == "mixer") {
            if ($user_exist) {
                $user_exist = json_decode($user_exist, true);
                $data['followStatus'] = 0;
                $data['totalViewer'] = $user_exist['totalViewer'];
                $data['status'] = $user_exist['status'];
                $data['jobCount'] = $user_exist['jobCount'];
                $redis->setex($key_cached, config("follow-streamer.cached.stopTimeout"), json_encode($data));
            }
        }
        elseif ($type == "youtube") {
            if ($user_exist) {
                $user_exist = json_decode($user_exist, true);
                $data['followStatus'] = 0;
                $data['totalViewer'] = $user_exist['totalViewer'];
                $data['status'] = $user_exist['status'];
                $data['jobCount'] = $user_exist['jobCount'];
                $redis->setex($key_cached, config("follow-streamer.cached.stopTimeout"), json_encode($data));
            }
        }

        if ($type == "twitch") {
            if (!in_array($user->name, config("follow-streamer.streamerAlwaysCheck"))) {
                $key_list_streamer_cached = Lang::get("cached.followListStreamer");
                $redis->lrem($key_list_streamer_cached, 0, $user->name);
            }
        } elseif ($type == "mixer") {
            $key_list_streamer_cached = Lang::get("cached.followListStreamerMixer");
            $redis->lrem($key_list_streamer_cached, 0, $user->name);
        }
        elseif ($type == "youtube") {
            $key_list_streamer_cached = Lang::get("cached.followListStreamerYoutube");
            $redis->lrem($key_list_streamer_cached, 0, $user->name);
        }

        if (!in_array($user->name, config("follow-streamer.streamerAlwaysCheck"))) {
            $key_list_all_streamer = Lang::get("cached.followListAllStreamer");
            $redis->srem($key_list_all_streamer, $user->id);
        }


        Log::info("[stopFollowStreamer] {$user->name} {$type}");
        return response()->json(['status' => 0, 'msg' => "Success stop follow stream"]);
    }

    public function getStateOfStreamer(Request $request)
    {
        $name = $request->input('name');
        $user = User::where('name', $name)->first();
        if (!$user) {
            return response()->json(['status' => 100, 'msg' => "User is not exists"]);
        }
    }
    /**
     * @SWG\POST(path="/api/saveSetting",
     *   tags={"api"},
     *   summary="",
     *   description="",
     *   operationId="saveSetting",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="token",
     *     description="token",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="auto_tweet",
     *     description="auto_tweet",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    //twitter_streamer
    public function saveSetting(Request $request)
    {
        // input request : token, auto_tweet
        $token = $request->input('token');
        $autoTweet = $request->input('auto_tweet');
        $userToken = Token::where("token", $token)->first();
        if ($userToken != null) {
            $socialConnected = SocialConnected::where("user_id",
                $userToken->user_id)->first();
            if ($socialConnected != null) {
                $socialConnected->auto_tweet = $autoTweet;
                $socialConnected->save();
                User::flushLoginInfo($userToken->user_id);
                return response()->json(array("status" => 0));
            } else {
                return response()->json(array("status" => 2,
                    "error" => "Not connect social"));
            }
        } else {
            return response()->json(array("status" => 1, "error" => "user do not exist"));
        }
    }

    /**
     * @SWG\POST(path="/api/shareOnSocial",
     *   tags={"api"},
     *   summary="",
     *   description="",
     *   operationId="shareOnSocial",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="video_code",
     *     description="video_code",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="post_content",
     *     description="post_content",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function shareOnSocial(Request $request)
    {
        // video_link, video_code, message, token
        $postContent = $request->input('post_content');
        $videoCode = $request->input('video_code');
        Log::info("Share $postContent $videoCode");
        $video = Video::where("code", $videoCode)->first();
        if ($video != null) {
            $twitter = SocialConnected::where("user_id",
                $video->user_id)->first();
            if ($twitter != null) {
                $result = Helper::postTwitter($twitter->token, $twitter->token_secret, $postContent);
                if ($result) {
                    Log::info("Share ok");
                    return response()->json(array("status" => 0));
                } else {
                    Log::info("Share error");
                    return response()->json(array("status" => 3, "error" => "post twitter error"));
                }
            } else {
                Log::info("Share twitter do not connect");
                return response()->json(array("status" => 2, "error" => "twitter do not connect"));
            }

        } else {
            Log::info("share video do not exist");
            return response()->json(array("status" => 1, "error" => "video do not exist"));
        }

    }

    /**
     * @SWG\POST(path="/api/refreshToken",
     *   tags={"api"},
     *   summary="refresh token",
     *   description="refresh token",
     *   operationId="refreshToken",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="token",
     *     description="token",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function refreshToken(Request $request)
    {
        $token = $request->input('token');
        $userToken = Token::where("token", $token)->first();
        if (!$userToken) {
            return response()->json(['status' => 2, 'msg' => "User is not exists"]);
        } else {
            $userId = $userToken->user_id;
            $user = User::find($userId);
            if ($user != null && $user->type == User::USER_TYPE_MIXER) {
                $social = SocialAccount::where("user_id", $userId)
                    ->first();
                $update = MixerHelper::refreshToken($social);
                if ($update != null) {
                    return response()->json(['status' => 0, 'access_token' => $update->access_token]);
                }
            }
            return response()->json(['status' => 1, 'msg' => "User not expire"]);
        }
    }
    // Disconect twitter
    /**
     * @SWG\POST(path="/api/removeConnections",
     *   tags={"api"},
     *   summary="removeConnections",
     *   description="removeConnections",
     *   operationId="removeConnections",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="token",
     *     description="token",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="type",
     *     description="type: twitter, discord",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function removeConnections(Request $request)
    {
        $token = $request->input('token');
        $type = $request->input('type');
        $userToken = Token::where("token", $token)->first();
        if (!$userToken) {
            return response()->json(['status' => 2, 'msg' => "User is not exists"]);
        } else {
            $userId = $userToken->user_id;
            if ($type == "discord") {
                $socialConnected = DiscordInfo::where("user_id", $userId)->delete();
                return response()->json(['status' => 0]);
            } elseif ($type == "twitter") {
                $socialConnected = SocialConnected::where("user_id", $userId)->delete();
                User::flushLoginInfo($userId);
                return response()->json(['status' => 0]);
            } else {
                return response()->json(['status' => 1, 'msg' => 'Invaild type connection']);
            }
        }
    }

    // Disconect twitter
    /**
     * @SWG\POST(path="/api/checkUploadSpeed",
     *   tags={"api"},
     *   summary="checkUploadSpeed",
     *   description="checkUploadSpeed",
     *   operationId="checkUploadSpeed",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="file",
     *     description="upload video",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Response(response="default", description="status = 0 if success")
     * )
     */
    public function checkUploadSpeed(Request $request)
    {
        $status = 1;
        $error = "The file is not present on the request";
        if ($request->hasFile('file')) {
            $file = $request->file("file");
            if ($file->isValid()) {
                $status = 0;
                $link = $file->getClientOriginalName();
                return response()->json(['status' => $status, 'path' => $link]);
            } else {
                $status = 2;
                $error = "Verify fail";
            }
        }
        return response()->json(['status' => $status, 'error' => $error]);
    }

    /**
     * @SWG\POST(path="/api/getVideosNewest/{username}",
     *   tags={"api"},
     *   summary="getVideosNewest",
     *   description="getVideosNewest",
     *   operationId="getVideosNewest",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Response(response="default", description="status = 0 if success")
     * )
     */
    public function getVideosNewest(Request $request, $username)
    {
        $status = 1;
        $error = "User not found";
        $type = $request->input('type');
        $user = User::where("name", $username)->first();
        if ($user != null) {
            $videos = Video::where("user_id", $user->id)
                ->orderBy("created_at", "desc")
                ->take(5)->offset(0)->get();
            $status = 0;
            return response()->json(['status' => $status, 'videos' => $videos]);
        }
        return response()->json(['status' => $status, 'error' => $error]);
    }

    // testing purpose
    public function testSendSparkpostMail()
    {
        $tempId = 'test-2';
        $info['dateSubject'] = "2017-07-07";
        $info['imageProfile'] = "https://static-cdn.jtvnw.net/jtv_user_pictures/drdisrespectlive-profile_image-abc1fc67d2ea1ae1-300x300.png";
        $info['username'] = "TEST";
        $info['numberBoom'] = 10000;
        /*$info['user'] = $user;
        $info['video'] = $video;
        $info["sender"] = $sendername;
        $info['user'] = $user;*/
        $info['date'] = "2017-07-07";
        $info['link'] = route('playvideo', ['v' => 'rlISlJSp01UKw']);
        $info['link_share'] = route('playvideo', ['v' => 'rlISlJSp01UKw']) . "&ref=share";
        $info['thumbnail'] = 'https://d2540bljzu9e1.cloudfront.net/thumb/7099/Talk_Shows_2017-07-30_00-55-57_1501401416.jpg';
        $info['unsubscribe'] = 'https://boom.tv';
        $recipients = [
            [
                'address' => [
                    'name' => 'TRUNGHT',
                    'email' => 'tan.nn@boom.tv',
                ],
            ],
        ];
        Helper::sendMailBySparkPostTemplate($tempId, $info, $recipients, []);
    }

    public function sparkpostEventProcesss(Request $request)
    {
        $all = $request->all();
        Log::info("sparkpostEventProcesss " . json_encode($all));
        foreach ($all as $item) {
            if (!isset($item['msys']['track_event'])) {
                continue;
            }
            $data = $item['msys']['track_event'];
            if (isset($data['transmission_id'])) {
                $user_reminder_mail_log = UserReminderMailLog::where("transmission_id", $data['transmission_id'])->first();
                if ($user_reminder_mail_log) {
                    $user_reminder_mail_log->sparkpost_status = $data['type'];
                    $user_reminder_mail_log->save();
                    Log::info("sparkpostEventProcesss changed mail log {$user_reminder_mail_log->id}");
                }
            }
        }

    }

    /**
     * @SWG\Post(path="/api/uninstallInfo",
     *   tags={"api"},
     *   summary="get Uninstall user info",
     *   description="get Uninstall user info",
     *   operationId="api/infoUninstall",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="token",
     *     description="boom token",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="version",
     *     description="version info",
     *     required=false,
     *     type="string"
     *   ),
     *
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */

    public function uninstallInfo(Request $request)
    {
        $token = $request->input('token');
        $version = $request->input('version');
        Log::info("uninstallInfo token {$token} {$version}");
        $userToken = Token::where("token", $token)->first();

        if ($userToken != null) {
            if ($version == '') {
                $version = "old-version";
            }

            $info = new UninstallInfo;
            $info->user_id = $userToken->user_id;
            $info->version = $version;
            $info->time = Carbon::now();
            $info->save();
            $data = [
                'status' => 0,
                'msg' => 'Success'
            ];
        } else {
            $data = [
                'status' => 1,
                'msg' => 'User do not exist'
            ];
            Log::info("uninstallInfo User do not exist");
        }
        return response()->json($data);
    }

    /**
     * @param  $channel_id
     *
     * @return \json
     */
    public function getYoutubeToken($channel_id = '')
    {
        $response = array();

        if ($channel_id != '') {
            $boomtv_social = SocialAccount::where("channel_id", $channel_id)->first();

            if (!empty($boomtv_social))
                return response()->json(['status' => 'success', 'access_token' => $boomtv_social->access_token, 'refresh_token' => $boomtv_social->refresh_token]);
            else
                return response()->json(['status' => 'error', 'msg' => 'Incorrect channel id']);
        } else {
            return response()->json(['status' => 'error', 'msg' => 'Channel id is required']);
        }
    }
}
