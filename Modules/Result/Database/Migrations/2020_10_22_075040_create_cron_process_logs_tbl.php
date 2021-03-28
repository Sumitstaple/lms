<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronProcessLogsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lm_cron_process_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('process_name')->nullable();
            $table->enum('process_status', array('pending','running','complete'))->default('pending');
            $table->enum('last_batch_status', array('pending','running','complete'))->default('pending');
            $table->unsignedBigInteger('round_id')->nullable();
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
        Schema::dropIfExists('lm_cron_process_logs');
    }
}
