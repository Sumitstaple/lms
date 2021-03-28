<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRoundStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_rounds', function (Blueprint $table) {
          $table->enum('result_process_status', array('pending','wait_for_cron_process','running','complete'))->default('pending');
          $table->enum('notification_status', array('pending','running','complete'))->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('lms_rounds', function (Blueprint $table) {
          $table->dropColumn(['result_process_status','notification_status']);
        });
    }
}
