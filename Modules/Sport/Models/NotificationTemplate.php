<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;

class NotificationTemplate extends Model
{
	protected $table = 'lms_notification_templates';
    protected $fillable = ['name', 'slug','mode','type','long_description','is_active'];

}
