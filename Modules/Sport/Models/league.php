<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;
use Auth;


class League extends Model
{


	protected $table = 'lms_leagues';
    protected $fillable = ['user_id','is_private','sport_id', 'round_to_start','league_name','type','start_datetime','end_datetime','current_round_id','if_forfeit','is_banterboard'];

   public function sport()
    {
        return $this->hasOne('Modules\Sport\Models\Sport','id','sport_id');
    }
        // public function round()
        // {
        //     return $this->hasOne('Modules\Sport\Models\Round','id','round_to_start');
        // }
    public function location()
    { 
        return $this->hasOne('Modules\Sport\Models\LeagueLocation','league_id','id');
        // return $this->belongsTo('Modules\Sport\Models\LeagueLocation','league_id');
    }

    public function round()
    { 
        return $this->hasOne('Modules\Sport\Models\Round','id','current_round_id');
        // return $this->belongsTo('Modules\Sport\Models\LeagueLocation','league_id');
    }

    public function getRound(){
        return $this->hasOne('Modules\Sport\Models\Round','id','current_round_id');
    }

    public function leagueuserteam()
    {
        return $this->hasOne('Modules\Frontend\Models\SavedTeam','round_id','current_round_id','league_id','id')->where('user_id',Auth::id())->with('getTeam');
    }
    public function commentcount()
    {
        return $this->hasOne('Modules\Frontend\Models\LeagueCommentStatus','league_id','id')->where('user_id',Auth::user()->id);
    }
    public function getStartRound(){
        return $this->hasOne('Modules\Sport\Models\Round','id','round_to_start');
    }

}
