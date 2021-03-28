<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateResultStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_user_stats', function (Blueprint $table) {
          $table->string('goal_difference')->nullable();
          $table->string('final_goal_difference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_user_stats', function (Blueprint $table) {
          $table->dropColumn(['goal_difference','final_goal_difference']);
        });
    }
}
