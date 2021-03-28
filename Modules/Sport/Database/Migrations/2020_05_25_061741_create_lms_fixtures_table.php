<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsFixturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_fixtures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('round_id');
            $table->string('fixture_name');
            $table->dateTime('match_datetime', 0);
            $table->unsignedBigInteger('home_team_id');
            $table->unsignedBigInteger('away_team_id');
            $table->unsignedBigInteger('winner_team_id')->nullable();
            $table->unsignedBigInteger('looser_team_id')->nullable();
            $table->enum('fixure_result', array('draw','cancel'))->nullable();
            $table->bigInteger('added_by');
            $table->timestamps();
            $table->foreign('sport_id')->references('id')->on('lms_sports')->onDelete('cascade');
            $table->foreign('round_id')->references('id')->on('lms_rounds')->onDelete('cascade');
            $table->foreign('home_team_id')->references('id')->on('lms_teams')->onDelete('cascade');
            $table->foreign('away_team_id')->references('id')->on('lms_teams')->onDelete('cascade');
            $table->foreign('winner_team_id')->references('id')->on('lms_teams')->onDelete('cascade');
            $table->foreign('looser_team_id')->references('id')->on('lms_teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_fixtures');
    }
}
