<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumsToLmsLeagueCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_league_comments', function (Blueprint $table) {
            $table->bigInteger('sub_parent_id')->default(0)->after('parent_comment_id');
            $table->bigInteger('sub_sub_parent_id')->default(0)->after('sub_parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_league_comments', function (Blueprint $table) {

        });
    }
}
