<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsLeagueUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_league_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('user_id');
            $table->string('is_admin');
            $table->string('is_team_pickup');
            $table->enum('status', array('request_pending','active','rejected'));
            $table->timestamps();
            $table->foreign('league_id')->references('id')->on('lms_leagues')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_league_users');
    }
}
