<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LmsUserInvitation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('lms_user_invitation', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('email');
          $table->string('mode');
          $table->bigInteger('added_by');
          $table->unsignedBigInteger('leagues_id');
          $table->enum('invitation_status', array('sent','received','approved'));
          $table->timestamps();
          $table->foreign('leagues_id')->references('id')->on('lms_leagues')->onDelete('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_user_invitations');
    }
}
