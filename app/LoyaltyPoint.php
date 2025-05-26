<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
      // Your existing LoyaltyPoint model code...

    /**
     * Define the inverse of the one-to-one relationship with User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
