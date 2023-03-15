<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App;
use Illuminate\Support\Facades\Session;
use TwitchApi;
use Auth;
use App\Models\User;
use DB;
use Log;
use Mail;
use App\Models\SocialAccount;
use View;
use Lang;
use App\Models\BoomMeter;
use App\Models\BoomMeterType;
use App\Models\SessionBoomMeter;
use URL;
use Redirect;
use App\Models\Token;

class BoomMeterUserController extends Controller
{
    public function status(Request $request, $token = "")
    {
        $return  = BoomMeter::getInfoToReview($token);
        return view('boom.boom', $return);
        /*$twitch = SocialAccount::where("access_token", $token)->first();
        $userByCode = User::where("code", $token)->first();
        $cooldown = $request->input('cooldown');
        if (!in_array($cooldown, [1, 2, 3, 4])) $cooldown = 0;
        $chanel = "";
        $folderServer = strtolower(config("aws.folder_client"));
        $links3 = config("aws.linkS3BoomMeter");
        $path = URL::to('/') . "/boom_status/image_gif/";
        $pathDefault = $links3 . "default/";
        $cssLink = $pathDefault . "style.css";
        $checkDefault = -1;
        $version = "?0";
        if ($twitch != null) {
            $user = User::find($twitch->user_id);
            $chanel = $user->name;
        }
        $boomMeterTypeId = null;
        $userCode = "";
        if ($userByCode != null) {
            $userCode = $userByCode->code;
            $chanel = $userByCode->name;
            $boomMeter = BoomMeter::where("user_code", $userByCode->code)
                ->first();
            if ($boomMeter != null) {
                
                $links3 = config("aws.linkS3BoomMeter") . $folderServer . "/";
                $cssLink = $links3 . "defaults/style.css";
                $typeMeter = BoomMeterType::find($boomMeter->boom_meter_type_id);
                $boomMeterTypeId = $boomMeter->boom_meter_type_id;
                $version = "?".$typeMeter->version;
                if ($typeMeter->type != BoomMeterType::CUSTOM_TYPE) {
                    $path = $links3 . $typeMeter->folders3 . "/";
                    $checkDefault = 0;
                } else {
                    $checkDefault = 1;
                    $version = "?" . $boomMeter->timestamp;

                    if ($boomMeter->custom_img) {
                        $path = $links3 . $boomMeter->user_code . "/";
                    }
                    if ($boomMeter->custom_style) {
                        $cssLink = $links3 . $boomMeter->user_code . "/" . "style.css";
                    }
                }
            }
        }
        $cssContent = file_get_contents($cssLink);
        return view('boom.boom', ["chanel" => $chanel,
            "cooldown" => $cooldown, "path" => $path,
            "cssLink" => $cssLink, "checkDefault" => $checkDefault,
            "cssContent" => $cssContent,
            "version" => $version,
            "boomMeterTypeId" => $boomMeterTypeId,
            "userCode" => $userCode]);*/
    }
    public function updateSessionBoomMeter(Request $request)
    {
        $userCode = $request->input('userCode');
        $boomMeterTypeId = $request->input('boomMeterTypeId');
        SessionBoomMeter::saveSession($userCode, $boomMeterTypeId);
        return response()->json(['status' => 1]);
    }
    public function boomMeter(Request $request)
    {
        $token = $request->input('token');
        if ($token != null) {
            $ref = $this->checkValidateRef($request);
            $token_obj = Token::where('token', $token)->first();
            if ($token_obj) {
                $user = User::where('id', $token_obj->user_id)->first();
                if ($user) {
                    auth()->login($user);
                    return redirect()->to(route("skins",['ref'=>$ref]));
                } else {
                    return redirect()->to(route("skins",['ref'=>$ref]));
                }
            } else {
                return redirect()->to(route("skins",['ref'=>$ref]));
            }
        }
        if (Auth::check()) {
            $user = Auth::user();

            $ref = $this->checkValidateRef($request);

            $boomMeter = BoomMeter::where("user_code", $user->code)->first();
            $typeClassic = BoomMeterType::where("type", BoomMeterType::DEFAULT_BASIC_TYPE)->first();
            if ($boomMeter == null) {
                $boomMeter = new BoomMeter();
                $boomMeter->boom_meter_type_id = $typeClassic->id;
                $boomMeter->user_code = $user->code;
                $boomMeter->timestamp = time();
                $boomMeter->save();
            }
            if ($user->allow_custom_meter) {
                $boomMeterTypes = BoomMeterType::orderBy('type')->orderBy('created_at')->get();
            } else {
                $boomMeterTypes = BoomMeterType::where("type", "!=", BoomMeterType::CUSTOM_TYPE)->get();
            }
            $hasImage = false;
            if ($boomMeter->custom_img == 1) $hasImage = 1;
            $assetLink = config('content.cloudfront') . '/assets/' . config('content.assets_ver') . '/';
            $timestamp = $boomMeter->timestamp;
            $links3 = config("aws.linkS3BoomMeter");
            $folderServer = strtolower(config("aws.folder_client"));
            $folderLink = $links3 . $folderServer . "/";
            $imageCustom = $folderLink . $user->code . "/";
            $boom_meter_changed_msg = Session::pull('boom_meter_msg');
            return view('user.custom_boom_meter',
                [
                    "boomMeterTypes" => $boomMeterTypes,
                    "links3" => $links3 . $folderServer . "/",
                    "assetLink" => $assetLink,
                    "installTypeId" => $boomMeter->boom_meter_type_id,
                    "hasImage" => $hasImage,
                    "imageCustom" => $imageCustom,
                    "note" => Lang::get('user.boom_meter_note.' . $ref),
                    'boom_meter_changed_msg' => $boom_meter_changed_msg,
                    'ref' => $ref,
                    "timestamp" => $timestamp
                ]
            );
        } else {
            $ref = $this->checkValidateRef($request);
            $uri = route("skins",['ref'=>$ref]);
            $urlLogin = route("oauth", ['is_claim' => 1, 'source' => 0, 'redirect_uri' => $uri]);
            return redirect()->to($urlLogin);
        }

    }

