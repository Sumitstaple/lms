<?php

namespace App\Laravue\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;


class PermissionGroup extends Model
{
    protected $table = 'lms_permissions_group';
    protected $fillable = ['role_id','permission_ids'];
}
