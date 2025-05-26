<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionItem extends Model
{
    //------------------------------------ Relations ----------------------------

    public function businessService() {
        return $this->belongsTo(BusinessService::class);
    }

    public function promotion() {
        return $this->belongsTo(Promotion::class);
    }
}
