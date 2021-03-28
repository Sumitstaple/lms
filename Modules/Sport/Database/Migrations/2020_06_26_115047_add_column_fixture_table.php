<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFixtureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('lms_fixtures', function (Blueprint $table) {
            $table->enum('fixure_result_status', array('pending','complete','draw','abandon'))->default('pending');
            $table->enum('is_valid', array('y','n'))->default('y');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('lms_fixtures', function (Blueprint $table) {
            $table->dropColumn('fixure_result_status');
            $table->dropColumn('is_valid');

        });
    }
}
