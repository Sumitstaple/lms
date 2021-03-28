<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UndoResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('lms_result_undo_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('fixture_stats')->nullable();
            $table->text('user_stats')->nullable();
            $table->unsignedBigInteger('round_id');
            $table->text('last_result_stats')->nullable();
            $table->foreign('round_id')->references('id')->on('lms_rounds')->onDelete('cascade');
            $table->enum('active', array('yes','no'))->default('no');
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
        Schema::dropIfExists('lms_result_undo_stats');
    }
}
