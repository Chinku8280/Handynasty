<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $appends =[
        'product_image_url',
    ];

    //------------------------------------ Accessors ---------------------------

    public function getProductImageUrlAttribute() {
        if(is_null($this->default_image)){
            return asset('img/no-image.jpg');
        }
        return asset_url('product/'.$this->id.'/'.$this->default_image);
        // return asset_url('services/'.$this->default_image);
    }

    public function getImageAttribute($value) {
        if (is_array(json_decode($value, true))) {
            return json_decode($value, true);
        }
        return $value;
    }
}
