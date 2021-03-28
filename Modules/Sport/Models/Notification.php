<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;

class Notification extends Model
{
	protected $table = 'lms_user_notifications';
    protected $fillable = ['user_id', 'message','is_read','type','target_tbl','target_column','target_id','league_id'];
    
    public function user()
    {
        return $this->hasOne('App\Users','id','user_id');
    }

}
