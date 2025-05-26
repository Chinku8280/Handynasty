<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeGroup extends Model
{
    //------------------------------------ Attributes ---------------------------
    protected $guarded = ['id'];
    protected $table = 'employee_groups';


    //------------------------------------ Relations ----------------------------

    public function services() {
        return $this->hasMany(EmployeeGroupService::class, 'employee_groups_id', 'id', 'employee_group_services');
    }

} /* end of class */
