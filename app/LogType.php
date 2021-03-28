<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogType extends Model
{
    protected $table = 'lms_log_types';
    
    protected $fillable = [
        'type', 'description','sort_order'
    ];
}