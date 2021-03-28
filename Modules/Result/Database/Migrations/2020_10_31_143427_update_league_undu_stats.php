<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeagueUnduStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_leagues', function (Blueprint $table) {
         $table->text('last_round_id')->nullable();
         $table->enum('crn_undo_done', array('yes','no'))->default('no');
         $table->enum('is_winner_calculated', array('yes','no'))->default('no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_leagues', function (Blueprint $table) {
         $table->dropColumn(['last_round_id','crn_undo_done','is_winner_calculated']);
        });
    }
}
