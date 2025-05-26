<?php

namespace App;

use App\Observers\VoucherObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{

    //------------------------------------ Attributes ---------------------------

    private $settings;

    public function __construct() {
        parent::__construct();
        $this->settings = CompanySetting::first();
    }

    protected static function boot() {
        parent::boot();
        static::observe(VoucherObserver::class);
    }

    protected $appends = [
        'voucher_image_url', 
        // 'applied_between_time', 
        // 'voucher_detail_url'
    ];

    //------------------------------------ Relations ----------------------------
   
    public function customers() {
        return $this->hasMany(VoucherUser::class, 'voucher_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'voucher_users', 'voucher_id', 'user_id');
    }

    public function voucher_user()
    {
        return $this->belongsToMany(User::class, 'voucher_users', 'voucher_id', 'user_id');
    }

    public function voucher_usage()
    {
        return $this->belongsToMany(User::class, 'voucher_usages', 'voucher_id', 'user_id');
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

    // public function getvoucherImageUrlAttribute() {
    //     if(is_null($this->image)){
    //         return asset('img/no-image.jpg');
    //     }
    //     return asset_url('voucher/'.$this->image);
    // }

    public function getVoucherImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('img/no-image.jpg');
        }
    
        return asset('/user-uploads/voucher/' . $this->image);
    }    


    // public function getAppliedBetweenTimeAttribute() {
    //     return $this->open_time.' - '.$this->close_time;
    // }

    public function getStartDateAttribute($value) {
        $date = new Carbon($value);
        return $date->format('Y-m-d h:i A');
    }

    public function getEndDateAttribute($value) {
        $date = new Carbon($value);
        return $date->format('Y-m-d h:i A');
    }

    // public function getOpenTimeAttribute($value) {
    //     return Carbon::createFromFormat('H:i:s', $value)->setTimezone($this->settings->timezone)->format($this->settings->time_format);
    // }

    // public function getCloseTimeAttribute($value) {
    //     return Carbon::createFromFormat('H:i:s', $value)->setTimezone($this->settings->timezone)->format($this->settings->time_format);
    // }
 

    // public function getvoucherDetailUrlAttribute() {
    //     return route('front.voucherDetail', ['voucherSlug' => $this->slug, 'voucherId' => $this->id]);
    // }

    // public function getVoucherDetailUrlAttribute()
    // {
    //     return asset('voucher/' . $this->id . '/' . $this->slug);
    // }     


   

} /* end of class */
