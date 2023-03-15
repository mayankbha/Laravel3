<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('subscriptions', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('streamer_id')->unsigned();
			$table->foreign('streamer_id')->references('id')->on('users');
            $table->integer('subscriber_id')->unsigned();
			$table->foreign('subscriber_id')->references('id')->on('users');
            $table->integer('status')->default(1)->comment = "0=Unsubscribed,1=Subscribed";
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
