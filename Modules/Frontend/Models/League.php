<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;

class League extends Model
{
	protected $table = 'lms_leagues';
    protected $fillable = ['user_id','is_private','sport_id', 'round_to_start','league_name','town','city','state','country','type','start_datetime','end_datetime'];

   public function sport()
    {
        return $this->hasOne('Modules\Sport\Models\Sport','id','sport_id');
    }
    public function round()
    {
        return $this->hasOne('Modules\Sport\Models\Round','id','round_to_start');
    }

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function leagueusers()
    {
        
        return $this->hasMany('Modules\Frontend\Models\UsersLeague','league_id','id')->with('user');
    }

    public function location()
    {
        return $this->hasOne('Modules\Sport\Models\LeagueLocation','league_id','id');
    }

    public function getUsers()
    {
        return $this->hasMany('Modules\Frontend\Models\UsersLeague','league_id','id');
    }

}
