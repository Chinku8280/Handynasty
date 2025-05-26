<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{

    //------------------------------------ Attributes ---------------------------

    protected $dates = ['start_date_time', 'end_date_time', 'created_at'];

    //------------------------------------ Relations ----------------------------
    protected $appends =[
        'coupon_image_url'
    ];
    public function customers() 
    {
        return $this->hasMany(CouponUser::class, 'coupon_id');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_users', 'coupon_id', 'user_id');
    }

    public function coupon_usage()
    {
        return $this->belongsToMany(User::class, 'coupon_usages', 'coupon_id', 'user_id');
    }
    
    public function getCouponImageUrlAttribute() {
        if(is_null($this->coupon_image)){
            return asset('img/no-image.jpg');
        }
        // return asset_url('service/'.$this->id.'/'.$this->coupon_image);
        return coupon_asset_url($this->coupon_image);
    }
} /* end of class */
