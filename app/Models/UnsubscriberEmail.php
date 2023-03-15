<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Hash;

class UnsubscriberEmail extends Model
{
    //
    protected $fillable = ['email','user_id','status','type'];

    protected $table = "unsubscriber_emails";

    const TYPE_MONTAGE = 1;
    const TYPE_CHURN = 0;

    public static function getUnsubscriberEmailLink($code,$type=1){
        $user = User::where('code',$code)->first();

        if (!$user){
            return "";
        }

        return route("unsubscribe",['code'=>$code,'type'=>$type,'key'=>static::createValidKey($user)]);
    }

    public static function checkValidateKey($code,$key){
        $user = User::where('code',$code)->first();
        if (!$user){
            return 0;
        }
        return static::checkValidKey($key,$user);
    }

    public static function createValidKey($user){
        return Hash::make("boom.tv".$user->code.$user->id);
    }

    public static function checkValidKey($key,$user){
        return Hash::check("boom.tv".$user->code.$user->id,$key);
    }

    public static function checkIfCanSendEmail($user,$type=1){
        $obj = static::where('user_id',$user->id)->where('type',$type)->first();
        if ($obj){
            if ($obj->status == 1){
                return false;
            }
            else{
                return true;
            }
        }
        else{
            return true;
        }
    }
}
