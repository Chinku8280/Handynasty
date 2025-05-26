<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = 'outlets';

    protected $fillable = [
        'outlet_name',
        'outlet_description',
        'image',
        'longitute',
        'latitude',
        'address',
        'phone',
        'open_time',
        'close_time',
    ];

    public function services()
    {
        return $this->hasMany(BusinessService::class, 'outlet_id');
    }

    public function manyServices()
    {
        return $this->belongsToMany(BusinessService::class, 'business_services_outlets', 'outlet_id', 'business_service_id');
    }
    
}
