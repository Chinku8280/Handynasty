<?php

namespace App;

use App\Observers\PromotionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = "promotions";

    //------------------------------------ Attributes ---------------------------

    private $settings;

    public function __construct() {
        parent::__construct();
        $this->settings = CompanySetting::first();
    }

    protected static function boot() {
        parent::boot();
        static::observe(PromotionObserver::class);
    }

    protected $appends = [
        'promotion_image_url', 'applied_between_time'
        // 'promotion_detail_url'
    ];

    //------------------------------------ Relations ----------------------------

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function outlet() {
        return $this->belongsTo(Outlet::class);
    }

    public function services() {
        return $this->hasMany(PromotionItem::class);
    }

    // public function items(){
    //     return $this->hasMany(PromotionItem::class);
    // }

    public function bookings() {
        return $this->hasMany(Booking::class);
    }

    //------------------------------------ Scopes -------------------------------

    public function scopeActive($query) {
        return $query->where('status', 'active');
    }

    //------------------------------------ Accessors ----------------------------

    public function getPromotionImageUrlAttribute() {
        if(is_null($this->image)){
            return asset('img/no-image.jpg');
        }
        return asset_url('promotion/'.$this->image);
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

    public function getOpenTimeAttribute($value) {
        return Carbon::createFromFormat('H:i:s', $value)->setTimezone($this->settings->timezone)->format($this->settings->time_format);
    }

    public function getCloseTimeAttribute($value) {
        return Carbon::createFromFormat('H:i:s', $value)->setTimezone($this->settings->timezone)->format($this->settings->time_format);
    }

    public function getmaxOrderPerCustomerAttribute($value) {
        if($this->uses_limit==0 && $value==0) {
            return 'Infinite';
        }
        elseif($this->uses_limit>0 && ($value==0 || $value=='')) {
            return $this->uses_limit;
        }
        return $value;
    }

    // public function getpromotionDetailUrlAttribute() {
        
    // }

    //------------------------------------ Mutators -----------------------------

    public function setLocationIdAttribute($value) {
        $this->attributes['location_id'] = Location::where('name', $value)->first()->id;
    }
}
