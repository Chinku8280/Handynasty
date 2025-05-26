<?php

namespace App;

use App\Observers\BookingObserver;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //------------------------------------ Attributes -----------------------

    protected $dates = ['date_time'];

    protected static function boot() {
        parent::boot();
        static::observe(BookingObserver::class);
    }

    //------------------------------------ Relations ------------------------

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function deal() {
        return $this->belongsTo(Deal::class);
    }

    public function promotion() {
        return $this->belongsTo(Promotion::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function coupon() {
        return $this->belongsTo(Coupon::class);
    }

    public function employees() {
        return $this->belongsToMany(User::class, 'employee_id');
    }

    public function items() {
        return $this->hasMany(BookingItem::class);
    }

    public function payment() {
        return $this->hasOne(Payment::class)->whereNotNull('paid_on');
    }

    public function completedPayment() {
        return $this->hasOne(Payment::class)->where('status', 'completed')->whereNotNull('paid_on');
    }


    //--------------------------------------- Accessor --------------------------

    public function getDateTimeAttribute($value) {
        if($this->validateDate($value)){
            return Carbon::createFromFormat('Y-m-d H:i:s', $value)->setTimezone(CompanySetting::first()->timezone);
        }
        return '';
    }

    public function getUtcDateTimeAttribute() {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['date_time']);
    }

    //------------------------------------ Mutators -----------------------------

     public function setDateTimeAttribute($value) {
        $this->attributes['date_time'] = Carbon::parse($value, CompanySetting::first()->timezone)->setTimezone('UTC');
    }

    //----------------------------------- Validations -------------------

    function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }


} /* end of class */
