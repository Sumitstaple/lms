<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
	protected $table = 'lms_sports';
    protected $fillable = ['sport_name', 'sport_icon','added_by'];
}
