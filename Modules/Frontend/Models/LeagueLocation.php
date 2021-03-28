<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Model;

class LeagueLocation extends Model
{
	protected $table = 'lms_league_location';
    protected $fillable = ['user_id','league_town','league_city','league_state', 'league_country'];
}
