<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRoundTableUnduColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_rounds', function (Blueprint $table) {
         $table->enum('is_undu_able', array('no','yes','running'))->default('no');
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
          $table->dropColumn(['is_undu_able']);
        });
    }
}
