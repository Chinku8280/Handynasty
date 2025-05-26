<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoyaltyShop extends Model
{
    protected $appends = [
        'loyalty_shop_image_url',
    ];

    //------------------------------------ Accessors ---------------------------

    public function getLoyaltyShopImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('img/no-image.jpg');
        }
    
        return asset('/user-uploads/loyalty-shop/' . $this->image);
    }

    //------------------------------------ Relations ----------------------------

    public function loyalty_shop_user()
    {
        return $this->belongsToMany(User::class, 'loyalty_shop_users', 'loyalty_shop_id', 'user_id');
    }
}
