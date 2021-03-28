<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;

class Round extends Model
{
	protected $table = 'lms_rounds';
    protected $fillable = ['user_id','is_private','sport_id', 'round_to_start','league_name','town','city','state','country','type','start_datetime','end_datetime'];

}
