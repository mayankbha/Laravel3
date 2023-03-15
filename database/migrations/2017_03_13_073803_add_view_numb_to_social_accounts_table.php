<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddViewNumbToSocialAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->integer('view_numb')->nullable()->default(0);
            $table->integer('follower_numb')->nullable()->default(0);
            $table->integer('following_numb')->nullable()->default(0);
            $table->integer('subscriber_numb')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropColumn('view_numb');
            $table->dropColumn('follower_numb');
            $table->dropColumn('following_numb');
            $table->dropColumn('subscriber_numb');
        });
    }
}
