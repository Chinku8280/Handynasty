<?php

namespace App;

use App\Observers\OfferObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class offer extends Model
{

    //------------------------------------ Attributes ---------------------------

    private $settings;

    public function __construct() {
        parent::__construct();
        $this->settings = CompanySetting::first();
    }

    protected static function boot() {
        parent::boot();
        static::observe(OfferObserver::class);
    }

    protected $appends = [
        'offer_image_url'
    ];

    //------------------------------------ Relations ----------------------------
   
    public function customers() {
        return $this->hasMany(OfferObserver::class, 'offer_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'offer_users', 'offer_id', 'user_id');
    }

 
    public function branch() {
        return $this->belongsTo(Location::class);
    }

    public function bookings() {
        return $this->hasMany(Booking::class);
    }

    //------------------------------------ Scopes -------------------------------

    public function scopeActive($query) {
        return $query->where('status', 'active');
    }

    //------------------------------------ Accessors ----------------------------

    // public function getofferImageUrlAttribute() {
    //     if(is_null($this->image)){
    //         return asset('img/no-image.jpg');
    //     }
    //     return asset_url('offer/'.$this->image);
    // }

    public function getofferImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('img/no-image.jpg');
        }
    
        return env('APP_URL').'/user-uploads/offer/' . $this->image;
    } 


    public function getAppliedBetweenTimeAttribute() {
        return $this->open_time.' - '.$this->close_time;
    }

    public function getStartDateAttribute($value) {
        $date = new Carbon($value);
        return $date->format('Y-m-d h:i A');
    }

    public function getEndDateAttribute($value) {
        $date = new Carbon($value);
        return $date->format('Y-m-d h:i A');
    }

  
   

} /* end of class */
