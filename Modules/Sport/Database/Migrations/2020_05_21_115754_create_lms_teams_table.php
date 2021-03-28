<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('lms_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sport_id');
             $table->foreign('sport_id')->references('id')->on('lms_sports');
            $table->string('team_name');
            $table->string('team_icon');
            $table->bigInteger('added_by');
            $table->enum('is_active', array('yes','no'));
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
        Schema::dropIfExists('lms_teams');
    }
}
