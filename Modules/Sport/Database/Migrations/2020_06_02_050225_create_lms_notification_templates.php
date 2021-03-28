<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLmsNotificationTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_notification_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',255);
            $table->string('slug',255);
            $table->string('mode', 100);
            $table->string('type',255);
            $table->longText('long_description');
            $table->char('is_active',100);
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
        Schema::dropIfExists('lms_notification_templates');
    }
}
