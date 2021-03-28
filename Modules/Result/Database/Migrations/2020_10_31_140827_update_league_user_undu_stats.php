<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeagueUserUnduStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_league_users', function (Blueprint $table) {
         $table->text('last_round_stats')->nullable();
         $table->enum('crn_undo_done', array('yes','no'))->default('no');
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
          $table->dropColumn(['last_round_stats','crn_undo_done']);
        });
    }
}
