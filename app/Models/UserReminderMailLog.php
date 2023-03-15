<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReminderMailLog extends Model
{
    //
    protected $fillable = ['user_id','user_reminder_id','template_id','email','current_status'];

    const CURRENT_STATUS_CREATED = 0;
    const CURRENT_STATUS_SENT = 1;
    const CURRENT_STATUS_UNSUBSCRIBE = 100;

    public function templateToString(){
        switch ($this->template_id){
            case 0 :
                $template_id = "we-miss-you-1";
                break;
            case 1 :
                $template_id = "we-miss-you-2";
                break;
            case 2 :
                $template_id = "we-miss-you-3";
                break;
            case 3 :
                $template_id = "we-miss-you-4";
                break;
            default :
                break;
        }
        return $template_id;
    }

    public function currentStatusToString(){
        switch ($this->current_status){
            case static::CURRENT_STATUS_CREATED :
                $template_id = "Created";
                break;
            case static::CURRENT_STATUS_SENT :
                $template_id = "Sent";
                break;
            case static::CURRENT_STATUS_UNSUBSCRIBE :
                $template_id = "Not sent, unsubscribe";
                break;
            default :
                break;
        }
        return $template_id;
    }

}

