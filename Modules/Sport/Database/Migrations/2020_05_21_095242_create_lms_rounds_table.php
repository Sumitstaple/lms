<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_rounds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sport_id');
            $table->foreign('sport_id')->references('id')->on('lms_sports')->onDelete('cascade');
            $table->string('round_name');
            $table->bigInteger('round_number');
            $table->longText('round_description');
            $table->dateTime('start_datetime', 0);
            $table->dateTime('end_datetime', 0);
            $table->bigInteger('added_by');
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
        Schema::dropIfExists('lms_rounds');
    }
}
