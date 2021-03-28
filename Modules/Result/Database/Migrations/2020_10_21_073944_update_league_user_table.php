<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeagueUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_league_users', function (Blueprint $table) {
         $table->enum('crn_forfeit_checked', array('yes','no'))->default('no');
         $table->enum('crn_result_process', array('yes','no'))->default('no');
         //$table->enum('crn_last_result_process', array('yes','no'))->default('no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_league_users', function (Blueprint $table) {
         $table->dropColumn(['crn_result_process','crn_last_result_process','crn_forfeit_checked']);
        });
    }
}
