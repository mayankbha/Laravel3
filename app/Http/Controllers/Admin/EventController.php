<?php

namespace App\Http\Controllers\Admin;

use App\Events\Event;
use App\Helpers\AWSHelper;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use File;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\EventVod;
use Validator;
use App\Models\EventVodLiveUrl;

class EventController extends Controller
{
    //
    public function index(Request $request)
    {

        $game_status = 1000;

        Setting::firstCreate([
            'name' => "game_status",
            'title' => "Game status",
            'value' => '',
            'scope' => 'event',
        ]);
        $this->boom_setting = Setting::all_setting();

        if ($this->boom_setting->get('game_status')) {
            $game_status = $this->boom_setting->get('game_status')->value;
        }
        Setting::firstCreate([
            'name' => "next_event_date",
            'title' => "Next event date",
            'value' => '',
            'scope' => 'event',
        ]);
        $mmap_name_array = config("esea.files");
        $next_event_date = Setting::where('name', "next_event_date")->first();

        return view('admin.event.index', ['map_array' => $mmap_name_array, 'game_status' => $game_status, 'next_event_date' => $next_event_date]);
    }

    public function setEventStatus(Request $request)
    {

        $game_status = $request->has("game_status") ? $request->input("game_status") : 1000;
        $map_name_array = config("esea.files");
        $map_name_array[1000] = "offline";
        if (!key_exists($game_status, $map_name_array)) {
            abort(403);
        }
        $setting = Setting::where('name', 'game_status')->first();
        $setting->value = $game_status;
        $setting->scope = 'event';
        $setting->save();

        $event_config_array = ['next_event_date', 'team_name','game_name', 'event_map_change', 'allow_ip_list', 'comingsoon_date', 'comingsoon_game_name', 'comingsoon_team_name'];
        foreach ($event_config_array as $item) {
            $value = $request->input($item);
            $setting = Setting::where('name', $item)->first();
            $setting->value = $value;
            $setting->scope = 'event';
            $setting->save();
        }
        return 1;
    }

    public function getListEventVod(Request $request)
    {
        $list_vod = EventVod::all();
        $data['list_vod'] = $list_vod;
        return view('admin.event.list_vod', ['data' => $data]);
    }

    public function getAddVod(Request $request)
    {
        $mmap_name_array = config("esea.files");
        $data['map_name_array'] = $mmap_name_array;
        $name_array = ['set_a', 'set_b'];
        $list_live_urls = [];
        foreach ($name_array as $value) {

            $item = [
                'jumbotron' => "",
                'caster' => "",
                'live_1' => "",
                'live_2' => "",
                'live_3' => "",
                'live_4' => "",
            ];
            $item = json_decode(\GuzzleHttp\json_encode($item));

            $list_live_urls[$value] = $item;
        }
        return view('admin.event.add_vod', ['data' => $data, "list_live_urls" => $list_live_urls]);
    }

