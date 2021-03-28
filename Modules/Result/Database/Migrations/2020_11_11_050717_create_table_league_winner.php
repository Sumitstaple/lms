<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLeagueWinner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_league_winner_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('round_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('league_type', array('lms','lml'))->nullable();
            $table->enum('status', array('w','l','d'))->nullable();
            $table->enum('is_sent', array('yes','no'))->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_league_winner_summary');
    }
}
