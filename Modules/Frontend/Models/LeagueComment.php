<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Frontend\Models\Frontend;

class LeagueComment extends Model
{
	protected $table = 'lms_league_comments';
    protected $fillable = ['league_id','user_id','round_id', 'comment','parent_comment_id','sub_parent_id','status','sub_sub_parent_id'];

   public function league()
    {
        return $this->hasOne('Modules\Sport\Models\League','id','league_id');
    }
   public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}