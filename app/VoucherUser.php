<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherUser extends Model
{
    //------------------------------------ Relations ----------------------------

    public function user() {
        return $this->belongsTo(User::class);
    }
} /* end of class  */
