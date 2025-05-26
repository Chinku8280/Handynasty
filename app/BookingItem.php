<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    //------------------------------------ Relations -----------------------

    public function businessService(){
        return $this->belongsTo(BusinessService::class);
    }

    public function booking(){
        return $this->belongsTo(Booking::class);
    }

} /* end of class */
