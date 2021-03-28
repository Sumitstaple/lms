<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Frontend\Models\Frontend;

class LeagueCommentStatus extends Model
{
	protected $table = 'lms_league_comment_status';
  protected $fillable = ['league_id','user_id','round_id', 'comment_count'];

} 