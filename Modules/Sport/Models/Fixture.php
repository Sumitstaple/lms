<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;

use Modules\Sport\Models\Sport;

class Fixture extends Model
{
	protected $table = 'lms_fixtures';
    protected $fillable = ['fixture_name','sport_id','round_id','match_datetime','home_team_id','away_team_id','winner_team_id','looser_team_id','fixure_result','added_by','status'];

    public function sport()
    {
        return $this->hasOne('Modules\Sport\Models\Sport','id','sport_id');
    }
    public function round()
    {
        return $this->hasOne('Modules\Sport\Models\Round','id','round_id');
    }
    public function hometeam()
    {
        return $this->hasOne('Modules\Sport\Models\Team','id','home_team_id');
    }
    public function awayteam()
    {
        return $this->hasOne('Modules\Sport\Models\Team','id','away_team_id');
    }
    public function winnerteam()
    {
        return $this->hasOne('Modules\Sport\Models\Team','id','winner_team_id');
    }
    public function looserteam()
    {
        return $this->hasOne('Modules\Sport\Models\Team','id','looser_team_id');
    }
}
