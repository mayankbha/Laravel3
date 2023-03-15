<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocialFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('social_follows', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
            $table->integer('social_account_type')->default(0)->comment = "0=Twitch,2=Mixer,3=Youtube";
            $table->integer('recommended_streamer_id')->unsigned();
			$table->foreign('recommended_streamer_id')->references('id')->on('users');
            $table->integer('followers')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('subscriptions', function(Blueprint $table)
		{
			$table->dropPrimary('subscriptions_id_primary');	
			$table->dropForeign('subscriptions_streamer_id_foreign');
			$table->dropForeign('subscriptions_subscriber_id_foreign');
		});
    }
}
