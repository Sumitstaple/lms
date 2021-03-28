<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsLeagueCommentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_league_comment_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('league_id');
            $table->bigInteger('round_id');
            $table->bigInteger('user_id');
            $table->bigInteger('comment_count');
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
        Schema::dropIfExists('lms_league_comment_status');
    }
}
