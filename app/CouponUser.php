<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponUser extends Model
{

    protected $table = "coupon_users";

    //------------------------------------ Relations ----------------------------

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

}
