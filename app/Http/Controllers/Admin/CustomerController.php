<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\BusinessService;
use App\Category;
use App\Coupon;
use App\CouponRedeem;
use App\CouponUsage;
use App\CouponUser;
use App\Feedback;
use App\HealthQuestion;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Customer\StoreCustomer;
use App\Notifications\NewUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateCustomer;
use App\Location;
use App\LoyaltyPoint;
use App\LoyaltyProgramHistory;
use App\LoyaltyProgramHour;
use App\LoyaltyShop;
use App\LoyaltyShopRedeem;
use App\LoyaltyShopUsage;
use App\LoyaltyShopUser;
use App\Offer;
use App\Outlet;
use App\Package;
use App\Role;
use App\Voucher;
use App\PackageUser;
use App\VoucherRedeem;
use App\VoucherUsage;
use App\VoucherUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.customers'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer'), 403);

    //     $customers = User::all();
    //     return view('admin.customer.index', compact('customers'));
    // }

    public function index(Request $request)
    {
        // return $request->all();

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer'), 403);

        if($request->has('customer_search') && $request->filled('customer_search'))
        {
            $customer_search = $request->customer_search;

            $customers = User::allCustomers()
                                ->where(function ($q) use ($customer_search){
                                    $q->where('name', 'like', '%'.$customer_search.'%')
                                    ->orWhere('mobile', 'like', '%'.$customer_search.'%')
                                    ->orWhere('email', 'like', '%'.$customer_search.'%');
                                });
        }
        else
        {
            $customers = User::allCustomers();
            $customer_search = "";
        }

        if($request->has('filter_oulet_id') && $request->filled('filter_oulet_id'))
        {
            $filter_oulet_id = $request->filter_oulet_id;

            $customers = $customers->where('outlet_id', $request->filter_oulet_id);
        }
        else
        {
            $filter_oulet_id = $request->filter_oulet_id;
        }

        // if(Session::has('outlet_slug') || Session::has('outlet_id'))
        // {
        //     $customers = $customers->where('outlet_id', Session::get('outlet_id'));
        // } 

        // sort by
        $customer_sort = $request->customer_sort ?? '';

        switch ($customer_sort)
        {
            case 'newest':
                $customers->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $customers->orderBy('created_at', 'asc');
                break;
            case 'alphabetically_asc':
                $customers->orderBy('name', 'asc');
                break;
            case 'alphabetically_desc':
                $customers->orderBy('name', 'desc');
                break;
            default:
                break;
        }

        $customers = $customers->get();

        $outlet = Outlet::where('status', 'active')->get();
        
        return view('admin.customer.index', compact('customers', 'customer_search', 'customer_sort', 'outlet', 'filter_oulet_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_customer'), 403);

        $branches = Location::all();
        $outlet = Outlet::where('status', 'active')->get();

        return view('admin.customer.create', compact('branches', 'outlet'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomer $request)
    {
        // return $request->all();

        $user = new User();
        // $user->prefix = $request->prefix;
        // $user->fname = $request->fname;
        // $user->lname = $request->lname;
        // $user->name = $request->fname . " " . $request->lname;
        $user->name = $request->full_name;
        $user->email = $request->email;
        // $user->password = Hash::make($request->password);
        $user->password = $request->password;
        // $user->branch_id = $request->branch;
        $user->outlet_id = $request->outlet_id;
        $user->mobile = $request->mobile;

        if($request->filled('dob'))
        {
            $user->dob = date('Y-m-d', strtotime($request->dob));
        }

        if($request->filled('gender'))
        {
            $user->gender = $request->gender;     
        }

        if ($request->hasFile('image')) 
        {
            $user->image = Files::upload($request->image, 'avatar');
        }
        $user->status = 'active';
        $user->save();

        // add customer role
        $userRole = Role::where('name', 'customer')->withoutGlobalScopes()->first()->id;

        $user->roles()->attach($userRole);

        // $user->notify(new NewUser('123456'));
        return Reply::redirect(route('admin.customers.index'), __('messages.createdSuccessfully'), ['user' => ['id' => $user->id, 'text' => $user->name]]);
        // return Reply::successWithData(__('messages.createdSuccessfully'), ['user' => ['id' => $user->id, 'text' => $user->name]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // public function show($id)
    // {
    //     abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

    //     $customer = User::findOrFail($id);

    //     $assignedPackages = PackageUser::where('user_id', $id)->with('package')->get();
    //     // dd($customer);
    //     $usedCoupons = Coupon::where('status', 'active')
    //         ->whereHas('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })
    //         ->get();

    //     $unusedCoupons = Coupon::where('status', 'active')
    //         ->whereDoesntHave('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })
    //         ->get();
    //         $usedCoupons->each(function ($coupon) {
    //             $couponTitle = optional($coupon)->title;
            
    //             if ($couponTitle) {
    //                 $coupon->formattedStartDate = optional(Carbon::parse($coupon->start_date_time))->format('j F Y');
    //                 $coupon->formattedEndDate = optional(Carbon::parse($coupon->end_date_time))->format('j F Y');
    //             } else {
    //                 echo "Title property is not present for used coupon with ID: " . optional($coupon)->id . "\n";
    //                 dd($coupon);
    //             }
    //         });
            
    //         $unusedCoupons->each(function ($coupon) {
    //             $couponTitle = optional($coupon)->title;
            
    //             if ($couponTitle) {
    //                 $coupon->formattedStartDate = optional(Carbon::parse($coupon->start_date_time))->format('j F Y');
    //                 $coupon->formattedEndDate = optional(Carbon::parse($coupon->end_date_time))->format('j F Y');
    //             } else {
    //                 echo "Title property is not present for unused coupon with ID: " . optional($coupon)->id . "\n";
    //                 dd($coupon);
    //             }
    //         });
            

    //     $usedVoucher = Voucher::where('status', 'active')
    //         ->whereHas('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })
    //         ->get();
    //     $usedVoucher->each(function ($voucher) {
    //         $voucher->formattedEndDate = Carbon::parse($voucher->end_date_time)->format('j F Y');
    //     });
        
    //     $availableVoucher = Voucher::where('status', 'active')
    //         ->whereDoesntHave('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })->get();
    //     $availableVoucher->each(function ($voucher) {
    //         $voucher->formattedEndDate = Carbon::parse($voucher->end_date_time)->format('j F Y');
    //     });

    //     $availableOffers = Offer::where('status', 'active')
    //         ->whereDoesntHave('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })->get();
    //     $claimedOffers = Offer::where('status', 'active')
    //         ->whereHas('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })
    //         ->get();
    //     $packages = Package::all();    

    //     $loyaltyPoints = LoyaltyPoint::where('user_id', $id)->get();

    //     if (\request()->ajax()) {
    //         $view = view('admin.customer.ajax_show', compact('customer'))->render();
    //         return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    //     }

    //     $completedBookings = Booking::where('user_id', $id)->where('status', 'completed')->count();
    //     $approvedBookings = Booking::where('user_id', $id)->where('status', 'approved')->count();
    //     $pendingBookings = Booking::where('user_id', $id)->where('status', 'pending')->count();
    //     $canceledBookings = Booking::where('user_id', $id)->where('status', 'canceled')->count();
    //     $inProgressBookings = Booking::where('user_id', $id)->where('status', 'in progress')->count();
    //     $earning = Booking::where('user_id', $id)->where('status', 'completed')->sum('amount_to_pay');

    //     // dd($customer, $usedCoupons, $unusedCoupons, $usedVoucher, $availableVoucher, $availableOffers, $claimedOffers, $packages, $loyaltyPoints);

    //     return view('admin.customer.show', compact('customer', 'assignedPackages', 'packages', 'availableOffers', 'loyaltyPoints', 'claimedOffers', 'completedBookings', 'approvedBookings', 'pendingBookings', 'inProgressBookings', 'canceledBookings', 'earning', 'unusedCoupons', 'usedCoupons', 'availableVoucher', 'usedVoucher'));
    // }

    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $customer = User::findOrFail($id);

        // $customer_dob = date('Y-m-d', strtotime($customer->dob));
        $customer_dob = !empty($customer->dob) ? date('Y-m-d', strtotime($customer->dob)) : '';
        $customer_age = Carbon::parse($customer_dob)->age;
        $customer_gender = $customer->gender;

        $assignedPackages = PackageUser::where('user_id', $id)->with('package')->get();

        // *** coupon start ***

        // $usedCoupons = Coupon::where('status', 'active')
        //     ->whereHas('coupon_usage', function ($query) use ($customer) {
        //         $query->where('user_id', $customer->id);
        //     })
        //     ->get();

        $usedCoupons = Coupon::join('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id')
                            ->where('coupons.status', 'active')
                            ->where('coupon_usages.user_id', $customer->id)
                            ->select('coupons.*', 'coupon_usages.id as coupon_usage_id', 'coupon_usages.created_at as used_on_date')
                            ->get();        

        $usedCoupons->each(function ($coupon) {           
            $coupon->formattedStartDate = optional(Carbon::parse($coupon->start_date_time))->format('j F Y');
            $coupon->formattedEndDate = optional(Carbon::parse($coupon->end_date_time))->format('j F Y');   
            
            $coupon->used_on_date_format = Carbon::parse($coupon->used_on_date)->format('j F Y');
        });

        // $unusedCoupons = Coupon::where('status', 'active')
        //                         ->whereDoesntHave('users', function ($query) use ($customer) {
        //                             $query->where('user_id', $customer->id);
        //                         })
        //                         ->get();       
        
        
        // $coupon_id_arr = CouponUser::where('user_id', $customer->id)->pluck('coupon_id')->toArray();

        // $customer_coupons = Coupon::where('status', 'active')
        //                     ->whereDate('start_date_time', '<=', date('Y-m-d'))
        //                     ->whereDate('end_date_time', '>=', date('Y-m-d'))
        //                     ->whereIn('id', $coupon_id_arr)
        //                     ->get();

        // $all_coupons = Coupon::where('status', 'active')
        //                     ->whereDate('start_date_time', '<=', date('Y-m-d'))
        //                     ->whereDate('end_date_time', '>=', date('Y-m-d'))
        //                     ->where('is_customer_specific', 0)
        //                     ->get();

        // $unusedCoupons = $all_coupons->merge($customer_coupons);

        $redeem_coupon_id_arr = CouponRedeem::where('user_id', $customer->id)->pluck('coupon_id')->toArray();

        $redeem_coupons = Coupon::where('status', 'active')
                            ->whereDate('start_date_time', '<=', date('Y-m-d'))
                            ->whereDate('end_date_time', '>=', date('Y-m-d'))
                            ->whereIn('id', $redeem_coupon_id_arr)
                            ->get();

        $unusedCoupons = collect();

        foreach($redeem_coupons as $item)
        {
            $item->formattedStartDate = optional(Carbon::parse($item->start_date_time))->format('j F Y');
            $item->formattedEndDate = optional(Carbon::parse($item->end_date_time))->format('j F Y'); 

            // check coupon uses limit active or not

            if($item->uses_limit == null || $item->uses_limit == "")
            {
                $item->used_status = 'active';
            }
            else if($item->uses_limit == 0)
            {
                $item->used_status = 'deactive';
            }
            else
            {
                if(($item->used_time != $item->uses_limit))
                {
                    $item->used_status = 'active';
                }
                else
                {
                    $item->used_status = 'deactive';
                }
            }

            // check coupon used or not

            if(CouponUsage::where('user_id', $customer->id)->where('coupon_id', $item->id)->exists())
            {
                $item->isUsed = true;
            }
            else
            {
                $item->isUsed = false;
            }   
            
            // check age

            if($item->max_age >= $customer_age && $item->min_age <= $customer_age)
            {
                $item->isAgeEligible = true;
            }
            else
            {
                $item->isAgeEligible = false;
            }

            // check gender

            if(DB::table('coupon_gender')->where('coupon_id', $item->id)->where('gender', $customer_gender)->exists())
            {
                $item->isGenderEligible = true;
            }
            else
            {
                $item->isGenderEligible = false;
            }

            // Apply filter condition
            // if ($item->used_status == 'active' && $item->isUsed === false && $item->isAgeEligible === true && $item->isGenderEligible === true) 
            // {
            //     $unusedCoupons->push($item);
            // }

            // Apply filter condition
            if ($item->used_status == 'active' && $item->isUsed === false) 
            {
                $unusedCoupons->push($item);
            }
        }

        // *** coupon end ***

        // *** voucher start ***

        // returns unique Voucher model rows using whereHas(), regardless of how many related voucher_usage rows match.

        // $usedVoucher = Voucher::where('status', 'active')
        //     ->whereHas('voucher_usage', function ($query) use ($customer) {
        //         $query->where('user_id', $customer->id);
        //     })
        //     ->get();

        $usedVoucher = Voucher::join('voucher_usages', 'vouchers.id', '=', 'voucher_usages.voucher_id')
                                ->where('vouchers.status', 'active')
                                ->where('voucher_usages.user_id', $customer->id)
                                ->select('vouchers.*', 'voucher_usages.id as voucher_usage_id', 'voucher_usages.created_at as used_on_date')
                                ->get();

        $usedVoucher->each(function ($voucher) {
            $voucher->formattedStartDate = !empty($voucher->start_date_time) ? (Carbon::parse($voucher->start_date_time)->format('j F Y')) : '';
            $voucher->formattedEndDate = !empty($voucher->end_date_time) ? (Carbon::parse($voucher->end_date_time)->format('j F Y')) : '';
            $voucher->used_on_date_format = Carbon::parse($voucher->used_on_date)->format('j F Y');
        });

        $availableVoucher = [];
        
        // $availableVoucher = Voucher::where('status', 'active')
        //     ->whereDoesntHave('users', function ($query) use ($customer) {
        //         $query->where('user_id', $customer->id);
        //     })->get();

        $redeemed_vouchers = VoucherRedeem::join('vouchers', 'voucher_redeems.voucher_id', '=', 'vouchers.id')
                                            ->where('voucher_redeems.user_id', $customer->id)
                                            ->where('voucher_redeems.usage_status', 0)
                                            // ->whereDate('vouchers.start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('vouchers.end_date_time', '>=', date('Y-m-d'))
                                            ->where('vouchers.status', 'active')
                                            ->where('is_redeemable', 1)
                                            ->select('vouchers.*', 'voucher_redeems.user_id as voucher_redeem_user_id', 'voucher_redeems.usage_status as voucher_redeem_usage_status', 'voucher_redeems.id as voucher_redeem_id')
                                            ->get();

        $without_redeem_vouchers_raw = Voucher::where('status', 'active')
                                            // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                            ->where('is_redeemable', 2)
                                            ->where('is_customer_specific', 0)
                                            ->where(function ($query) use ($customer) {
                                                $query->whereNull('max_order_per_customer')
                                                    ->orWhereColumn('max_order_per_customer', '>', DB::raw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ' . $customer->id . ')'));
                                                    // ->orWhereRaw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ?) < vouchers.max_order_per_customer', [$customer->id]);
                                                })                                      
                                            ->get();

        $without_redeem_vouchers = $without_redeem_vouchers_raw->flatMap(function ($voucher) use ($customer) {
                                                $usageCount = DB::table('voucher_usages')
                                                    ->where('voucher_id', $voucher->id)
                                                    ->where('user_id', $customer->id)
                                                    ->count();
                                            
                                                $max = $voucher->max_order_per_customer;
                                            
                                                // If max is null, treat as unlimited — include once
                                                if (is_null($max)) {
                                                    return collect([$voucher]);
                                                }
                                            
                                                $remaining = $max - $usageCount;
                                            
                                                return $remaining > 0
                                                    ? collect(array_fill(0, $remaining, $voucher))
                                                    : collect(); // no more allowed uses
                                            });  

        $without_redeem_customer_vouchers_raw = Voucher::where('status', 'active')
                                            // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                            ->where('is_redeemable', 2)
                                            ->where('is_customer_specific', 1)
                                            ->whereHas('voucher_user', function ($query) use ($customer) {
                                                $query->where('user_id', $customer->id);
                                            })
                                            ->where(function ($query) use ($customer) {
                                                $query->whereNull('max_order_per_customer')
                                                    ->orWhereColumn('max_order_per_customer', '>', DB::raw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ' . $customer->id . ')'));
                                                    // ->orWhereRaw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ?) < vouchers.max_order_per_customer', [$customer->id]);
                                                })                                      
                                            ->get();

        $without_redeem_customer_vouchers = $without_redeem_customer_vouchers_raw->flatMap(function ($voucher) use ($customer) {
                                                $usageCount = DB::table('voucher_usages')
                                                    ->where('voucher_id', $voucher->id)
                                                    ->where('user_id', $customer->id)
                                                    ->count();
                                            
                                                $max = $voucher->max_order_per_customer;
                                            
                                                // If max is null, treat as unlimited — include once
                                                if (is_null($max)) {
                                                    return collect([$voucher]);
                                                }
                                            
                                                $remaining = $max - $usageCount;
                                            
                                                return $remaining > 0
                                                    ? collect(array_fill(0, $remaining, $voucher))
                                                    : collect(); // no more allowed uses
                                            });      
    
        // $availableVoucher = $redeemed_vouchers
        //                     ->merge($without_redeem_vouchers)                                        
        //                     ->merge($without_redeem_customer_vouchers);

        $availableVoucher = collect()
                        ->concat($redeemed_vouchers)
                        ->concat($without_redeem_vouchers)
                        ->concat($without_redeem_customer_vouchers)
                        ->values();

        $filtered_available_vouchers = collect();

        foreach($availableVoucher as $voucher)
        {
            if(!isset($voucher->voucher_redeem_id))
            {
                $voucher->voucher_redeem_id = "";
            }

            $voucher->formattedStartDate = !empty($voucher->start_date_time) ? (Carbon::parse($voucher->start_date_time)->format('j F Y')) : '';
            $voucher->formattedEndDate = !empty($voucher->end_date_time) ? (Carbon::parse($voucher->end_date_time)->format('j F Y')) : '';

            // check age

            if($voucher->max_age >= $customer_age && $voucher->min_age <= $customer_age)
            {
                $voucher->isAgeEligible = true;
            }
            else
            {
                $voucher->isAgeEligible = false;
            }

            // check gender

            if(DB::table('voucher_gender')->where('voucher_id', $voucher->id)->where('gender', $customer_gender)->exists())
            {
                $voucher->isGenderEligible = true;
            }
            else
            {
                $voucher->isGenderEligible = false;
            }

            // validity start

            $voucher->validUntil = "";
              
            if($voucher->is_redeemable == 1)            
            {                      
                $VoucherRedeem = VoucherRedeem::where('voucher_id', $voucher->id)->where('user_id', $customer->id)->where('id', $voucher->voucher_redeem_id)->where('usage_status', 0)->first();
                
                if($VoucherRedeem)
                {
                    if ($voucher->validity_type == 'years') {
                    $validUntil = Carbon::parse($VoucherRedeem->created_at)->addYears($voucher->validity);
                    } elseif ($voucher->validity_type == 'months') {
                        $validUntil = Carbon::parse($VoucherRedeem->created_at)->addMonths($voucher->validity);
                    } else {
                        $validUntil = null;
                    }

                    $voucher->validUntil = date('d M Y', strtotime($validUntil));           
                }                    
            }
            else
            {
                if($voucher->is_customer_specific == 1)
                {                         
                    $VoucherUser = VoucherUser::where('voucher_id', $voucher->id)->where('user_id', $customer->id)->first();
                    
                    if($VoucherUser)
                    {
                        if ($voucher->validity_type == 'years') {
                            $validUntil = Carbon::parse($VoucherUser->created_at)->addYears($voucher->validity);
                        } elseif ($voucher->validity_type == 'months') {
                            $validUntil = Carbon::parse($VoucherUser->created_at)->addMonths($voucher->validity);
                        } else {
                            $validUntil = null;
                        }

                        $voucher->validUntil = date('d M Y', strtotime($validUntil));       
                    }                           
                }
                else
                {                        
                    if ($voucher->validity_type == 'years') {
                        $validUntil = Carbon::parse($voucher->created_at)->addYears($voucher->validity);
                    } elseif ($voucher->validity_type == 'months') {
                        $validUntil = Carbon::parse($voucher->created_at)->addMonths($voucher->validity);
                    } else {
                        $validUntil = null;
                    }

                    $voucher->validUntil = date('d M Y', strtotime($validUntil));                                                                                                   
                }
            }

            // validity end

            // Apply filter condition
            // if($voucher->voucher_type == 1)
            // {
            //     if ($voucher->isAgeEligible === true && $voucher->isGenderEligible === true) 
            //     {
            //         $filtered_available_vouchers->push($voucher);
            //     }
            // }
            // else
            // {
            //     $filtered_available_vouchers->push($voucher);
            // }

            // Apply filter condition
            $filtered_available_vouchers->push($voucher);
        }
        
        // *** voucher end ***

        $availableOffers = Offer::where('status', 'active')
            ->whereDoesntHave('users', function ($query) use ($customer) {
                $query->where('user_id', $customer->id);
            })->get();
        $claimedOffers = Offer::where('status', 'active')
            ->whereHas('users', function ($query) use ($customer) {
                $query->where('user_id', $customer->id);
            })
            ->get();
        $packages = Package::all();    

        // loyalty point
        $loyaltyPoints = LoyaltyPoint::where('user_id', $id)->get();

        if (\request()->ajax()) {
            $view = view('admin.customer.ajax_show', compact('customer'))->render();
            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }

        $completedBookings = Booking::where('user_id', $id)->where('status', 'completed')->count();
        $approvedBookings = Booking::where('user_id', $id)->where('status', 'approved')->count();
        $pendingBookings = Booking::where('user_id', $id)->where('status', 'pending')->count();
        $canceledBookings = Booking::where('user_id', $id)->where('status', 'canceled')->count();
        $inProgressBookings = Booking::where('user_id', $id)->where('status', 'in progress')->count();
        $earning = Booking::where('user_id', $id)->where('status', 'completed')->sum('amount_to_pay');

        $categories = Category::where('status', 'active')->orderBy('order_level', 'desc')->get();

        // loyalty program start

        $outlet = Outlet::where('status', 'active')->get();
        $categories_loyalty_program = Category::where('status', 'active')
                                                ->where('is_loyalty_program', 1)
                                                ->orderBy('order_level', 'desc')
                                                ->get();

        $LoyaltyProgramHistory = LoyaltyProgramHistory::where('user_id', $customer->id)->get();

        foreach($LoyaltyProgramHistory as $item)
        {
            $loyalty_services_id_arr = DB::table('loyalty_program_history_services')->where('loyalty_program_history_id', $item->id)->pluck('service_id')->toArray();
            $loyalty_services_name_arr = BusinessService::whereIn('id', $loyalty_services_id_arr)->pluck('name')->toArray();
            $item->services_name = implode(', ', $loyalty_services_name_arr);
        }

        // loyalty program end

        // health questions start

        $health_qstn = HealthQuestionController::get_data($customer->id);
        
        // health questions end

        // *** loyalty shop product start ***

        $used_loyalty_shop_product = LoyaltyShopUsage::join('loyalty_shops', 'loyalty_shop_usages.loyalty_shop_id', '=', 'loyalty_shops.id')
                                        ->where('loyalty_shops.status', 'active')
                                        ->where('loyalty_shop_usages.user_id', $customer->id)
                                        ->select('loyalty_shops.*', 'loyalty_shop_usages.id as loyalty_shop_usage_id', 'loyalty_shop_usages.created_at as used_on_date')
                                        ->get();

        $used_loyalty_shop_product->each(function ($item) {
            $item->formattedStartDate = !empty($item->start_date_time) ? (Carbon::parse($item->start_date_time)->format('j F Y')) : '';
            $item->formattedEndDate = !empty($item->end_date_time) ? (Carbon::parse($item->end_date_time)->format('j F Y')) : '';
            $item->used_on_date_format = Carbon::parse($item->used_on_date)->format('j F Y');
        });


        $available_loyalty_shop_product = [];

        $redeemed_loyalty_shops = LoyaltyShopRedeem::join('loyalty_shops', 'loyalty_shop_redeems.loyalty_shop_id', '=', 'loyalty_shops.id')
                                            ->where('loyalty_shop_redeems.user_id', $customer->id)
                                            ->where('loyalty_shop_redeems.usage_status', 0)
                                            // ->whereDate('loyalty_shops.start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('loyalty_shops.end_date_time', '>=', date('Y-m-d'))
                                            ->where('loyalty_shops.status', 'active')
                                            ->where('is_redeemable', 1)
                                            ->select('loyalty_shops.*', 'loyalty_shop_redeems.user_id as loyalty_shops_redeem_user_id', 'loyalty_shop_redeems.usage_status as loyalty_shops_redeem_usage_status', 'loyalty_shop_redeems.id as loyalty_shop_redeem_id')
                                            ->get();

        $without_redeem_loyalty_shops = LoyaltyShop::where('status', 'active')
                                            // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                            ->where('is_redeemable', 2)
                                            ->where('is_customer_specific', 0)  
                                            ->whereNotIn('id', function ($query) use ($customer) {
                                                $query->select('loyalty_shop_id')
                                                        ->from('loyalty_shop_usages')
                                                        ->where('user_id', $customer->id);
                                            })
                                            ->get();

        $without_redeem_customer_loyalty_shops = LoyaltyShop::where('status', 'active')
                                            // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                            ->where('is_redeemable', 2)
                                            ->where('is_customer_specific', 1)
                                            ->whereHas('loyalty_shop_user', function ($query) use ($customer) {
                                                $query->where('user_id', $customer->id);
                                            })
                                            ->whereNotIn('id', function ($query) use ($customer) {
                                                $query->select('loyalty_shop_id')
                                                        ->from('loyalty_shop_usages')
                                                        ->where('user_id', $customer->id);
                                            })                                  
                                            ->get();

        $available_loyalty_shop_product = collect()
                                        ->concat($redeemed_loyalty_shops)
                                        ->concat($without_redeem_loyalty_shops)
                                        ->concat($without_redeem_customer_loyalty_shops)
                                        ->values();

        $filtered_available_loyalty_shop_product = collect();

        foreach($available_loyalty_shop_product as $item)
        {
            if(!isset($item->loyalty_shop_redeem_id))
            {
                $item->loyalty_shop_redeem_id = "";
            }

            $item->formattedStartDate = !empty($item->start_date_time) ? (Carbon::parse($item->start_date_time)->format('j F Y')) : '';
            $item->formattedEndDate = !empty($item->end_date_time) ? (Carbon::parse($item->end_date_time)->format('j F Y')) : '';

            // check age

            if($item->max_age >= $customer_age && $item->min_age <= $customer_age)
            {
                $item->isAgeEligible = true;
            }
            else
            {
                $item->isAgeEligible = false;
            }

            // check gender

            if(DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $item->id)->where('gender', $customer_gender)->exists())
            {
                $item->isGenderEligible = true;
            }
            else
            {
                $item->isGenderEligible = false;
            }

            // validity start

            $item->validUntil = "";
              
            if($item->is_redeemable == 1)            
            {                      
                $LoyaltyShopRedeem = LoyaltyShopRedeem::where('loyalty_shop_id', $item->id)->where('user_id', $customer->id)->where('id', $item->loyalty_shop_redeem_id)->where('usage_status', 0)->first();
                
                if($LoyaltyShopRedeem)
                {
                    if ($item->validity_type == 'years') {
                    $validUntil = Carbon::parse($LoyaltyShopRedeem->created_at)->addYears($item->validity);
                    } elseif ($item->validity_type == 'months') {
                        $validUntil = Carbon::parse($LoyaltyShopRedeem->created_at)->addMonths($item->validity);
                    } else {
                        $validUntil = null;
                    }

                    $item->validUntil = date('d M Y', strtotime($validUntil));           
                }                    
            }
            else
            {
                if($item->is_customer_specific == 1)
                {                         
                    $LoyaltyShopUser = LoyaltyShopUser::where('loyalty_shop_id', $item->id)->where('user_id', $customer->id)->first();
                    
                    if($LoyaltyShopUser)
                    {
                        if ($item->validity_type == 'years') {
                            $validUntil = Carbon::parse($LoyaltyShopUser->created_at)->addYears($item->validity);
                        } elseif ($item->validity_type == 'months') {
                            $validUntil = Carbon::parse($LoyaltyShopUser->created_at)->addMonths($item->validity);
                        } else {
                            $validUntil = null;
                        }

                        $item->validUntil = date('d M Y', strtotime($validUntil));       
                    }                           
                }
                else
                {                        
                    if ($item->validity_type == 'years') {
                        $validUntil = Carbon::parse($item->created_at)->addYears($item->validity);
                    } elseif ($item->validity_type == 'months') {
                        $validUntil = Carbon::parse($item->created_at)->addMonths($item->validity);
                    } else {
                        $validUntil = null;
                    }

                    $item->validUntil = date('d M Y', strtotime($validUntil));                                                                                                   
                }
            }

            // validity end

            // Apply filter condition           
            // if ($item->isAgeEligible === true && $item->isGenderEligible === true) 
            // {
            //     $filtered_available_loyalty_shop_product->push($item);
            // }   

            // Apply filter condition
            $filtered_available_loyalty_shop_product->push($item);
        }

        // *** loyalty shop product end ***

        // dd($customer, $usedCoupons, $unusedCoupons, $usedVoucher, $availableVoucher, $availableOffers, $claimedOffers, $packages, $loyaltyPoints);

        return view('admin.customer.show', compact('customer', 'assignedPackages', 'packages', 'availableOffers', 'loyaltyPoints', 'claimedOffers', 'completedBookings', 'approvedBookings', 'pendingBookings', 'inProgressBookings', 'canceledBookings', 'earning', 'unusedCoupons', 'usedCoupons', 'filtered_available_vouchers', 'usedVoucher', 'categories', 'categories_loyalty_program', 'LoyaltyProgramHistory', 'outlet', 'health_qstn', 'filtered_available_loyalty_shop_product', 'used_loyalty_shop_product'));
    }

    // OLD Code of show customer
    
    // public function show($id)
    // {
    //     abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

    //     $customer = User::findOrFail($id);
    //     // dd($customer);
    //     $usedCoupons = Coupon::where('status', 'active')
    //         ->whereHas('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })
    //         ->get();

    //     $unusedCoupons = Coupon::where('status', 'active')
    //         ->whereDoesntHave('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })
    //         ->get();
    //     $usedCoupons->each(function ($coupon) {
    //         $coupon->formattedStartDate = Carbon::parse($coupon->start_date_time)->format('j F Y');
    //         $coupon->formattedEndDate = Carbon::parse($coupon->end_date_time)->format('j F Y');
    //     });
    //     $unusedCoupons->each(function ($coupon) {
    //         $coupon->formattedStartDate = Carbon::parse($coupon->start_date_time)->format('j F Y');
    //         $coupon->formattedEndDate = Carbon::parse($coupon->end_date_time)->format('j F Y');
    //     });

    //     $usedVoucher = Voucher::where('status', 'active')
    //         ->whereHas('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })
    //         ->get();
    //     $usedVoucher->each(function ($voucher) {
    //         $voucher->formattedEndDate = Carbon::parse($voucher->end_date_time)->format('j F Y');
    //     });
    //     $availableVoucher = Voucher::where('status', 'active')
    //         ->whereDoesntHave('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })->get();
    //     $availableVoucher->each(function ($voucher) {
    //         $voucher->formattedEndDate = Carbon::parse($voucher->end_date_time)->format('j F Y');
    //     });

    //     $availableOffers = Offer::where('status', 'active')
    //         ->whereDoesntHave('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })->get();
    //     $claimedOffers = Offer::where('status', 'active')
    //         ->whereHas('users', function ($query) use ($customer) {
    //             $query->where('user_id', $customer->id);
    //         })
    //         ->get();
    //     $packages = Package::all();    

    //     $loyaltyPoints = LoyaltyPoint::where('user_id', $id)->get();

    //     if (\request()->ajax()) {
    //         $view = view('admin.customer.ajax_show', compact('customer'))->render();
    //         return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    //     }

    //     $completedBookings = Booking::where('user_id', $id)->where('status', 'completed')->count();
    //     $approvedBookings = Booking::where('user_id', $id)->where('status', 'approved')->count();
    //     $pendingBookings = Booking::where('user_id', $id)->where('status', 'pending')->count();
    //     $canceledBookings = Booking::where('user_id', $id)->where('status', 'canceled')->count();
    //     $inProgressBookings = Booking::where('user_id', $id)->where('status', 'in progress')->count();
    //     $earning = Booking::where('user_id', $id)->where('status', 'completed')->sum('amount_to_pay');

    //     return view('admin.customer.show', compact('customer','packages', 'availableOffers', 'loyaltyPoints', 'claimedOffers', 'completedBookings', 'approvedBookings', 'pendingBookings', 'inProgressBookings', 'canceledBookings', 'earning', 'unusedCoupons', 'usedCoupons', 'availableVoucher', 'usedVoucher'));
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_customer'), 403);

        $customer = User::find($id);
        $branches = Location::all();
        $outlet = Outlet::where('status', 'active')->get();

        return view('admin.customer.edit', compact('customer', 'branches', 'outlet'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomer $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_customer'), 403);

        $user = User::find($id);
        // $user->prefix = $request->prefix;
        // $user->fname = $request->fname;
        // $user->lname = $request->lname;
        // $user->name = $request->fname . " " . $request->lname;
        $user->name = $request->full_name;
        $user->email = $request->email;
        // $user->branch_id = $request->branch;
        $user->outlet_id = $request->outlet_id;

        if($request->filled('dob'))
        {
            $user->dob = $request->dob;
        }

        if($request->filled('gender'))
        {
            $user->gender = $request->gender;     
        }
        
        if ($request->password != '') 
        {
            // $user->password = Hash::make($request->password);
            $user->password = $request->password;
        }

        if ($request->hasFile('image')) 
        {
            $user->image = Files::upload($request->image, 'avatar');
        }

        $user->status = $request->status;
        $user->save();

        return Reply::redirect(route('admin.customers.show', $id), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_customer'), 403);

        if(User::find($id))
        {
            CouponUser::where('user_id', $id)->delete();
            CouponRedeem::where('user_id', $id)->delete();
            CouponUsage::where('user_id', $id)->delete();

            Feedback::where('customer_id', $id)->delete();
            
            HealthQuestion::where('customer_id', $id)->delete();
            DB::table('health_question_customer_signatures')->where('customer_id', $id)->delete();

            LoyaltyPoint::where('user_id', $id)->delete();

            LoyaltyShopUser::where('user_id', $id)->delete();
            LoyaltyShopRedeem::where('user_id', $id)->delete();
            LoyaltyShopUsage::where('user_id', $id)->delete();

            VoucherUser::where('user_id', $id)->delete();
            VoucherRedeem::where('user_id', $id)->delete();
            VoucherUsage::where('user_id', $id)->delete();

            User::destroy($id);
        }

        return Reply::redirect(route('admin.customers.index'), __('messages.recordDeleted'));
    }

    public function customerBookings($id)
    {
    }

    public function storeLoyaltyPoints(Request $request, $customer_id)
    {
        // return $request->all();

        if($request->filled('loyalty_points'))
        {
            if($request->loyalty_points > 0)
            {
                LoyaltyPointController::coins_add($request->loyalty_points, $customer_id);

                $user = User::find($customer_id);
                     
                $timezone = config('app.timezone'); 
                $rowData = [
                    'loyaltyPoints' => $request->loyalty_points,
                    'date' => now()->setTimezone($timezone)->format('j F Y'), 
                    'time' => now()->setTimezone($timezone)->format('g:i A'), 
                    'points_type' => 'plus'
                ];
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Loyalty Coins saved successfully',
                    'loyaltyPoints' => $user->loyalty_points,
                    'rowData' => $rowData,
                ]);
            }
            else
            {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Loyalty Coins must be greater than 0',
                ]);
            }
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Loyalty Coins is required',
            ]);
        }
    }

    // store coupon used

    public function store_coupon_used(Request $request)
    {
        // return $request->all();

        $coupon_id = $request->coupon_id;
        $customer_id = $request->customer_id;

        // customer

        $customer = User::find($customer_id);

        if($customer)
        {
            // $customer_dob = date('Y-m-d', strtotime($customer->dob));
            $customer_dob = !empty($customer->dob) ? date('Y-m-d', strtotime($customer->dob)) : '';
            $customer_age = Carbon::parse($customer_dob)->age;
            $customer_gender = $customer->gender;
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Customer not found'
            ]);
        }

        // coupon

        $coupon = Coupon::find($coupon_id);

        if($coupon)
        {
            // check coupon uses limit active or not start

            if($coupon->uses_limit == null || $coupon->uses_limit == "")
            {
                $used_status = 'active';
            }
            else if($coupon->uses_limit == 0)
            {
                $used_status = 'deactive';
            }
            else
            {
                if(($coupon->used_time != $coupon->uses_limit))
                {
                    $used_status = 'active';
                }
                else
                {
                    $used_status = 'deactive';
                }
            }

            // check coupon uses limit active or not end

            if($coupon->is_customer_specific == 0)
            {
                $customer_can_redeem = "yes";
            }
            else
            {
                if(CouponUser::where('user_id', $customer_id)->where('coupon_id', $coupon_id)->exists())
                {
                    $customer_can_redeem = "yes";
                }
                else
                {
                    $customer_can_redeem = "no";
                }
            }

            if($used_status == "active")
            {
                if(strtotime(date('Y-m-d H:i:s')) >= strtotime($coupon->start_date_time) && strtotime(date('Y-m-d H:i:s')) <= strtotime($coupon->end_date_time))
                {
                    if(!CouponUsage::where('user_id', $customer_id)->where('coupon_id', $coupon_id)->exists())
                    {
                        if($coupon->max_age >= $customer_age && $coupon->min_age <= $customer_age)
                        {
                            if(DB::table('coupon_gender')->where('coupon_id', $coupon_id)->where('gender', $customer_gender)->exists())
                            {
                                if($customer_can_redeem == "yes")
                                {
                                    $coupon_usage = new CouponUsage;
                                    $coupon_usage->user_id = $customer_id;
                                    $coupon_usage->coupon_id = $coupon_id;
                                    $coupon_usage->save();

                                    $coupon->used_time = ($coupon->used_time + 1);
                                    $coupon->save();

                                    return response()->json([
                                        'status' => 'success',
                                        'message' => 'Data Saved Succesfully'
                                    ]);
                                }
                                else
                                {
                                    return response()->json([
                                        'status' => 'failed',
                                        'message' => 'Customer is not eligible to use this coupon'
                                    ]);
                                }   
                            }
                            else
                            {
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => 'Customer gender is not eligible to use this coupon'
                                ]);
                            }                         
                        }
                        else
                        {
                            return response()->json([
                                'status' => 'failed',
                                'message' => 'Customer age is not eligible to use this coupon'
                            ]);
                        }     
                    }
                    else
                    {                                        
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'Customer already used this Coupon'
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Coupon expired or not started"
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'status' => 'failed',
                    'message' => "All Coupons are used"
                ]);
            }
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Coupon not found'
            ]);
        }
    }

    // store voucher used

    // public function store_voucher_used(Request $request)
    // {
    //     // return $request->all();

    //     $voucher_id = $request->voucher_id;
    //     $customer_id = $request->customer_id;

    //     // customer

    //     $customer = User::find($customer_id);

    //     if($customer)
    //     {
    //         $customer_dob = date('Y-m-d', strtotime($customer->dob));
    //         $customer_age = Carbon::parse($customer_dob)->age;
    //         $customer_gender = $customer->gender;
    //     }
    //     else
    //     {
    //         return response()->json([
    //             'status' => 'failed',
    //             'message' => 'Customer not found'
    //         ]);
    //     }

    //     // voucher

    //     $voucher = Voucher::find($voucher_id);

    //     if($voucher)
    //     {     
    //         if(strtotime(date('Y-m-d H:i:s')) >= strtotime($voucher->start_date_time) && strtotime(date('Y-m-d H:i:s')) <= strtotime($voucher->end_date_time))
    //         {               
    //             if(VoucherRedeem::where('user_id', $customer_id)->where('voucher_id', $voucher_id)->where('usage_status', 0)->exists())  
    //             {                                   
    //                 $voucher_usage = new VoucherUsage;
    //                 $voucher_usage->user_id = $customer_id;
    //                 $voucher_usage->voucher_id = $voucher_id;
    //                 $voucher_usage->save();

    //                 $voucher_redeem = VoucherRedeem::where('voucher_id', $voucher_id)->where('user_id', $customer_id)->where('usage_status', 0)->first();
    //                 $voucher_redeem->usage_status = 1;
    //                 $voucher_redeem->save();

    //                 return response()->json([
    //                     'status' => 'success',
    //                     'message' => 'Data Saved Succesfully'
    //                 ]);        
    //             }
    //             else
    //             {
    //                 return response()->json([
    //                     'status' => 'failed',
    //                     'message' => "Voucher is not redeemed"
    //                 ]);
    //             }                         
    //         }
    //         else
    //         {
    //             return response()->json([
    //                 'status' => 'failed',
    //                 'message' => "Voucher expired or not started"
    //             ]);
    //         }
    //     }
    //     else
    //     {
    //         return response()->json([
    //             'status' => 'failed',
    //             'message' => 'Voucher not found'
    //         ]);
    //     }
    // }

    public function store_voucher_used(Request $request)
    {
        // return $request->all();

        $voucher_id = $request->voucher_id;
        $voucher_redeem_id = $request->voucher_redeem_id;
        $customer_id = $request->customer_id;

        // customer

        $customer = User::find($customer_id);

        if($customer)
        {
            // $customer_dob = date('Y-m-d', strtotime($customer->dob));
            $customer_dob = !empty($customer->dob) ? date('Y-m-d', strtotime($customer->dob)) : '';
            $customer_age = Carbon::parse($customer_dob)->age;
            $customer_gender = $customer->gender;
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Customer not found'
            ]);
        }

        // voucher

        $voucher = Voucher::find($voucher_id);

        $is_used = false;

        if($voucher)
        {     
            if($voucher->is_redeemable == 1)            
            {
                if(count(VoucherUsage::where('user_id', $customer_id)->where('voucher_id', $voucher_id)->get()) < $voucher->max_order_per_customer)
                {
                    $VoucherRedeem = VoucherRedeem::where('voucher_id', $voucher_id)->where('user_id', $customer_id)->where('id', $voucher_redeem_id)->where('usage_status', 0)->first();

                    if($VoucherRedeem)  
                    {                                                                                      
                        if ($voucher->validity_type == 'years') {
                            $validUntil = Carbon::parse($VoucherRedeem->created_at)->addYears($voucher->validity);
                        } elseif ($voucher->validity_type == 'months') {
                            $validUntil = Carbon::parse($VoucherRedeem->created_at)->addMonths($voucher->validity);
                        } else {
                            $validUntil = null;
                        }

                        if ($validUntil && Carbon::now()->lessThanOrEqualTo($validUntil))
                        {
                            $is_used = true;
                        }                           
                        else
                        {
                            return response()->json([
                                'status' => 'failed',
                                'message' => "Voucher validity is over"
                            ]);
                        }                                                                                              
                    }
                    else
                    {
                        return response()->json([
                            'status' => 'failed',
                            'message' => "Voucher is not redeemed"
                        ]);
                    }    
                } 
                else
                {
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Voucher is already used"
                    ]);
                }   
            }     
            else
            {                    
                // check voucher uses limit active or not end

                if($voucher->is_customer_specific == 0)
                {
                    $customer_can_use = "yes";
                }
                else
                {
                    if(VoucherUser::where('user_id', $customer_id)->where('voucher_id', $voucher_id)->exists())
                    {
                        $customer_can_use = "yes";
                    }
                    else
                    {
                        $customer_can_use = "no";
                    }
                }

                if(count(VoucherUsage::where('user_id', $customer_id)->where('voucher_id', $voucher_id)->get()) < $voucher->max_order_per_customer)
                {
                    if($voucher->max_age >= $customer_age && $voucher->min_age <= $customer_age)
                    {
                        if(DB::table('voucher_gender')->where('voucher_id', $voucher_id)->where('gender', $customer_gender)->exists())
                        { 
                            if($customer_can_use == "yes")
                            {
                                if($voucher->is_customer_specific == 1)
                                {                        
                                    $VoucherUser = VoucherUser::where('voucher_id', $voucher_id)->where('user_id', $customer_id)->first();
                                    
                                    if($VoucherUser)
                                    {
                                        if ($voucher->validity_type == 'years') {
                                            $validUntil = Carbon::parse($VoucherUser->created_at)->addYears($voucher->validity);
                                        } elseif ($voucher->validity_type == 'months') {
                                            $validUntil = Carbon::parse($VoucherUser->created_at)->addMonths($voucher->validity);
                                        } else {
                                            $validUntil = null;
                                        }                                  

                                        if ($validUntil && Carbon::now()->lessThanOrEqualTo($validUntil))
                                        {
                                            $is_used = true;
                                        }                           
                                        else
                                        {
                                            return response()->json([
                                                'status' => 'failed',
                                                'message' => "Voucher validity is over"
                                            ]);
                                        }   
                                    }
                                    else
                                    {
                                        return response()->json([
                                            'status' => false,
                                            'message' => 'You can not use this voucher'
                                        ]);
                                    }                       
                                }   
                                else
                                {                          
                                    if ($voucher->validity_type == 'years') {
                                        $validUntil = Carbon::parse($voucher->created_at)->addYears($voucher->validity);
                                    } elseif ($voucher->validity_type == 'months') {
                                        $validUntil = Carbon::parse($voucher->created_at)->addMonths($voucher->validity);
                                    } else {
                                        $validUntil = null;
                                    }

                                    if ($validUntil && Carbon::now()->lessThanOrEqualTo($validUntil))
                                    {
                                        $is_used = true;
                                    }                           
                                    else
                                    {
                                        return response()->json([
                                            'status' => 'failed',
                                            'message' => "Voucher validity is over"
                                        ]);
                                    }                          
                                }  
                            }
                            else
                            {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'You can not use this voucher'
                                ]);
                            }
                        }
                        else
                        {
                            return response()->json([
                                'status' => 'failed',
                                'message' => 'You are not eligible to use this voucher'
                            ]);
                        }  
                    }
                    else
                    {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'Your age is not eligible to use this voucher'
                        ]);
                    }   
                }   
                else
                {
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Voucher is already used"
                    ]);
                }                  
            }   
                                
            if($is_used == true)
            {
                $voucher_usage = new VoucherUsage();
                $voucher_usage->user_id = $customer_id;
                $voucher_usage->voucher_id = $voucher_id;
                $voucher_usage->save();

                $voucher_redeem = VoucherRedeem::where('voucher_id', $voucher_id)->where('user_id', $customer_id)->where('id', $voucher_redeem_id)->where('usage_status', 0)->first();
                
                if($voucher_redeem)
                {
                    $voucher_redeem->usage_status = 1;
                    $voucher_redeem->save();
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Voucher Used Succesfully'
                ]);  
            }
            else
            {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Voucher is not used'
                ]);
            }
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Voucher not found'
            ]);
        }
    }

    public function store_loyalty_shop_product_used(Request $request)
    {
        // return $request->all();

        $loyalty_shop_id = $request->loyalty_shop_id;
        $loyalty_shop_redeem_id = $request->loyalty_shop_redeem_id;
        $customer_id = $request->customer_id;

        // customer

        $customer = User::find($customer_id);

        if($customer)
        {
            // $customer_dob = date('Y-m-d', strtotime($customer->dob));
            $customer_dob = !empty($customer->dob) ? date('Y-m-d', strtotime($customer->dob)) : '';
            $customer_age = Carbon::parse($customer_dob)->age;
            $customer_gender = $customer->gender;
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Customer not found'
            ]);
        }

        // loyalty_shop

        $loyalty_shop = LoyaltyShop::find($loyalty_shop_id);

        $is_used = false;

        if($loyalty_shop)
        {     
            if($loyalty_shop->is_redeemable == 1)            
            {
                // if(count(LoyaltyShopUsage::where('user_id', $customer_id)->where('loyalty_shop_id', $loyalty_shop_id)->get()) == 0)
                // {
                    if($loyalty_shop->max_age >= $customer_age && $loyalty_shop->min_age <= $customer_age)
                    {
                        if(DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $loyalty_shop_id)->where('gender', $customer_gender)->exists())
                        { 
                            $LoyaltyShopRedeem = LoyaltyShopRedeem::where('loyalty_shop_id', $loyalty_shop_id)->where('user_id', $customer_id)->where('id', $loyalty_shop_redeem_id)->where('usage_status', 0)->first();

                            if($LoyaltyShopRedeem)  
                            {                                                                                      
                                if ($loyalty_shop->validity_type == 'years') {
                                    $validUntil = Carbon::parse($LoyaltyShopRedeem->created_at)->addYears($loyalty_shop->validity);
                                } elseif ($loyalty_shop->validity_type == 'months') {
                                    $validUntil = Carbon::parse($LoyaltyShopRedeem->created_at)->addMonths($loyalty_shop->validity);
                                } else {
                                    $validUntil = null;
                                }

                                if ($validUntil && Carbon::now()->lessThanOrEqualTo($validUntil))
                                {
                                    $is_used = true;
                                }                           
                                else
                                {
                                    return response()->json([
                                        'status' => 'failed',
                                        'message' => "Loyalty Shop validity is over"
                                    ]);
                                }                                                                                              
                            }
                            else
                            {
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => "Loyalty Shop is not redeemed"
                                ]);
                            } 
                        }
                        else
                        {
                            return response()->json([
                                'status' => 'failed',
                                'message' => 'You are not eligible to use this loyalty shop'
                            ]);
                        }  
                    }
                    else
                    {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'Your age is not eligible to use this loyalty shop'
                        ]);
                    }   

                // } 
                // else
                // {
                //     return response()->json([
                //         'status' => 'failed',
                //         'message' => "Loyalty Shop is already used"
                //     ]);
                // }   
            }     
            else
            {                    
                // check loyalty_shop uses limit active or not end

                if($loyalty_shop->is_customer_specific == 0)
                {
                    $customer_can_use = "yes";
                }
                else
                {
                    if(LoyaltyShopUser::where('user_id', $customer_id)->where('loyalty_shop_id', $loyalty_shop_id)->exists())
                    {
                        $customer_can_use = "yes";
                    }
                    else
                    {
                        $customer_can_use = "no";
                    }
                }

                if(count(LoyaltyShopUsage::where('user_id', $customer_id)->where('loyalty_shop_id', $loyalty_shop_id)->get()) == 0)
                {
                    if($loyalty_shop->max_age >= $customer_age && $loyalty_shop->min_age <= $customer_age)
                    {
                        if(DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $loyalty_shop_id)->where('gender', $customer_gender)->exists())
                        { 
                            if($customer_can_use == "yes")
                            {
                                if($loyalty_shop->is_customer_specific == 1)
                                {                        
                                    $LoyaltyShopUser = LoyaltyShopUser::where('loyalty_shop_id', $loyalty_shop_id)->where('user_id', $customer_id)->first();
                                    
                                    if($LoyaltyShopUser)
                                    {
                                        if ($loyalty_shop->validity_type == 'years') {
                                            $validUntil = Carbon::parse($LoyaltyShopUser->created_at)->addYears($loyalty_shop->validity);
                                        } elseif ($loyalty_shop->validity_type == 'months') {
                                            $validUntil = Carbon::parse($LoyaltyShopUser->created_at)->addMonths($loyalty_shop->validity);
                                        } else {
                                            $validUntil = null;
                                        }                                  

                                        if ($validUntil && Carbon::now()->lessThanOrEqualTo($validUntil))
                                        {
                                            $is_used = true;
                                        }                           
                                        else
                                        {
                                            return response()->json([
                                                'status' => 'failed',
                                                'message' => "LoyaltyShop validity is over"
                                            ]);
                                        }   
                                    }
                                    else
                                    {
                                        return response()->json([
                                            'status' => false,
                                            'message' => 'You can not use this loyalty shop'
                                        ]);
                                    }                       
                                }   
                                else
                                {                          
                                    if ($loyalty_shop->validity_type == 'years') {
                                        $validUntil = Carbon::parse($loyalty_shop->created_at)->addYears($loyalty_shop->validity);
                                    } elseif ($loyalty_shop->validity_type == 'months') {
                                        $validUntil = Carbon::parse($loyalty_shop->created_at)->addMonths($loyalty_shop->validity);
                                    } else {
                                        $validUntil = null;
                                    }

                                    if ($validUntil && Carbon::now()->lessThanOrEqualTo($validUntil))
                                    {
                                        $is_used = true;
                                    }                           
                                    else
                                    {
                                        return response()->json([
                                            'status' => 'failed',
                                            'message' => "Loyalty Shop validity is over"
                                        ]);
                                    }                          
                                }  
                            }
                            else
                            {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'You can not use this loyalty shop'
                                ]);
                            }
                        }
                        else
                        {
                            return response()->json([
                                'status' => 'failed',
                                'message' => 'You are not eligible to use this loyalty shop'
                            ]);
                        }  
                    }
                    else
                    {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'Your age is not eligible to use this loyalty shop'
                        ]);
                    }   
                }   
                else
                {
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Loyalty Shop is already used"
                    ]);
                }                  
            }   
                                
            if($is_used == true)
            {
                $loyalty_shop_usage = new LoyaltyShopUsage();
                $loyalty_shop_usage->user_id = $customer_id;
                $loyalty_shop_usage->loyalty_shop_id = $loyalty_shop_id;
                $loyalty_shop_usage->save();

                $loyalty_shop_redeem = LoyaltyShopRedeem::where('loyalty_shop_id', $loyalty_shop_id)->where('user_id', $customer_id)->where('id', $loyalty_shop_redeem_id)->where('usage_status', 0)->first();
                
                if($loyalty_shop_redeem)
                {
                    $loyalty_shop_redeem->usage_status = 1;
                    $loyalty_shop_redeem->save();
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Loyalty Shop Product Used Succesfully'
                ]);  
            }
            else
            {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Loyalty Shop Product is not used'
                ]);
            }
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Loyalty Shop Product not found'
            ]);
        }
    }
}
