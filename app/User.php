<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use App\Observers\UserObserver;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;



class User extends Authenticatable
{

    //------------------------------------ Traits ---------------------------

    use HasApiTokens, LaratrustUserTrait, Notifiable;
    
    //------------------------------------ Attributes ---------------------------

  
    protected static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
        static::laratrustObserve(UserObserver::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
 
    protected $fillable = [
        'fname', 'lname', 'dob', 'image_display_in_app', 'mobile', 'referral_code', 'email','role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'user_image_url', 'mobile_with_code', 'formatted_mobile'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    //------------------------------------ Relations ----------------------------

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_users', 'user_id', 'package_id')
                    ->withPivot('status');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_user')->withTimestamps();
    }
    
   
    public function employeeGroup()
    {
        return $this->belongsTo(EmployeeGroup::class, 'group_id');
    }

    public function loyaltyPoint()
    {
        return $this->hasOne(LoyaltyPoint::class);
    }

    public function todoItems()
    {
        return $this->hasMany(TodoItem::class);
    }

    public function completedBookings()
    {
        return $this->hasMany(Booking::class, 'user_id')->where('bookings.status', 'completed');
    }

    public function booking()
    {
        return $this->belongsToMany(Booking::class);
    }

    public function services()
    {
        return $this->belongsToMany(BusinessService::class);
    }

    //------------------------------------ Scopes -------------------------------

    public function scopeAllAdministrators()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'administrator');
        });
    }

    public function scopeAllCustomers()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'customer')->withoutGlobalScopes();
        });
    }

    public function scopeOtherThanCustomers()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', '<>', 'customer');
        });
    }

    public function scopeAllEmployees()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        });
    }

    public function scopeAllTherapist()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'therapist');
        });
    }

    //------------------------------------ Accessors ----------------------------

    public function getUserImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('img/default-avatar-user.png');
        }
        return asset_url('avatar/' . $this->image);
    }

    public function getRoleAttribute()
    {
        return $this->roles->first();
    }

    public function getMobileWithCodeAttribute()
    {
        return substr($this->calling_code, 1) . $this->mobile;
    }

    public function getFormattedMobileAttribute()
    {
        if (!$this->calling_code) {
            return $this->mobile;
        }
        return $this->calling_code . '-' . $this->mobile;
    }

    public function routeNotificationForNexmo($notification)
    {
        return $this->mobile_with_code;
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('administrator');
    }
    public function isBranch()
    {
        return $this->role === 'branch'; // Adjust this logic as per your user roles
    }

    public function getIsEmployeeAttribute()
    {
        return $this->hasRole('employee');
    }

    public function getIsCustomerAttribute()
    {
        if ($this->roles()->withoutGlobalScopes()->where('roles.name', 'customer')->count() > 0) {
            return true;
        }
        return false;
    }

    //------------------------------------ Mutators -----------------------------

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    //------------------------------------ Formats -----------------------------

    public function userBookingCount($date)
    {
        return Booking::whereNull('deal_id')->where('user_id', $this->id)->whereDate('created_at', $date)->get()->count();
    }
} /* end of class */
