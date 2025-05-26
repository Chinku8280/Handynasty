<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    //

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }
}
