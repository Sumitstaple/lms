<?php

namespace Modules\Sport\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sport\Models\Sport;

class Round extends Model
{
	protected $table = 'lms_rounds';
    protected $fillable = ['added_by','sport_id', 'round_name','round_number','round_description','start_datetime','end_datetime'];

     public function sport()
    {
        return $this->hasOne('Modules\Sport\Models\Sport','id','sport_id');
    }
}
