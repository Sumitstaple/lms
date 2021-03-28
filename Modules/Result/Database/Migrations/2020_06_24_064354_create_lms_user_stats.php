<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsUserStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_user_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->integer('debit_point');
            $table->integer('credit_point');
            $table->integer('total_points');
            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('round_id');
            $table->unsignedBigInteger('fixture_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('league_id')->references('id')->on('lms_leagues')->onDelete('cascade');	
            $table->foreign('round_id')->references('id')->on('lms_rounds')->onDelete('cascade');
            $table->foreign('fixture_id')->references('id')->on('lms_fixtures')->onDelete('cascade');	
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
        Schema::dropIfExists('lms_user_stats');
    }
}
