<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferUser extends Model
{
    //------------------------------------ Relations ----------------------------

    public function user() {
        return $this->belongsTo(User::class);
    }

} /* end of class  */
