<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageUser extends Model
{

    protected $fillable = [
        'package_id', 
        'description', 
    ];
    //------------------------------------ Relations ----------------------------

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

} /* end of class  */
