<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;
use Auth;
class SavedTeam extends Model
{
	protected $table = 'lms_league_user_teams';
	protected $fillable = [
        'user_id', 'league_id', 'round_id','team_id','fixture_id'
    ];

    public function getTeam(){
        return $this->hasOne('Modules\Sport\Models\Team','id','team_id');
    }

}
 