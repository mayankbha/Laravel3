<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Game;
use File;
use App\Models\BoomEvent;

use Log;
use Illuminate\Http\Request;

use App\Http\Requests;
use phpDocumentor\Reflection\Types\Object_;

class EventController extends Controller
{
    //

    public function showEvent(Request $request){
        if ($request->server('HTTP_X_FORWARDED_PROTO') == 'https'){
            $scheme = 'https';
        }
        elseif($request->server('REQUEST_SCHEME') == "https"){
            $scheme = 'https';
        }
        else{
            $scheme = 'http';
        }

        if($scheme == 'https') {
            return redirect()->to(str_replace("https","http",$request->fullUrl()));
        }

        $game_status = 1000;
        if ($this->boom_setting->get('game_status'))
        {
            $game_status = $this->boom_setting->get('game_status')->value;
        }

        if (!File::exists(config("esea.paths.play360"))){
            die("File play360.json is not exsist");
        }

        $input = $request->has('input') ? $request->input('input') : "cloud";
        $quality = $request->input('quality');
        $show_jumbo = $request->input('show_jumbo');
        $show_jumbo = ($show_jumbo == null) ? 1 : $show_jumbo;

        if (!in_array($quality,['high','mid','low'])){
            $quality = "";
        }

        $default_player = $request->has("player") ? $request->input('player') : 'jw';

        if (!in_array($default_player,['bit','jw'])){
            $default_player = 'jw';
        }

        if ($input == "live"){
            $obj = File::get(config("esea.paths.live360"));
        }
        elseif($input == "cloud"){
            $obj = File::get(config("esea.paths.cloud360"));
        }
        elseif($input == "play"){
            $obj = File::get(config("esea.paths.play360"));
        }
        else{
            $obj = File::get(config("esea.paths.cloud360"));
        }

        $obj = json_decode($obj);
        if ($game_status >= 1000){
            return view("event.empty",['event'=>$obj]);
        }
        elseif($game_status == 100){
            $obj->main_stream_url = Helper::event_stream_generate_quality($obj->main_stream_url,$quality);
            $obj->sub_stream_url = Helper::event_stream_generate_quality($obj->sub_stream_url,$quality);
            return view("event.off_360_content",[
                'event'=>$obj,
                'show_jumbo'=>$show_jumbo,
                'default_player'=>$default_player,
                'current_map' => $game_status,
                'input' => $input,
                'quality' => $quality,
            ]);
        }
        else{
            $mmap_name_array = config("esea.files");

            $map_name = isset($mmap_name_array[$game_status]) ?  $mmap_name_array[$game_status] : "";
            if ($map_name == ""){
                return view("event.empty",['event'=>$obj]);
            }
            $map_config = $obj->minimap->$map_name;
            foreach ($map_config->camera as $key=>$item){
                $map_config->camera[$key]->url = Helper::event_stream_generate_quality($item->url,$quality);
            }

            $obj->main_stream_url = Helper::event_stream_generate_quality($obj->main_stream_url,$quality);
            $obj->sub_stream_url = Helper::event_stream_generate_quality($obj->sub_stream_url,$quality);

            $event_object = BoomEvent::find($obj->id)->first();
            return view("event.index",[
                'event'=>$obj,
                'map_config'=>$map_config,
                "event_object" => $event_object,
                'show_jumbo'=>$show_jumbo,
                'default_player'=>$default_player,
                'current_map' => $game_status,
                'input' => $input,
                'quality' => $quality,
            ]);
        }
    }

    public function checkMapChange(Request $request){


        $game_status = 1000;
        if ($this->boom_setting->get('game_status'))
        {
            $game_status = $this->boom_setting->get('game_status')->value;
        }

        $input = $request->has('input') ? $request->input('input') : "cloud";
        $quality = $request->input('quality');
        $current_map = $request->has('current_map') ? $request->input('current_map') : "1000";
        $camera_position = $request->has('camera_position') ? $request->input('camera_position') : 0;

        if (!in_array($quality,['high','mid','low'])){
            $quality = "";
        }

        if ($game_status >= 1000){
            return response()->json(['status'=>1000]);
        }
        elseif($game_status == 100){
            return response()->json(['status'=>100]);
        }
        else{
            if ($game_status == $current_map){
                return response()->json(['status'=>500]);
            }
            if ($input == "live"){
                $obj = File::get(config("esea.paths.live360"));
            }
            elseif($input == "cloud"){
                $obj = File::get(config("esea.paths.cloud360"));
            }
            elseif($input == "play"){
                $obj = File::get(config("esea.paths.play360"));
            }
            else{
                $obj = File::get(config("esea.paths.cloud360"));
            }

            $obj = json_decode($obj);
            $mmap_name_array = config("esea.files");

            $map_name = isset($mmap_name_array[$game_status]) ?  $mmap_name_array[$game_status] : "";

            $map_config = $obj->minimap->$map_name;
            foreach ($map_config->camera as $key=>$item){
                $map_config->camera[$key]->url = Helper::event_stream_generate_quality($item->url,$quality);
            }

            $obj->main_stream_url = Helper::event_stream_generate_quality($obj->main_stream_url,$quality);
            $obj->sub_stream_url = Helper::event_stream_generate_quality($obj->sub_stream_url,$quality);

            $html_content = view("event.map",[
                'event'=>$obj,
                'map_config'=>$map_config,
                'current_map' => $game_status,
                'camera_position' => $camera_position,
            ])->render();
            return response()->json(['status'=>1,'content'=>$html_content,'current_map'=>$game_status]);
        }
    }

    public function checkEventState(Request $request){
        $game_status = 1000;
        if ($this->boom_setting->get('game_status'))
        {
            $game_status = $this->boom_setting->get('game_status')->value;
        }
        if ($game_status >= 1000){
            return response()->json(['status'=>1000]);
        }
        else{
            return response()->json(['status'=>1]);
        }
    }

    public function incView(Request $request){
        $event_id = $request->input('id');
        $event = BoomEvent::where('id',$event_id)->get()->first();
        $update = ($request->input('update') != null) ? $request->input('update') : 0;
        if ($event){
            if ($update){
                $event->view_numb = $event->view_numb + 1;
                $event->save();
                return response()->json(['status'=>1,'view_numb'=>$event->view_numb]);
            }
            else{
                return response()->json(['status'=>1,'view_numb'=>$event->view_numb]);
            }

        }
        else{
            return response()->json(['status'=>0]);
        }
    }

    public function likeEvent(Request $request){
        $event_id = $request->input('id');
        $event = BoomEvent::where('id',$event_id)->get()->first();
        if ($event){
            $likes = $event->like_numb;

            $like_state = $request->session()->get('like_event_state.' . $event_id);
            if ($like_state['like_state'] == true && $like_state['event_id'] == $event_id) {
                if ($likes > 0) $likes--;
                $like_state = [
                    "event_id" => $event_id,
                    "like_state" => false
                ];
            } else {
                $likes++;
                $like_state = [
                    "event_id" => $event_id,
                    "like_state" => true
                ];
            }
            $request->session()->put('like_event_state.' . $event_id, $like_state);
            $event->like_numb = $likes;

            $event->save();

            return response()->json(['status'=>1,'like_numb'=>$event->like_numb]);
        }
        else{
            return response()->json(['status'=>0,'like_numb'=>0]);
        }
    }
}
