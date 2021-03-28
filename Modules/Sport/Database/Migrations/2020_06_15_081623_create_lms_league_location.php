<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsLeagueLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_league_location', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('league_id');
            $table->string('league_town');
            $table->string('league_city');
            $table->string('league_state');
            $table->string('league_country');
            $table->timestamps();
            $table->foreign('league_id')->references('id')->on('lms_leagues')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_league_location');
    }
}
