<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'lms_system_logs';
    
    protected $fillable = [
        'user_id', 'log_type_id','log_action','log_text'
    ];

    public function logtype()
    {
        return $this->hasOne('App\LogType','id','log_type_id');
    }

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

}