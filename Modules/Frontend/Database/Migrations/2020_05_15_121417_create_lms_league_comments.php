<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsLeagueComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_league_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('league_id');
            $table->bigInteger('round_id');
            $table->longText('comment');
            $table->bigInteger('user_id');
            $table->bigInteger('parent_comment_id');
            $table->enum('status', array('active','deactive'))->default('active');
            $table->enum('is_edit', array('no','yes'))->default('no');
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
        Schema::dropIfExists('lms_league_comments');
    }
}
