<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatedRoundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('lms_rounds', function (Blueprint $table) {
            $table->enum('process_status', array('pending','progress','complete'))->default('pending');
            $table->enum('is_flip_over_round', array('yes','no'))->default('no');
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
          $table->dropColumn(['process_status','is_flip_over_round']);
        });
    }
}
