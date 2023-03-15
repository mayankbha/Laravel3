<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\UserReminder;
use App\Models\UserReminderMailLog;
use App\Helpers\Helper;
use Session;

class ReminderController extends Controller
{
    //
    public function churnLog(Request $request){
        $input_data = $request->all();
        $page = Helper::pageValidate($request);
        $limit = 50;
        $offset = ($page-1)*50;
        $s = $request->input("s");
        if ($s == null){
            $list_reminder = UserReminder::with("user")->where('type',UserReminder::TYPE_CHURN)->orderBy("created_at","desc")->paginate($limit);
        }
        else{
            $list_reminder = UserReminder::with("user")->whereHas('user',function ($query) use ($s) {
                $query->where("name",'like',"%{$s}%");
            } )->where('type',UserReminder::TYPE_CHURN)->orderBy("created_at","desc")->paginate($limit);
        }
        $list_reminder->setPath(route("admin.reminder.churn"));
        if ($s != null){
            $list_reminder->setPath(route("admin.reminder.churn",['s'=>$s]));
        }

        $reminder_msg = Session::pull('reminder_msg');
        return view('admin.reminder.index',[
            'list_reminder' => $list_reminder,
            'msg' => $reminder_msg,
            'input_data' => $input_data,
        ]);
    }

    public function churnEmailReport(Request $request){
        $total_email_send = UserReminderMailLog::where('transmission_id','!=','')->count();
        $total_email_with_open_or_click = UserReminderMailLog::where('transmission_id','!=','')->whereIn('sparkpost_status',['open','click'])->count();

        return view('admin.reminder.email_report',[
            'total_email_send' => $total_email_send,
            'total_email_with_open_or_click' => $total_email_with_open_or_click,
        ]);
    }

    public function getEmailLog(Request $request){
        $id = $request->input('id');
        $reminder = UserReminder::find($id);
        if (!$reminder){
            return response()->json(['status'=>1,'content'=>"Reminder is not exist"]);
        }
        $list_email_log = UserReminderMailLog::where('user_reminder_id',$id)->orderBy('updated_at','desc')->get();
        $content = view('admin.reminder.email-log',['list_email_log'=>$list_email_log])->render();
        return response()->json(['status'=>0,'content'=>$content]);
    }

    public function startReminder(Request $request){
        $id = $request->input('id');
        $reminder = UserReminder::find($id);
        if (!$reminder){
            $reminder_msg = Session::put('reminder_msg',"Reminder is not exist");
            return redirect()->back();
        }
        if ($reminder->current_status == UserReminder::CURRENT_STATUS_START_REMIND){
            Session::put('reminder_msg',"Success start remind with user_id: {$reminder->id}, user_email: {$reminder->email}.");
            UserReminder::dispatchStartUserReminderJob($reminder);
            return redirect()->back();
        }
        else{
            Session::put('reminder_msg',"Reminder with id {$reminder->id} is processing");
            return redirect()->back();
        }
    }
    public function reportUserComeback()
    {
        $datas = UserReminder::getDataUserComeBack();
        return view('admin.reminder.comeback', ["datas" => $datas]);
    }
}
