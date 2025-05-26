<?php

namespace App;

use App\Observers\LocationObserver;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Location extends Authenticatable
{
   
    //------------------------------------ Attributes ---------------------------

    protected $appends = [
        'branch_image_url'
    ];

    protected static function boot() {
        parent::boot();
        static::observe(LocationObserver::class);
    }
   
    //------------------------------------ Relations ----------------------------

    public function services() {
        return $this->hasMany(BusinessService::class);
    }

    public function deals() {
        return $this->belongsToMany(Deal::class);
    }

    public function getBranchImageUrlAttribute() {
        if(is_null($this->image)){
            return asset('img/no-image.jpg');
        }
        return asset_url('branch/'.$this->image);
    }

} /* end of class */
