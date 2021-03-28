<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLmsManageTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_manage_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('rank_order');
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
        Schema::dropIfExists('lms_manage_teams');
    }
}
