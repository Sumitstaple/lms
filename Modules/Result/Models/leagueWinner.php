<?php

namespace Modules\Result\Models;

use Illuminate\Database\Eloquent\Model;

class leagueWinner extends Model
{
	protected $table = 'lms_league_winner_summary';
	protected $fillable = ['league_id','round_id','user_id','league_type','status'];
}
