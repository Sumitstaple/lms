<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;

class Team extends Model
{
	protected $table = 'lms_teams';
    protected $fillable = ['team_name', 'team_icon','added_by','sport_id'];

     public function sport()
    {
        return $this->hasOne('Modules\Sport\Models\Sport','id','sport_id');
    }
}
