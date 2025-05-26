<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{

  
    //------------------------------------ Attributes ---------------------------

    protected $dates = ['created_at'];

    //------------------------------------ Relations ----------------------------

   
    public function customers() {
        return $this->hasMany(PackageUser::class, 'package_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'package_users', 'package_id', 'user_id')
                    ->withPivot('status');
    }
    
} /* end of class */