    public function actionBoomMeter(Request $request, $action, $boomMeterTypeId)
    {
        // action: install, unlock
        if (Auth::check()) {
            $usercode = Auth::user()->code;
            if ($action == "install") {
                $ref = $this->checkValidateRef($request);
                BoomMeter::installBoomMeter($usercode, $boomMeterTypeId);
                return response()->json(['status'=>0,'msg'=>Lang::get('user.boom_meter_alert.' . $ref)]);
            }
            /*if($action == "unlock")
            {
                BoomMeter::unlockBoomMeter($usercode, $boomMeterId);
            }*/
        }
        else{
            return response()->json(['status'=>1,'msg'=>'Authenticated fail!']);
        }
    }

    public function getUploadImage()
    {
        if (Auth::check()) {
            $images = ["1.gif", "2.gif", "3.gif", "4.gif", "5.gif", "6.gif", "7.gif", "8.gif", "9.gif", "CD5half.gif", "CDRising.gif", "CDStatic.png"];
            return view('user.upload_boom_meter', ["images" => $images]);
        } else {
            $uri = route("custom_boom_meter");
            $urlLogin = route("oauth", ['is_claim' => 1, 'source' => 0, 'redirect_uri' => $uri]);
            return redirect()->to($urlLogin);
        }
    }

    public function postUploadImage(Request $request)
    {
        $file = $request->file("file");
        $error = true;
        if ($request->hasFile('file')) {
            if (Auth::check()) {
                $user = Auth::user();
                $result = BoomMeter::uploadBoomMeter($file, $user->code);
                if ($result) {
                    return Redirect::route('review_boom_meter');
                }
            }
        }
        return Redirect::back()->withErrors(['File type is zip']);
    }

    public function uploadCss(Request $request)
    {
        $content = $request->input("content");
        if (Auth::check()) {
            $user = Auth::user();
            $result = BoomMeter::uploadCssToS3($user->code, $content);
            if ($result) {
                return response()->json(['status' => 0, 'message' => 'Set css successly!']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Set css error!']);
            }

        }
    }

    public function review()
    {
        if (Auth::check()) {
            $code = Auth::user()->code;
            $return  = BoomMeter::getInfoToReview($code, true);
            return view('user.review_custom_meter', $return);
        } else {
            $uri = route("review_boom_meter");
            $urlLogin = route("oauth", ['is_claim' => 1, 'source' => 0, 'redirect_uri' => $uri]);
        }
    }

    public function demoBoomMeter(Request $request, $id)
    {
        $id = intval($id);

        $boom_meter_type = BoomMeterType::where('id', $id)->first();
        if (!$boom_meter_type) {
            abort(403);
        }
        if ($boom_meter_type->type == 2) {
            if (Auth::check()) {
                $code = Auth::user()->code;
                if (!auth()->user()->allow_custom_meter){
                    abort(403);
                }
                $boomMeter = BoomMeter::where("user_code", $code)->first();
                if ($boomMeter == null){
                    return redirect()->to(route("skins"));
                }
                if ($boomMeter->custom_img == 0){
                    return redirect()->to(route("skins"));
                }
                $links3 = config("aws.linkS3BoomMeter");
                $path = $links3 . "default/";
                $pathDefault = $path;
                $cssLink = $path . "style.css";
                $version = "?" . $boomMeter->timestamp;
                $links3 = config("aws.linkS3BoomMeter");
                $folderServer = strtolower(config("aws.folder_client"));
                if ($boomMeter->custom_img){
                    $path = $links3 . $boom_meter_type->folders3 . $folderServer . "/" . auth()->user()->code . "/";
                }
                if ($boomMeter->custom_style){
                    $cssLink = $links3 . $boom_meter_type->folders3 . $folderServer . "/" . auth()->user()->code . "/" . "style.css";
                }
                $cssContent = file_get_contents($cssLink . $version);
                return view('user.demo_meter', ["code" => $code,
                    "path" => $path, "cssLink" => $cssLink,
                    "cssContent" => $cssContent,
                    "pathDefault" => $pathDefault, "version" => $version]);
            } else {
                $uri = route("demo_boom_meter",['id'=>$id]);
                $urlLogin = route("oauth", ['is_claim' => 1, 'source' => 0, 'redirect_uri' => $uri]);
                return redirect()->to($urlLogin);
            }
        } else {
            $links3 = config("aws.linkS3BoomMeter");
            $path = $links3 . "default/";
            $pathDefault = $path;
            $version = "?" . $boom_meter_type->version;
            $links3 = config("aws.linkS3BoomMeter");
            $folderServer = strtolower(config("aws.folder_client"));

            $path = $links3 . $folderServer . "/" . $boom_meter_type->folders3 . "/";
            $cssLink = $links3 . $folderServer . "/" . "defaults" . "/" . "style.css";
            $cssContent = file_get_contents($cssLink . $version);

            return view('user.demo_meter', [
                "path" => $path, "cssLink" => $cssLink,
                "cssContent" => $cssContent,
                "pathDefault" => $pathDefault, "version" => $version]);
        }


    }

    private function checkValidateRef(Request $request){
        $ref = $request->input('ref');
        if ($ref == null) {
            $ref = "returnuser";
        }
        if (!in_array($ref, ['onboarding', 'returnuser','olduser'])) {
            $ref = "returnuser";
        }
        if ($ref == "olduser"){
            $ref = "returnuser";
        }
        return $ref;
    }
}
