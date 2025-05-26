<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceBanner extends Model
{
    protected $table = "service_banners";

    protected $fillable = [
        'image',
    ];
}
