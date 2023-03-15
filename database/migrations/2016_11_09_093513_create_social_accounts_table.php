<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use App\Models\SocialAccount;
class CreateSocialAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('social_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('social_id');
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('expries')->nullable();
            $table->string('type');
            $table->timestamps();
        });

         $user=User::where('uid',"<>",null)->get();
         foreach ($user as $key => $value) {
            $social=SocialAccount::where('user_id',$value->id)->get();
            if($social==null)
                SocialAccount::create([
                    "user_id"=>$value->id,
                    "social_id"=>$value->uid
                    ]);
            else
                SocialAccount::where('user_id',$value->id)->update([
                    "user_id"=>$value->id,
                    "social_id"=>$value->uid
                    ]);

         }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('social_accounts');
    }
}
