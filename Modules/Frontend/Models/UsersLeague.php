<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Frontend\Models\Frontend;
use Auth;

class UsersLeague extends Model
{
	protected $table = 'lms_league_users';
    protected $fillable = ['league_id','user_id','sport_id','is_admin','is_team_pickup', 'status','is_play'];

   public function league()
    {
        return $this->hasOne('Modules\Sport\Models\League','id','league_id')->with("getRound",'leagueuserteam','commentcount','getStartRound');
    }
   public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function sport()
    {

        return $this->hasOne('Modules\Sport\Models\Sport','id','sport_id');

        // return $this->hasManyThrough('Modules\Sport\Models\Sport', 'Modules\Sport\Models\League','sport_id','id');
    }

    public function round()
    {
        return $this->belongsToMany('Modules\Sport\Models\Round','sport_id','sport_id');
    }

    public function userteam() 
    {
        return $this->hasOne('Modules\Sport\Models\Team','id','team_id');
    }

    public function getRound(){
        return $this->hasOne('Modules\Sport\Models\Round','id','current_round_id');
    }

} 