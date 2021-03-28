<?php
/**
 * File Permission.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */

namespace App\Laravue\Models;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Permission
 *
 * @package App\Laravue\Models
 */
class Permission extends Model
{
    protected $table = 'lms_permissions';
    public $guard_name = 'api';

    /**
     * To exclude permission management from the list
     *
     * @param $query
     * @return Builder
     */
    public function scopeAllowed($query)
    {
        //return $query->where('name', '!=', Acl::PERMISSION_PERMISSION_MANAGE);
    }
}
