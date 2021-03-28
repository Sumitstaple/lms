<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFixtureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_fixtures', function (Blueprint $table) {
          $table->integer('home_team_score')->nullable();
          $table->integer('away_team_score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_fixtures', function (Blueprint $table) {
         $table->dropColumn(['home_team_score','away_team_score']);
        });
    }
}
