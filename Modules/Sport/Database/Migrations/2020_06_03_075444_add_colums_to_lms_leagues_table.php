<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumsToLmsLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_leagues', function (Blueprint $table) {
            $table->string('town',255);
            $table->string('city',255);
            $table->string('state',255);
            $table->string('country',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_leagues', function (Blueprint $table) {

        });
    }
}
