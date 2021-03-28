<?php

namespace Modules\Result\Models;

use Illuminate\Database\Eloquent\Model;

class userStatsUndu extends Model
{
	protected $table = 'lms_result_undo_stats';
	protected $fillable = ['fixture_stats','user_stats','round_id','last_result_stats'];
}
