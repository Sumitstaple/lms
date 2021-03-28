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
         $table->string('result_stats')->nullable();
         $table->enum('is_forfet_knokout', array('yes','no'))->default('no');
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
        	$table->dropColumn(['result_stats','is_forfet_knokout']);
        });
    }
}
