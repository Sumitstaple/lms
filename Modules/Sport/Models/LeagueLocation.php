<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;

class LeagueLocation extends Model
{
	protected $table = 'lms_league_location';
    protected $fillable = ['user_id','league_town','league_city','league_state', 'league_country'];
}