    public function postAddVod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:event_vods|max:255',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.event.vod.add'))
                ->withErrors($validator)
                ->withInput();
        }
        $name = $request->input('name');
        $game_name = $request->input('game_name');
        $team_name = $request->input('team_name');
        $map_id = $request->input('map_id');
        $vod_date = $request->input("vod_date");

        $mmap_name_array = config("esea.files");

        if (!isset($mmap_name_array[$map_id])) {
            $map_id = 1;
            $map_name = $mmap_name_array[$map_id];
        } else {
            $map_name = $mmap_name_array[$map_id];
        }

        $thumbnail = "";
        $update_thumnail = $request->input('update_thumbnail');
        if ($update_thumnail) {
            $file_thumnail = $request->file("file_thumb");
            if ($request->hasFile('file_thumb')) {
                $upload_content = AWSHelper::uploadReportToS3($file_thumnail->getPathname(), 'public/assets/event-vods/', $file_thumnail->getClientOriginalName(), "boomtv-contents");
                $thumbnail = $upload_content['links3'];
            } else {
                $thumbnail = "";
            }
        } else {
            $thumbnail = $request->input('thumbnail');
        }
        $vod = new EventVod();
        $vod->name = $name;
        $vod->game_name = $game_name;
        $vod->team_name = $team_name;
        $vod->map_id = $map_id;
        $vod->map_name = $map_name;
        $vod->vod_date = $vod_date;
        $vod->thumbnail = $thumbnail;
        $vod->save();

        $set_a = $request->input('set_a');
        if ($set_a == null){
            $set_a = [];
        }
        $set_live_url = new EventVodLiveUrl();
        $set_live_url->name = "set_a";
        $set_live_url->url = implode(',',$set_a);
        $set_live_url->event_vod_id = $vod->id;
        $set_live_url->save();

        $set_b = $request->input('set_b');
        if ($set_b == null){
            $set_b = [];
        }
        $set_live_url = new EventVodLiveUrl();
        $set_live_url->name = "set_b";
        $set_live_url->url = implode(',',$set_b);
        $set_live_url->event_vod_id = $vod->id;
        $set_live_url->save();

        return redirect(route('admin.event.vod.list'))->with(['vod_msg' => "Add success!"]);
        // Store the blog post...
    }

    public function getEditVod(Request $request)
    {
        $id = $request->input('id');
        $vod = EventVod::find($id);
        if (!$vod) {
            return abort(404);
        }

        $list_360 = explode(',', $vod->vod_360_url);
        $vod->live_1 = isset($list_360[0]) ? trim($list_360[0]) : "";
        $vod->live_2 = isset($list_360[1]) ? trim($list_360[1]) : "";
        $vod->live_3 = isset($list_360[2]) ? trim($list_360[2]) : "";
        $vod->live_4 = isset($list_360[3]) ? trim($list_360[3]) : "";

        $data['vod'] = $vod;
        $mmap_name_array = config("esea.files");
        $data['map_name_array'] = $mmap_name_array;
        $name_array = ['set_a', 'set_b'];
        $list_live_urls = [];
        foreach ($name_array as $value) {
            $item = EventVodLiveUrl::where('event_vod_id', $vod->id)->where('name', $value)->first();
            if ($item) {
                $tmp = explode(",", $item->url);
                $item->jumbotron = isset($tmp[0]) ? $tmp[0] : "";
                $item->caster = isset($tmp[1]) ? $tmp[1] : "";
                $item->live_1 = isset($tmp[2]) ? $tmp[2] : "";
                $item->live_2 = isset($tmp[3]) ? $tmp[3] : "";
                $item->live_3 = isset($tmp[4]) ? $tmp[4] : "";
                $item->live_4 = isset($tmp[5]) ? $tmp[5] : "";
            } else {
                $item = [
                    'jumbotron' => "",
                    'caster' => "",
                    'live_1' => "",
                    'live_2' => "",
                    'live_3' => "",
                    'live_4' => "",
                ];
                $item = json_decode(\GuzzleHttp\json_encode($item));
            }

            $list_live_urls[$value] = $item;
        }
        return view('admin.event.edit_vod', ['data' => $data, 'list_live_urls' => $list_live_urls]);
    }

    public function postEditVod(Request $request)
    {
        $id = $request->input('id');
        $vod = EventVod::find($id);
        if (!$vod) {
            return abort(404);
        }
        $name = $request->input('name');
        $game_name = $request->input('game_name');
        $team_name = $request->input('team_name');
        $map_id = $request->input('map_id');
        $vod_date = $request->input("vod_date");

        $mmap_name_array = config("esea.files");

        if (!isset($mmap_name_array[$map_id])) {
            $map_id = 1;
            $map_name = $mmap_name_array[$map_id];
        } else {
            $map_name = $mmap_name_array[$map_id];
        }

        $thumbnail = $vod->thumbnail;
        $update_thumnail = $request->input('update_thumbnail');
        if ($update_thumnail) {
            $file_thumnail = $request->file("file_thumb");
            if ($request->hasFile('file_thumb')) {
                $upload_content = AWSHelper::uploadReportToS3($file_thumnail->getPathname(), 'public/assets/event-vods/', $file_thumnail->getClientOriginalName(), "boomtv-contents");
                $thumbnail = $upload_content['links3'];
            } else {
                $thumbnail = "";
            }
        } else {
            $thumbnail = $request->input('thumbnail');
        }
        $vod->name = $name;
        $vod->game_name = $game_name;
        $vod->team_name = $team_name;
        $vod->map_id = $map_id;
        $vod->map_name = $map_name;
        $vod->vod_date = $vod_date;
        $vod->thumbnail = $thumbnail;
        $vod->save();

        $set_a = $request->input('set_a');
        if ($set_a == null){
            $set_a = [];
        }
        $set_live_url = EventVodLiveUrl::where('name','set_a')->where('event_vod_id',$vod->id)->first();
        if (!$set_live_url){
            $set_live_url = new EventVodLiveUrl();
        }
        $set_live_url->name = "set_a";
        $set_live_url->url = implode(',',$set_a);
        $set_live_url->event_vod_id = $vod->id;
        $set_live_url->save();

        $set_b = $request->input('set_b');
        if ($set_b == null){
            $set_b = [];
        }
        $set_live_url = EventVodLiveUrl::where('name','set_b')->where('event_vod_id',$vod->id)->first();
        if (!$set_live_url){
            $set_live_url = new EventVodLiveUrl();
        }
        $set_live_url->name = "set_b";
        $set_live_url->url = implode(',',$set_b);
        $set_live_url->event_vod_id = $vod->id;
        $set_live_url->save();

        return redirect(route('admin.event.vod.list'))->with(['vod_msg' => "Edit success!"]);
    }

    public function removeVod(Request $request)
    {
        $id = $request->input('id');
        $vod = EventVod::find($id);
        if (!$vod) {
            $msg = "Vod not found";
        } else {
            $vod->delete();
            $msg = "success remove vod!";
        }
        return redirect(route('admin.event.vod.list'))->with(['vod_msg' => $msg]);
    }

    public function showEventSetOfUrl(Request $request)
    {
        $list_set_url = Setting::where('scope', 'event-vod-url')->get();
        $data = array();
        foreach ($list_set_url as $key => $item) {
            $tmp = explode(",", $item->value);
            $item->jumbotron = isset($tmp[0]) ? $tmp[0] : "";
            $item->caster = isset($tmp[1]) ? $tmp[1] : "";
            $item->live_1 = isset($tmp[2]) ? $tmp[2] : "";
            $item->live_2 = isset($tmp[3]) ? $tmp[3] : "";
            $item->live_3 = isset($tmp[4]) ? $tmp[4] : "";
            $item->live_4 = isset($tmp[5]) ? $tmp[5] : "";
            $data[$key] = $item;
        }
        return view("admin.event.edit_set_url", ['data' => $data]);
    }

    public function postEventSetOfUrl(Request $request)
    {
        $set_of_a = $request->input('set_url_a');
        $set_of_b = $request->input('set_url_b');
        if ($set_of_a == null) {
            $set_of_a = [];
        }
        if ($set_of_b == null) {
            $set_of_b = [];
        }
        $setting = Setting::where('name', 'set_url_a')->first();
        $setting->value = implode(",", $set_of_a);
        $setting->scope = 'event-vod-url';
        $setting->save();

        $setting = Setting::where('name', 'set_url_b')->first();
        $setting->value = implode(",", $set_of_b);
        $setting->scope = 'event-vod-url';
        $setting->save();

        return redirect()->to(route('admin.event.setOfUrl'));
    }

    public function getAddLiveUrl(Request $request)
    {
        $id = $request->input('id');
        $vod = EventVod::find($id);
        if (!$vod) {
            return abort(403);
        }
        $back = $request->input('back');
        return view("admin.event.add_live_url", ['vod' => $vod, 'back' => $back]);
    }

    public function postAddLiveUrl(Request $request)
    {
        $id = $request->input('id');
        $vod = EventVod::find($id);
        if (!$vod) {
            return abort(403);
        }
        $url = $request->input('url');
        $event_vod_live_url = new EventVodLiveUrl();
        $event_vod_live_url->event_vod_id = $vod->id;
        $event_vod_live_url->url = implode(",", $url);
        $event_vod_live_url->save();

        $back = $request->input('back');
        return redirect($back)->with(['vod_msg' => 'Add live urls success!']);
    }

    public function getEditLiveUrl(Request $request)
    {
        $id = $request->input('id');
        $vod_live_url = EventVodLiveUrl::find($id);
        if (!$vod_live_url) {
            return abort(403);
        }
        $tmp = explode(",", $vod_live_url->url);
        $vod_live_url->jumbotron = isset($tmp[0]) ? $tmp[0] : "";
        $vod_live_url->caster = isset($tmp[1]) ? $tmp[1] : "";
        $vod_live_url->live_1 = isset($tmp[2]) ? $tmp[2] : "";
        $vod_live_url->live_2 = isset($tmp[3]) ? $tmp[3] : "";
        $vod_live_url->live_3 = isset($tmp[4]) ? $tmp[4] : "";
        $vod_live_url->live_4 = isset($tmp[5]) ? $tmp[5] : "";
        $back = $request->input('back');
        return view("admin.event.edit_live_url", ['vod_live_url' => $vod_live_url, 'back' => $back]);
    }

    public function postEditLiveUrl(Request $request)
    {
        $id = $request->input('id');
        $vod_live_url = EventVodLiveUrl::find($id);
        if (!$vod_live_url) {
            return abort(403);
        }
        $url = $request->input('url');
        $vod_live_url = new EventVodLiveUrl();
        $vod_live_url->url = implode(",", $url);
        $vod_live_url->save();

        $back = $request->input('back');
        return redirect($back)->with(['vod_msg' => 'Edit live urls success!']);
    }

    public function removeLiveUrl(Request $request)
    {
        $id = $request->input('id');
        $vod_live_url = EventVodLiveUrl::find($id);
        if (!$vod_live_url) {
            return abort(403);
        }
        $vod_live_url->delete();
        $back = $request->input('back');
        return redirect($back)->with(['vod_msg' => 'Remove live urls success!']);
    }

}
