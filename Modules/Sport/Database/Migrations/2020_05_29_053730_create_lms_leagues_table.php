<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_leagues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('league_name');
            $table->enum('type', array('lms','lml'));
            $table->unsignedBigInteger('sport_id');
            $table->string('round_to_start');
            $table->dateTime('start_datetime', 0);
            $table->dateTime('end_datetime', 0);
            $table->enum('status', array('active','de-active'))->default('active');
            $table->enum('is_private', array('yes','no'));
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sport_id')->references('id')->on('lms_sports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_leagues');
    }
}
