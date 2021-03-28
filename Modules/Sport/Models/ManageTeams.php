<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;

class ManageTeams extends Model
{
	protected $table = 'lms_manage_teams';
    protected $fillable = ['league_id', 'team_id','rank_order'];

}
