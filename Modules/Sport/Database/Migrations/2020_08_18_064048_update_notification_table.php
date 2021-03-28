<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_user_notifications', function (Blueprint $table) {
         $table->string('type',155)->nullable();
         $table->string('target_tbl',155)->nullable();
         $table->string('target_column',155)->nullable();
         $table->string('target_id',155)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_user_notifications', function (Blueprint $table) {

        });
    }
}
