<?php

namespace App;

use App\Observers\BusinessServiceObserver;
use Illuminate\Database\Eloquent\Model;

class BusinessService extends Model
{

    //------------------------------------ Attributes ---------------------------

    protected static function boot() {
        parent::boot();
        static::observe(BusinessServiceObserver::class);
    }

    protected $appends =[
        'service_image_url',
        'service_detail_url',
        'discounted_price'
    ];

    //------------------------------------ Relations ---------------------------

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function bookingItems() {
        return $this->hasMany(BookingItem::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function manyOutlets()
    {
        return $this->belongsToMany(Outlet::class, 'business_services_outlets', 'business_service_id', 'outlet_id');
    }

    //------------------------------------ Scopes ---------------------------

    public function scopeActive($query) {
        return $query->where('status', 'active');
    }

    //------------------------------------ Accessors ---------------------------

    public function getServiceImageUrlAttribute() {
        if(is_null($this->default_image)){
            return asset('img/no-image.jpg');
        }
        return asset_url('service/'.$this->id.'/'.$this->default_image);
        // return asset_url('services/'.$this->default_image);
    }

    public function getImageAttribute($value) {
        if (is_array(json_decode($value, true))) {
            return json_decode($value, true);
        }
        return $value;
    }

    public function getServiceDetailUrlAttribute() {
        return route('front.serviceDetail', ['categorySlug' => $this->category->slug, 'serviceSlug' => $this->slug]);
    }

    public function getDiscountedPriceAttribute(){
        if($this->discount > 0){
            if($this->discount_type == 'fixed'){
                return ($this->price - $this->discount);
            }
            elseif($this->discount_type == 'percent'){
                $discount = (($this->discount/100)*$this->price);
                return round(($this->price - $discount), 2);
            }
        }
        return $this->price;
    }

    //------------------------------------ Mutators ---------------------------

    // public function setNameAttribute($value) {
    //     $this->attributes['name'] = ucwords($value);
    // }

} /* end of class */
