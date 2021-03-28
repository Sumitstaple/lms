<?php

namespace Modules\Result\Models;

use Illuminate\Database\Eloquent\Model;

class mailNotification extends Model
{
	protected $table = 'lms_mail_notification';
	protected $fillable = ['user_id','league_id','message'];
}
