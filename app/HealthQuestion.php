<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HealthQuestion extends Model
{
    protected $appends = [
        'customer_signature_image_url'
    ];

    public function getCustomerSignatureImageUrlAttribute()
    {
        if (is_null($this->customer_signature) || empty($this->customer_signature)) {
            // return asset('img/no-image.jpg');
            return "";
        }
    
        return asset('/user-uploads/customer-signature/' . $this->customer_signature);
    }    
}
