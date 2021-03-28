<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamLeauge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_league_user_teams', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('user_id');
          $table->unsignedBigInteger('league_id');
          $table->unsignedBigInteger('round_id');
          $table->unsignedBigInteger('team_id');
          $table->unsignedBigInteger('fixture_id');
          $table->enum('status', array('w','l','d'))->nullable()->comment('w-win,l-loss,d-draw');
          $table->timestamps();
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
          $table->foreign('league_id')->references('id')->on('lms_leagues')->onDelete('cascade');
          $table->foreign('round_id')->references('id')->on('lms_rounds')->onDelete('cascade');
          $table->foreign('team_id')->references('id')->on('lms_teams')->onDelete('cascade');
          $table->foreign('fixture_id')->references('id')->on('lms_fixtures')->onDelete('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_league_user_teams');
    }
}
