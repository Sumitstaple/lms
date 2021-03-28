<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLocationFileds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_league_location', function (Blueprint $table) {   
            $table->string('league_town')->nullable()->change();
            $table->string('league_city')->nullable()->change();
            $table->string('league_state')->nullable()->change();
            $table->string('league_country')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_league_location', function (Blueprint $table) {

        });
    }
}
