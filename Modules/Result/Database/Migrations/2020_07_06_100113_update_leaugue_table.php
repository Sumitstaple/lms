<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeaugueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_leagues', function (Blueprint $table) {
            $table->unsignedBigInteger('current_round_id');
            $table->foreign('current_round_id')->references('id')->on('lms_rounds')->onDelete('cascade');
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
          $table->dropColumn(['current_round_id']);
        });
    }
}
