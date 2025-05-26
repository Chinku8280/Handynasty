<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{

    //------------------------------------ Attributes ---------------------------

    protected $guarded = [];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('withoutCustomerRole', function (Builder $builder) {
            $builder->where('name', '<>', 'customer');
        });
    }

    //------------------------------------ Relations ----------------------------

    public function getRoleCount() {
        return $this->hasMany(User::class);
    }

    //------------------------------------ Accessors ----------------------------

    public function getMemberCountAttribute() {
        return $this->users->count();
    }

} /* end of class */
