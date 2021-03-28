<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_sports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sport_name');
            $table->string('sport_icon');
            $table->bigInteger('added_by');
            $table->enum('is_active', array('yes','no'));
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
        Schema::dropIfExists('lms_sports');
    }
}
