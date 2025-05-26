<?php

namespace App\Http\Controllers\Api;

use App\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\LoyaltyPoint;
use App\LoyaltyShop;
use App\LoyaltyShopRedeem;
use App\LoyaltyShopUsage;
use App\Outlet;
use App\User;
use App\VoucherRedeem;
use App\VoucherUsage;
use App\VoucherUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VouchersController extends Controller
{
    public function redeemable_vouchers(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $voucher_id_arr = VoucherUser::where('user_id', $user_id)->pluck('voucher_id')->toArray();

        $customer_vouchers_raw = Voucher::where('status', 'active')
                                    // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                    // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                    ->where(function ($query) {
                                        $query->where(function ($q) {
                                            $q->whereNotNull('start_date_time')
                                              ->whereNotNull('end_date_time')
                                              ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                              ->whereDate('end_date_time', '>=', date('Y-m-d'));
                                        })->orWhere(function ($q) {
                                            $q->whereNull('start_date_time')
                                              ->orWhereNull('end_date_time');
                                        });
                                    })
                                    ->whereIn('id', $voucher_id_arr)
                                    ->where('is_customer_specific', 1)
                                    ->where('is_redeemable', 1)
                                    ->where('voucher_type', 1)
                                    ->get();

        // $customer_vouchers = $customer_vouchers_raw->flatMap(function ($voucher) use ($user_id) {
        //     $redeemCount = VoucherRedeem::where('voucher_id', $voucher->id)
        //                                 ->where('user_id', $user_id)
        //                                 ->count();
        
        //     $max = $voucher->max_order_per_customer;
        
        //     // If max is null, treat as unlimited — include once
        //     if (is_null($max)) {
        //         return collect([$voucher]);
        //     }
        
        //     $remaining = $max - $redeemCount;
        
        //     return $remaining > 0
        //         ? collect(array_fill(0, $remaining, $voucher))
        //         : collect(); // no more allowed uses
        // });  

        $customer_vouchers = $customer_vouchers_raw->flatMap(function ($voucher) use ($user_id) {
            $redeemCount = VoucherRedeem::where('voucher_id', $voucher->id)
                ->where('user_id', $user_id)
                ->count();
    
            $max = $voucher->max_order_per_customer;
    
            $remaining = is_null($max) ? 1 : max(0, $max - $redeemCount);
    
            return collect(range(1, $remaining))->map(fn () => clone $voucher);
        });

        $all_vouchers_raw = Voucher::where('status', 'active')
                                // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                ->where(function ($query) {
                                    $query->where(function ($q) {
                                        $q->whereNotNull('start_date_time')
                                          ->whereNotNull('end_date_time')
                                          ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                          ->whereDate('end_date_time', '>=', date('Y-m-d'));
                                    })->orWhere(function ($q) {
                                        $q->whereNull('start_date_time')
                                          ->orWhereNull('end_date_time');
                                    });
                                })
                                ->where('is_customer_specific', 0)
                                ->where('is_redeemable', 1)
                                ->where('voucher_type', 1)
                                ->get();

        // $all_vouchers = $all_vouchers_raw->flatMap(function ($voucher) use ($user_id) {
        //     $redeemCount = VoucherRedeem::where('voucher_id', $voucher->id)
        //                                 ->where('user_id', $user_id)
        //                                 ->count();
        
        //     $max = $voucher->max_order_per_customer;
        
        //     // If max is null, treat as unlimited — include once
        //     if (is_null($max)) {
        //         return collect([$voucher]);
        //     }
        
        //     $remaining = $max - $redeemCount;
        
        //     return $remaining > 0
        //         ? collect(array_fill(0, $remaining, $voucher))
        //         : collect(); // no more allowed uses
        // }); 
        
        $all_vouchers = $all_vouchers_raw->flatMap(function ($voucher) use ($user_id) {
            $redeemCount = VoucherRedeem::where('voucher_id', $voucher->id)
                ->where('user_id', $user_id)
                ->count();
    
            $max = $voucher->max_order_per_customer;
    
            $remaining = is_null($max) ? 1 : max(0, $max - $redeemCount);
    
            return collect(range(1, $remaining))->map(fn () => clone $voucher);
        });

        // $vouchers = $all_vouchers->merge($customer_vouchers);

        $vouchers = collect()
                        ->concat($all_vouchers)
                        ->concat($customer_vouchers)
                        ->values();

        $filtered_vouchers = collect();

        if (!$vouchers->isEmpty())
        {
            foreach ($vouchers as $loop_key=>$item) 
            {             
                // check voucher uses limit active or not

                // if($item->uses_limit == null || $item->uses_limit == "")
                // {
                //     $item->redeemed_status = 'active';
                // }
                // else if($item->uses_limit == 0)
                // {
                //     $item->redeemed_status = 'deactive';
                // }
                // else
                // {
                //     if(($item->used_time != $item->uses_limit))
                //     {
                //         $item->redeemed_status = 'active';
                //     }
                //     else
                //     {
                //         $item->redeemed_status = 'deactive';
                //     }
                // }

                // check voucher redeem or not

                if(count(VoucherRedeem::where('user_id', $user_id)->where('voucher_id', $item->id)->get()) == $item->max_order_per_customer)
                {
                    $item->isRedeemed = true;

                    // check voucher used or not

                    if(VoucherUsage::where('user_id', $user_id)->where('voucher_id', $item->id)->exists())
                    {
                        $item->isUsed = true;
                    }
                    else
                    {
                        $item->isUsed = false;
                    }
                }
                else
                {
                    $item->isRedeemed = false;
                    $item->isUsed = false;
                }
                
                // check age

                if($item->max_age >= $user_age && $item->min_age <= $user_age)
                {
                    $item->isAgeEligible = true;
                }
                else
                {
                    $item->isAgeEligible = false;
                }

                // check gender

                if(DB::table('voucher_gender')->where('voucher_id', $item->id)->where('gender', $user_gender)->exists())
                {
                    $item->isGenderEligible = true;
                }
                else
                {
                    $item->isGenderEligible = false;
                }

                // end date
                if(!empty($item->end_date_time))
                {
                    $item->expire_date = date('d M Y', strtotime($item->end_date_time));
                }
                else
                {
                    $item->expire_date = "";
                }
                
                // balance qty
                $VoucherRedeem_qty = VoucherRedeem::where('voucher_id', $item->id)->where('user_id', $user_id)->count();

                $item->balance_qty = $item->max_order_per_customer - $VoucherRedeem_qty;

                // unique id
                $item->custom_unique_id = $loop_key+1;
                
                // Apply filter condition
                // if ($item->isRedeemed === false && $item->isUsed === false && $item->isAgeEligible === true && $item->isGenderEligible === true) 
                // {
                //     $filtered_vouchers->push($item);
                // }

                // Apply filter condition
                if ($item->isRedeemed === false && $item->isUsed === false) 
                {
                    $filtered_vouchers->push($item);
                }
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Vouchers List',
                'data' => $filtered_vouchers
            ]);
        } 
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => [],
            ]);
        }
    }

    public function redeemable_vouchers_without_login()
    {
        $all_vouchers_raw = Voucher::where('status', 'active')
                                // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                ->where(function ($query) {
                                    $query->where(function ($q) {
                                        $q->whereNotNull('start_date_time')
                                          ->whereNotNull('end_date_time')
                                          ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                          ->whereDate('end_date_time', '>=', date('Y-m-d'));
                                    })->orWhere(function ($q) {
                                        $q->whereNull('start_date_time')
                                          ->orWhereNull('end_date_time');
                                    });
                                })
                                ->where('is_customer_specific', 0)
                                ->where('is_redeemable', 1)
                                ->where('voucher_type', 1)
                                ->get();

        // $all_vouchers = $all_vouchers_raw->flatMap(function ($voucher) use ($user_id) {
        //     $redeemCount = VoucherRedeem::where('voucher_id', $voucher->id)
        //                                 ->where('user_id', $user_id)
        //                                 ->count();
        
        //     $max = $voucher->max_order_per_customer;
        
        //     // If max is null, treat as unlimited — include once
        //     if (is_null($max)) {
        //         return collect([$voucher]);
        //     }
        
        //     $remaining = $max - $redeemCount;
        
        //     return $remaining > 0
        //         ? collect(array_fill(0, $remaining, $voucher))
        //         : collect(); // no more allowed uses
        // }); 

        $all_vouchers = $all_vouchers_raw->flatMap(function ($voucher) {

            $max = $voucher->max_order_per_customer;
        
            $remaining = is_null($max) ? 1 : $max;
        
            return collect(range(1, $remaining))->map(fn () => clone $voucher);
        });
      

        if (!$all_vouchers->isEmpty())
        {
            foreach ($all_vouchers as $loop_key=>$item) 
            {                            
                // end date
                if(!empty($item->end_date_time))
                {
                    $item->expire_date = date('d M Y', strtotime($item->end_date_time));
                }
                else
                {
                    $item->expire_date = "";
                }

                // unique id
                $item->custom_unique_id = $loop_key+1;              
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Vouchers List',
                'data' => $all_vouchers
            ]);
        } 
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => [],
            ]);
        }
        
    }

    // public function redeem_vouchers(Request $request) 
    // {
    //     $user_id = Auth::user()->id;
    //     $user = User::find($user_id);

    //     $user_dob = date('Y-m-d', strtotime($user->dob));
    //     $user_age = Carbon::parse($user_dob)->age;
    //     $user_gender = $user->gender;

    //     $voucher_id = $request->voucher_id;
    //     $voucher = Voucher::find($voucher_id);

    //     if($voucher)
    //     {        
    //         // check voucher uses limit active or not start

    //         // if($voucher->uses_limit == null || $voucher->uses_limit == "")
    //         // {
    //         //     $redeemed_status = 'active';
    //         // }
    //         // else if($voucher->uses_limit == 0)
    //         // {
    //         //     $redeemed_status = 'deactive';
    //         // }
    //         // else
    //         // {
    //         //     if(($voucher->used_time != $voucher->uses_limit))
    //         //     {
    //         //         $redeemed_status = 'active';
    //         //     }
    //         //     else
    //         //     {
    //         //         $redeemed_status = 'deactive';
    //         //     }
    //         // }

    //         // check voucher uses limit active or not end

    //         if($voucher->is_customer_specific == 0)
    //         {
    //             $customer_can_redeem = "yes";
    //         }
    //         else
    //         {
    //             if(VoucherUser::where('user_id', $user_id)->where('voucher_id', $voucher_id)->exists())
    //             {
    //                 $customer_can_redeem = "yes";
    //             }
    //             else
    //             {
    //                 $customer_can_redeem = "no";
    //             }
    //         }

    //         // if($redeemed_status == "active")
    //         // {     

    //             if(strtotime(date('Y-m-d')) >= strtotime($voucher->start_date_time) && strtotime(date('Y-m-d')) <= strtotime($voucher->end_date_time))
    //             {
    //                 if($voucher->max_age >= $user_age && $voucher->min_age <= $user_age)
    //                 {
    //                     if(DB::table('voucher_gender')->where('voucher_id', $voucher_id)->where('gender', $user_gender)->exists())
    //                     {                           
    //                         if($customer_can_redeem == "yes")
    //                         {
    //                             if(count(VoucherRedeem::where('user_id', $user_id)->where('voucher_id', $voucher_id)->get()) != $voucher->max_order_per_customer)
    //                             {
    //                                 if($voucher->is_welcome == 1)
    //                                 {
    //                                     $voucher_redeem = new VoucherRedeem;

    //                                     $voucher_redeem->voucher_id = $voucher_id;
    //                                     $voucher_redeem->user_id = $user_id;

    //                                     $result = $voucher_redeem->save();

    //                                     if($result)
    //                                     {
    //                                         $voucher->used_time += 1;
    //                                         $voucher->save();

    //                                         return response()->json([
    //                                             'status' => true,
    //                                             'message' => "Voucher is Redeemed",
    //                                         ]);
    //                                     }
    //                                     else
    //                                     {
    //                                         return response()->json([
    //                                             'status' => false,
    //                                             'message' => "Voucher is not Redeemed",
    //                                         ]);
    //                                     }
    //                                 }
    //                                 else
    //                                 {                                   
    //                                     if($user->loyalty_points >= $voucher->loyalty_point)
    //                                     {
    //                                         $voucher_redeem = new VoucherRedeem;

    //                                         $voucher_redeem->voucher_id = $voucher_id;
    //                                         $voucher_redeem->user_id = $user_id;

    //                                         $result = $voucher_redeem->save();

    //                                         if($result)
    //                                         {
    //                                             $voucher->used_time += 1;
    //                                             $voucher->save();

    //                                             // coin minus from user start

    //                                             if ($voucher->loyalty_point > 0)
    //                                             {
    //                                                 $user->loyalty_points -= $voucher->loyalty_point;
    //                                                 $user->save();

    //                                                 LoyaltyPointController::coins_used($voucher->loyalty_point);

    //                                                 // $LoyaltyPoint = new LoyaltyPoint();
    //                                                 // $LoyaltyPoint->user_id = $user_id;
    //                                                 // $LoyaltyPoint->points_type = 'minus';
    //                                                 // $LoyaltyPoint->loyalty_points = $voucher->loyalty_point;
    //                                                 // $LoyaltyPoint->description = "coins used for voucher redeemed";
    //                                                 // $LoyaltyPoint->save();
    //                                             }
                                
    //                                             // coin minus from user end

    //                                             return response()->json([
    //                                                 'status' => true,
    //                                                 'message' => "Voucher is Redeemed",
    //                                             ]);
    //                                         }
    //                                         else
    //                                         {
    //                                             return response()->json([
    //                                                 'status' => false,
    //                                                 'message' => "Voucher is not Redeemed",
    //                                             ]);
    //                                         }
    //                                     }
    //                                     else
    //                                     {
    //                                         return response()->json([
    //                                             'status' => false,
    //                                             'message' => "You don't have enough loyalty coins",
    //                                         ]);
    //                                     }
    //                                 }
    //                             }
    //                             else
    //                             {
    //                                 return response()->json([
    //                                     'status' => false,
    //                                     'message' => "You have already redeemed this voucher",
    //                                 ]);
    //                             }
    //                         }
    //                         else
    //                         {
    //                             return response()->json([
    //                                 'status' => false,
    //                                 'message' => 'You can not redeem this voucher'
    //                             ]);
    //                         }
    //                     }
    //                     else
    //                     {
    //                         return response()->json([
    //                             'status' => false,
    //                             'message' => 'You are not eligible to use this voucher'
    //                         ]);
    //                     }   
    //                 }
    //                 else
    //                 {
    //                     return response()->json([
    //                         'status' => false,
    //                         'message' => 'Your age is not eligible to use this voucher'
    //                     ]);
    //                 }   
    //             }
    //             else
    //             {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => "Voucher expired or not started"
    //                 ]);
    //             }  
                            
    //         // }
    //         // else
    //         // {
    //         //     return response()->json([
    //         //         'status' => false,
    //         //         'message' => "All Vouchers are redeemed",
    //         //     ]);
    //         // }
    //     }
    //     else
    //     {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Voucher not found",
    //         ]);
    //     }
    // }

    public function redeem_vouchers(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $voucher_id = $request->voucher_id;
        $voucher = Voucher::find($voucher_id);

        if($voucher)
        {      
            if($voucher->is_redeemable == 1)  
            {
                // check voucher uses limit active or not start

                // if($voucher->uses_limit == null || $voucher->uses_limit == "")
                // {
                //     $redeemed_status = 'active';
                // }
                // else if($voucher->uses_limit == 0)
                // {
                //     $redeemed_status = 'deactive';
                // }
                // else
                // {
                //     if(($voucher->used_time != $voucher->uses_limit))
                //     {
                //         $redeemed_status = 'active';
                //     }
                //     else
                //     {
                //         $redeemed_status = 'deactive';
                //     }
                // }

                // check voucher uses limit active or not end

                if($voucher->is_customer_specific == 0)
                {
                    $customer_can_redeem = "yes";
                }
                else
                {
                    if(VoucherUser::where('user_id', $user_id)->where('voucher_id', $voucher_id)->exists())
                    {
                        $customer_can_redeem = "yes";
                    }
                    else
                    {
                        $customer_can_redeem = "no";
                    }
                }

                // if($redeemed_status == "active")
                // {     

                    
                        // if(strtotime(date('Y-m-d')) >= strtotime($voucher->start_date_time) && strtotime(date('Y-m-d')) <= strtotime($voucher->end_date_time))                       
                        if((!empty($voucher->start_date_time) && !empty($voucher->end_date_time) && strtotime(date('Y-m-d')) >= strtotime($voucher->start_date_time) && strtotime(date('Y-m-d')) <= strtotime($voucher->end_date_time)) || (empty($voucher->start_date_time) || empty($voucher->end_date_time)))
                        {
                            // if($voucher->max_age >= $user_age && $voucher->min_age <= $user_age)
                            // {
                            //     if(DB::table('voucher_gender')->where('voucher_id', $voucher_id)->where('gender', $user_gender)->exists())
                            //     {       

                                    if($customer_can_redeem == "yes")
                                    {
                                        if(count(VoucherRedeem::where('user_id', $user_id)->where('voucher_id', $voucher_id)->get()) != $voucher->max_order_per_customer)
                                        {                                                                            
                                            if($user->loyalty_points >= $voucher->loyalty_point)
                                            {
                                                $voucher_redeem = new VoucherRedeem;

                                                $voucher_redeem->voucher_id = $voucher_id;
                                                $voucher_redeem->user_id = $user_id;

                                                $result = $voucher_redeem->save();

                                                if($result)
                                                {
                                                    $voucher->used_time += 1;
                                                    $voucher->save();

                                                    // coin minus from user start

                                                    if ($voucher->loyalty_point > 0)
                                                    {
                                                        $user->loyalty_points -= $voucher->loyalty_point;
                                                        $user->save();

                                                        LoyaltyPointController::coins_used($voucher->loyalty_point);

                                                        // $LoyaltyPoint = new LoyaltyPoint();
                                                        // $LoyaltyPoint->user_id = $user_id;
                                                        // $LoyaltyPoint->points_type = 'minus';
                                                        // $LoyaltyPoint->loyalty_points = $voucher->loyalty_point;
                                                        // $LoyaltyPoint->description = "coins used for voucher redeemed";
                                                        // $LoyaltyPoint->save();
                                                    }
                                    
                                                    // coin minus from user end

                                                    return response()->json([
                                                        'status' => true,
                                                        'message' => "Voucher is Redeemed",
                                                    ]);
                                                }
                                                else
                                                {
                                                    return response()->json([
                                                        'status' => false,
                                                        'message' => "Voucher is not Redeemed",
                                                    ]);
                                                }
                                            }
                                            else
                                            {
                                                return response()->json([
                                                    'status' => false,
                                                    'message' => "You don't have enough loyalty coins",
                                                ]);
                                            }                                           
                                        }
                                        else
                                        {
                                            return response()->json([
                                                'status' => false,
                                                'message' => "You have already redeemed this voucher",
                                            ]);
                                        }
                                    }
                                    else
                                    {
                                        return response()->json([
                                            'status' => false,
                                            'message' => 'You can not redeem this voucher'
                                        ]);
                                    }

                            //     }
                            //     else
                            //     {
                            //         return response()->json([
                            //             'status' => false,
                            //             'message' => 'You are not eligible to use this voucher'
                            //         ]);
                            //     }   
                            // }
                            // else
                            // {
                            //     return response()->json([
                            //         'status' => false,
                            //         'message' => 'Your age is not eligible to use this voucher'
                            //     ]);
                            // }   

                        }
                        else
                        {
                            return response()->json([
                                'status' => false,
                                'message' => "Voucher expired or not started"
                            ]);
                        }  
                    
                                
                // }
                // else
                // {
                //     return response()->json([
                //         'status' => false,
                //         'message' => "All Vouchers are redeemed",
                //     ]);
                // }
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => "Voucher is not redeemable",
                ]);
            }
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => "Voucher not found",
            ]);
        }
    }

    // public function my_vouchers(Request $request)
    // {
    //     $user_id = Auth::user()->id;

    //     $VoucherRedeem = VoucherRedeem::join('vouchers', 'voucher_redeems.voucher_id', '=', 'vouchers.id')
    //         ->where('voucher_redeems.user_id', $user_id)
    //         ->where('voucher_redeems.usage_status', 0)
    //         ->whereDate('vouchers.start_date_time', '<=', date('Y-m-d'))
    //         ->whereDate('vouchers.end_date_time', '>=', date('Y-m-d'))
    //         ->where('vouchers.status', 'active')
    //         ->select('vouchers.*', 'voucher_redeems.user_id', 'voucher_redeems.usage_status', 'voucher_redeems.id as voucher_redeem_id')
    //         ->get();

    //     if (!$VoucherRedeem->isEmpty()) 
    //     {
    //         foreach ($VoucherRedeem as $item) 
    //         {
    //             $item->end_date = date('d M Y', strtotime($item->end_date_time));
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'My Vouchers List',
    //             'data' => $VoucherRedeem
    //         ]);
    //     } 
    //     else 
    //     {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data not found',
    //             'data' => $VoucherRedeem
    //         ]);
    //     }
    // }

    public function my_vouchers(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $redeemed_vouchers = VoucherRedeem::join('vouchers', 'voucher_redeems.voucher_id', '=', 'vouchers.id')
                                        ->where('voucher_redeems.user_id', $user_id)
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
                                        ->where(function ($query) use ($user_id) {
                                            $query->whereNull('max_order_per_customer')
                                                ->orWhereColumn('max_order_per_customer', '>', DB::raw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ' . $user_id . ')'));
                                                // ->orWhereRaw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ?) < vouchers.max_order_per_customer', [$user_id]);
                                            })                                      
                                        ->get();

        $without_redeem_vouchers = $without_redeem_vouchers_raw->flatMap(function ($voucher) use ($user_id) {
                                        $usageCount = DB::table('voucher_usages')
                                            ->where('voucher_id', $voucher->id)
                                            ->where('user_id', $user_id)
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
                                            ->whereHas('voucher_user', function ($query) use ($user_id) {
                                                $query->where('user_id', $user_id);
                                            })
                                            ->where(function ($query) use ($user_id) {
                                                $query->whereNull('max_order_per_customer')
                                                    ->orWhereColumn('max_order_per_customer', '>', DB::raw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ' . $user_id . ')'));
                                                    // ->orWhereRaw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ?) < vouchers.max_order_per_customer', [$user_id]);
                                                })                                      
                                            ->get();

        $without_redeem_customer_vouchers = $without_redeem_customer_vouchers_raw->flatMap(function ($voucher) use ($user_id) {
                                                $usageCount = DB::table('voucher_usages')
                                                    ->where('voucher_id', $voucher->id)
                                                    ->where('user_id', $user_id)
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

        // returns unique rows using mearge()

        // $my_vouchers = $redeemed_vouchers
        //                     ->merge($without_redeem_vouchers)                                        
        //                     ->merge($without_redeem_customer_vouchers);

        $my_vouchers = collect()
                        ->concat($redeemed_vouchers)
                        ->concat($without_redeem_vouchers)
                        ->concat($without_redeem_customer_vouchers)
                        ->values();

        $filtered_vouchers = collect();

        if (!$my_vouchers->isEmpty()) 
        {
            foreach ($my_vouchers as $loop_key=>$item) 
            {           
                // check age

                if($item->max_age >= $user_age && $item->min_age <= $user_age)
                {
                    $item->isAgeEligible = true;
                }
                else
                {
                    $item->isAgeEligible = false;
                }

                // check gender

                if(DB::table('voucher_gender')->where('voucher_id', $item->id)->where('gender', $user_gender)->exists())
                {
                    $item->isGenderEligible = true;
                }
                else
                {
                    $item->isGenderEligible = false;
                }

                // end date
                if(!empty($item->end_date_time))
                {
                    $item->end_date_format = date('d M Y', strtotime($item->end_date_time));
                }
                else
                {
                    $item->end_date_format = "";
                }

                // voucher redeem id
                if(!isset($item->voucher_redeem_id))
                {
                    $item->voucher_redeem_id = "";
                }

                // validity start

                $item->validUntil = "";
              
                if($item->is_redeemable == 1)            
                {                      
                    $VoucherRedeem = VoucherRedeem::where('voucher_id', $item->id)->where('user_id', $user_id)->where('id', $item->voucher_redeem_id)->where('usage_status', 0)->first();
                    
                    if($VoucherRedeem)
                    {
                        if ($item->validity_type == 'years') {
                        $validUntil = Carbon::parse($VoucherRedeem->created_at)->addYears($item->validity);
                        } elseif ($item->validity_type == 'months') {
                            $validUntil = Carbon::parse($VoucherRedeem->created_at)->addMonths($item->validity);
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
                        $VoucherUser = VoucherUser::where('voucher_id', $item->id)->where('user_id', $user_id)->first();
                        
                        if($VoucherUser)
                        {
                            if ($item->validity_type == 'years') {
                                $validUntil = Carbon::parse($VoucherUser->created_at)->addYears($item->validity);
                            } elseif ($item->validity_type == 'months') {
                                $validUntil = Carbon::parse($VoucherUser->created_at)->addMonths($item->validity);
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

                // balance qty
                $VoucherUsage_qty = VoucherUsage::where('voucher_id', $item->id)->where('user_id', $user_id)->count();

                $item->balance_qty = $item->max_order_per_customer - $VoucherUsage_qty;
                
                // unique id
                $item->custom_unique_id = $loop_key+1;

                // Apply filter condition
                // if($item->voucher_type == 1)
                // {
                //     if ($item->isAgeEligible === true && $item->isGenderEligible === true) 
                //     {
                //         $filtered_vouchers->push($item);
                //     }
                // }
                // else
                // {
                //     $filtered_vouchers->push($item);
                // }

                // Apply filter condition
                $filtered_vouchers->push($item);
            }

            return response()->json([
                'status' => true,
                'message' => 'My Vouchers List',
                'data' => $filtered_vouchers
            ]);
        } 
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => []
            ]);
        }
    }

    // public function welcome_vouchers(Request $request)
    // {
    //     $user_id = Auth::user()->id;
    //     $user = User::find($user_id);

    //     $user_dob = date('Y-m-d', strtotime($user->dob));
    //     $user_age = Carbon::parse($user_dob)->age;
    //     $user_gender = $user->gender;

    //     $customer_redeem_welcome_vouchers = Voucher::where('status', 'active')
    //                                             // ->whereDate('start_date_time', '<=', date('Y-m-d'))
    //                                             // ->whereDate('end_date_time', '>=', date('Y-m-d'))
    //                                             ->where(function ($query) {
    //                                                 $query->where(function ($q) {
    //                                                     $q->whereNotNull('start_date_time')
    //                                                       ->whereNotNull('end_date_time')
    //                                                       ->whereDate('start_date_time', '<=', date('Y-m-d'))
    //                                                       ->whereDate('end_date_time', '>=', date('Y-m-d'));
    //                                                 })->orWhere(function ($q) {
    //                                                     $q->whereNull('start_date_time')
    //                                                       ->orWhereNull('end_date_time');
    //                                                 });
    //                                             })
    //                                             ->where('is_customer_specific', 1)
    //                                             ->where('is_welcome', 1)
    //                                             ->where('is_redeemable', 1)
    //                                             ->where('voucher_type', 1)
    //                                             ->whereHas('voucher_user', function ($query) use ($user_id) {
    //                                                 $query->where('user_id', $user_id);
    //                                             })
    //                                             ->get();

    //     $redeem_welcome_vouchers = Voucher::where('status', 'active')
    //                                         // ->whereDate('start_date_time', '<=', date('Y-m-d'))
    //                                         // ->whereDate('end_date_time', '>=', date('Y-m-d'))
    //                                         ->where(function ($query) {
    //                                             $query->where(function ($q) {
    //                                                 $q->whereNotNull('start_date_time')
    //                                                   ->whereNotNull('end_date_time')
    //                                                   ->whereDate('start_date_time', '<=', date('Y-m-d'))
    //                                                   ->whereDate('end_date_time', '>=', date('Y-m-d'));
    //                                             })->orWhere(function ($q) {
    //                                                 $q->whereNull('start_date_time')
    //                                                   ->orWhereNull('end_date_time');
    //                                             });
    //                                         })
    //                                         ->where('is_customer_specific', 0)
    //                                         ->where('is_welcome', 1)
    //                                         ->where('is_redeemable', 1)
    //                                         ->where('voucher_type', 1)
    //                                         ->get();

    //     // $customer_welcome_vouchers = Voucher::where('status', 'active')
    //     //                                         // ->whereDate('start_date_time', '<=', date('Y-m-d'))
    //     //                                         // ->whereDate('end_date_time', '>=', date('Y-m-d'))
    //     //                                         ->where('is_customer_specific', 1)
    //     //                                         ->where('is_welcome', 1)
    //     //                                         ->where('is_redeemable', 2)
    //     //                                         ->where('voucher_type', 1)
    //     //                                         ->whereHas('voucher_user', function ($query) use ($user_id) {
    //     //                                             $query->where('user_id', $user_id);
    //     //                                         })
    //     //                                         ->get();

    //     // $welcome_vouchers = Voucher::where('status', 'active')
    //     //                             // ->whereDate('start_date_time', '<=', date('Y-m-d'))
    //     //                             // ->whereDate('end_date_time', '>=', date('Y-m-d'))
    //     //                             ->where('is_customer_specific', 0)
    //     //                             ->where('is_welcome', 1)
    //     //                             ->where('is_redeemable', 2)
    //     //                             ->where('voucher_type', 1)
    //     //                             ->get();

    //     $all_welcome_vouchers = $customer_redeem_welcome_vouchers
    //                             ->merge($redeem_welcome_vouchers);
    //                             // ->merge($customer_welcome_vouchers)
    //                             // ->merge($welcome_vouchers);

    //     $filtered_vouchers = collect();

    //     if (!$all_welcome_vouchers->isEmpty())
    //     {
    //         foreach($all_welcome_vouchers as $item)
    //         {
    //             // check voucher uses limit active or not start

    //             // if($item->uses_limit == null || $item->uses_limit == "")
    //             // {
    //             //     $item->redeemed_status = 'active';
    //             // }
    //             // else if($item->uses_limit == 0)
    //             // {
    //             //     $item->redeemed_status = 'deactive';
    //             // }
    //             // else
    //             // {
    //             //     if(($item->used_time != $item->uses_limit))
    //             //     {
    //             //         $item->redeemed_status = 'active';
    //             //     }
    //             //     else
    //             //     {
    //             //         $item->redeemed_status = 'deactive';
    //             //     }
    //             // }

    //             // check voucher redeem or not

    //             if(count(VoucherRedeem::where('user_id', $user_id)->where('voucher_id', $item->id)->get()) == $item->max_order_per_customer)
    //             {
    //                 $item->isRedeemed = true;

    //                 // check voucher used or not

    //                 if(VoucherUsage::where('user_id', $user_id)->where('voucher_id', $item->id)->exists())
    //                 {
    //                     $item->isUsed = true;
    //                 }
    //                 else
    //                 {
    //                     $item->isUsed = false;
    //                 }
    //             }
    //             else
    //             {
    //                 $item->isRedeemed = false;
    //                 $item->isUsed = false;
    //             }

    //             // check age

    //             if($item->max_age >= $user_age && $item->min_age <= $user_age)
    //             {
    //                 $item->isAgeEligible = true;
    //             }
    //             else
    //             {
    //                 $item->isAgeEligible = false;
    //             }

    //             // check gender

    //             if(DB::table('voucher_gender')->where('voucher_id', $item->id)->where('gender', $user_gender)->exists())
    //             {
    //                 $item->isGenderEligible = true;
    //             }
    //             else
    //             {
    //                 $item->isGenderEligible = false;
    //             }    
                
    //             if(!empty($item->end_date_time))
    //             {
    //                 $item->end_date_format = date('d M Y', strtotime($item->end_date_time));
    //             }
    //             else
    //             {
    //                 $item->end_date_format = "";
    //             }
                
    //             // Apply filter condition
    //             // if ($item->isRedeemed === false && $item->isUsed === false && $item->isAgeEligible === true && $item->isGenderEligible === true) 
    //             // {
    //             //     $filtered_vouchers->push($item);
    //             // }

    //             // Apply filter condition
    //             if ($item->isRedeemed === false && $item->isUsed === false) 
    //             {
    //                 $filtered_vouchers->push($item);
    //             }
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Welcome Vouchers List',
    //             'data' => $filtered_vouchers
    //         ]);
    //     } 
    //     else 
    //     {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data not found',
    //             'data' => [],
    //         ]);
    //     }
    // }

    public function welcome_vouchers(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        // voucher start

        $customer_redeem_welcome_vouchers = Voucher::where('status', 'active')
                                                // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                                // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                                ->where(function ($query) {
                                                    $query->where(function ($q) {
                                                        $q->whereNotNull('start_date_time')
                                                          ->whereNotNull('end_date_time')
                                                          ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                                          ->whereDate('end_date_time', '>=', date('Y-m-d'));
                                                    })->orWhere(function ($q) {
                                                        $q->whereNull('start_date_time')
                                                          ->orWhereNull('end_date_time');
                                                    });
                                                })
                                                ->where('is_customer_specific', 1)
                                                ->where('is_welcome', 1)
                                                ->where('is_redeemable', 1)
                                                ->where('voucher_type', 1)
                                                ->whereHas('voucher_user', function ($query) use ($user_id) {
                                                    $query->where('user_id', $user_id);
                                                })
                                                ->get();

        $redeem_welcome_vouchers = Voucher::where('status', 'active')
                                            // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                            ->where(function ($query) {
                                                $query->where(function ($q) {
                                                    $q->whereNotNull('start_date_time')
                                                      ->whereNotNull('end_date_time')
                                                      ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                                      ->whereDate('end_date_time', '>=', date('Y-m-d'));
                                                })->orWhere(function ($q) {
                                                    $q->whereNull('start_date_time')
                                                      ->orWhereNull('end_date_time');
                                                });
                                            })
                                            ->where('is_customer_specific', 0)
                                            ->where('is_welcome', 1)
                                            ->where('is_redeemable', 1)
                                            ->where('voucher_type', 1)
                                            ->get();

        // $customer_welcome_vouchers = Voucher::where('status', 'active')
        //                                         // ->whereDate('start_date_time', '<=', date('Y-m-d'))
        //                                         // ->whereDate('end_date_time', '>=', date('Y-m-d'))
        //                                         ->where('is_customer_specific', 1)
        //                                         ->where('is_welcome', 1)
        //                                         ->where('is_redeemable', 2)
        //                                         ->where('voucher_type', 1)
        //                                         ->whereHas('voucher_user', function ($query) use ($user_id) {
        //                                             $query->where('user_id', $user_id);
        //                                         })
        //                                         ->get();

        // $welcome_vouchers = Voucher::where('status', 'active')
        //                             // ->whereDate('start_date_time', '<=', date('Y-m-d'))
        //                             // ->whereDate('end_date_time', '>=', date('Y-m-d'))
        //                             ->where('is_customer_specific', 0)
        //                             ->where('is_welcome', 1)
        //                             ->where('is_redeemable', 2)
        //                             ->where('voucher_type', 1)
        //                             ->get();

        $all_welcome_vouchers = $customer_redeem_welcome_vouchers
                                ->merge($redeem_welcome_vouchers);
                                // ->merge($customer_welcome_vouchers)
                                // ->merge($welcome_vouchers);

        $filtered_vouchers = collect();
        
        foreach($all_welcome_vouchers as $item)
        {
            // check voucher uses limit active or not start

            // if($item->uses_limit == null || $item->uses_limit == "")
            // {
            //     $item->redeemed_status = 'active';
            // }
            // else if($item->uses_limit == 0)
            // {
            //     $item->redeemed_status = 'deactive';
            // }
            // else
            // {
            //     if(($item->used_time != $item->uses_limit))
            //     {
            //         $item->redeemed_status = 'active';
            //     }
            //     else
            //     {
            //         $item->redeemed_status = 'deactive';
            //     }
            // }

            // check voucher redeem or not

            if(count(VoucherRedeem::where('user_id', $user_id)->where('voucher_id', $item->id)->get()) == $item->max_order_per_customer)
            {
                $item->isRedeemed = true;

                // check voucher used or not

                if(VoucherUsage::where('user_id', $user_id)->where('voucher_id', $item->id)->exists())
                {
                    $item->isUsed = true;
                }
                else
                {
                    $item->isUsed = false;
                }
            }
            else
            {
                $item->isRedeemed = false;
                $item->isUsed = false;
            }

            // check age

            if($item->max_age >= $user_age && $item->min_age <= $user_age)
            {
                $item->isAgeEligible = true;
            }
            else
            {
                $item->isAgeEligible = false;
            }

            // check gender

            if(DB::table('voucher_gender')->where('voucher_id', $item->id)->where('gender', $user_gender)->exists())
            {
                $item->isGenderEligible = true;
            }
            else
            {
                $item->isGenderEligible = false;
            }    
            
            if(!empty($item->end_date_time))
            {
                $item->end_date_format = date('d M Y', strtotime($item->end_date_time));
            }
            else
            {
                $item->end_date_format = "";
            }
            
            $item->loyalty_shop_type = "voucher";

            // Apply filter condition
            // if ($item->isRedeemed === false && $item->isUsed === false && $item->isAgeEligible === true && $item->isGenderEligible === true) 
            // {
            //     $filtered_vouchers->push($item);
            // }

            // Apply filter condition
            if ($item->isRedeemed === false && $item->isUsed === false) 
            {
                $filtered_vouchers->push($item);
            }
        }

        // voucher end

        // loyalty shop product start

        $customer_redeem_welcome_products = LoyaltyShop::where('status', 'active')
                                                // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                                // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                                ->where(function ($query) {
                                                    $query->where(function ($q) {
                                                        $q->whereNotNull('start_date_time')
                                                          ->whereNotNull('end_date_time')
                                                          ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                                          ->whereDate('end_date_time', '>=', date('Y-m-d'));
                                                    })->orWhere(function ($q) {
                                                        $q->whereNull('start_date_time')
                                                          ->orWhereNull('end_date_time');
                                                    });
                                                })
                                                ->where('is_customer_specific', 1)
                                                ->where('is_welcome', 1)
                                                ->where('is_redeemable', 1)
                                                ->whereHas('loyalty_shop_user', function ($query) use ($user_id) {
                                                    $query->where('user_id', $user_id);
                                                })
                                                ->get();

        $redeem_welcome_products = LoyaltyShop::where('status', 'active')
                                            // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                            ->where(function ($query) {
                                                $query->where(function ($q) {
                                                    $q->whereNotNull('start_date_time')
                                                      ->whereNotNull('end_date_time')
                                                      ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                                      ->whereDate('end_date_time', '>=', date('Y-m-d'));
                                                })->orWhere(function ($q) {
                                                    $q->whereNull('start_date_time')
                                                      ->orWhereNull('end_date_time');
                                                });
                                            })
                                            ->where('is_customer_specific', 0)
                                            ->where('is_welcome', 1)
                                            ->where('is_redeemable', 1)
                                            ->get();

        $all_welcome_products = $customer_redeem_welcome_products
                                ->merge($redeem_welcome_products);

        $filtered_products = collect();
        
        foreach($all_welcome_products as $item)
        {
            // if(count(LoyaltyShopRedeem::where('user_id', $user_id)->where('loyalty_shop_id', $item->id)->get()) > 0)
            // {
            //     $item->isRedeemed = true;

            //     // check voucher used or not

            //     if(LoyaltyShopUsage::where('user_id', $user_id)->where('loyalty_shop_id', $item->id)->exists())
            //     {
            //         $item->isUsed = true;
            //     }
            //     else
            //     {
            //         $item->isUsed = false;
            //     }
            // }
            // else
            // {
            //     $item->isRedeemed = false;
            //     $item->isUsed = false;
            // }

            // check age

            if($item->max_age >= $user_age && $item->min_age <= $user_age)
            {
                $item->isAgeEligible = true;
            }
            else
            {
                $item->isAgeEligible = false;
            }

            // check gender

            if(DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $item->id)->where('gender', $user_gender)->exists())
            {
                $item->isGenderEligible = true;
            }
            else
            {
                $item->isGenderEligible = false;
            }    
            
            if(!empty($item->end_date_time))
            {
                $item->end_date_format = date('d M Y', strtotime($item->end_date_time));
            }
            else
            {
                $item->end_date_format = "";
            }
            
            // Apply filter condition
            // if ($item->isRedeemed === false && $item->isUsed === false && $item->isAgeEligible === true && $item->isGenderEligible === true) 
            // {
            //     $filtered_vouchers->push($item);
            // }

            // Apply filter condition
            // if ($item->isRedeemed === false && $item->isUsed === false) 
            // {
            //     $filtered_products->push($item);
            // }

            $filtered_products->push($item);
        }

        // loyalty shop product end

        $welcome_voucher_product = collect()
                                        ->concat($filtered_vouchers)
                                        ->concat($filtered_products)
                                        ->values();

        if(!$welcome_voucher_product->isEmpty())
        {
            return response()->json([
                'status' => true,
                'message' => 'Welcome vouchers and products list',
                'data' => $welcome_voucher_product
            ]);
        }                                
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => []
            ]);
        }
    }

    public function voucher_details(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
      
        $user_loyalty_point = $user->loyalty_points ?? 0;

        $voucher_id = $request->voucher_id;
        $voucher_redeem_id = $request->voucher_redeem_id ?? '';

        $voucher = Voucher::find($voucher_id);

        if ($voucher)
        {
            $voucher->description_filter = strip_tags($voucher->description);

            // end date
            if(!empty($voucher->end_date_time))
            {
                $voucher->end_date_foramt = date('d M Y', strtotime($voucher->end_date_time));
            }
            else
            {
                $voucher->end_date_foramt = "";
            }

            // $voucher->start_date = date('d F Y', strtotime($voucher->start_date_time));
            // $voucher->end_date = date('d F Y', strtotime($voucher->end_date_time));

            // services

            $business_service_id = DB::table('voucher_services')->where('voucher_id', $voucher_id)->pluck('business_service_id')->toArray();
            $services = DB::table('business_services')->whereIn('id', $business_service_id)->select('id', 'name')->get();
            $service_name_arr = DB::table('business_services')->whereIn('id', $business_service_id)->pluck('name')->toArray();
            
            $voucher->services_name = implode(', ', $service_name_arr);
            $voucher->services = $services;

            // outlet

            $outlet_id = DB::table('voucher_outlets')->where('voucher_id', $voucher_id)->pluck('outlet_id')->toArray();
            $outlet = Outlet::whereIn('id', $outlet_id)->select('id', 'outlet_name')->get();
            $outlet_name_arr = Outlet::whereIn('id', $outlet_id)->pluck('outlet_name')->toArray();
            
            $voucher->outlet_name = implode(', ', $outlet_name_arr);
            $voucher->outlet = $outlet;

            // validity start

            $voucher->validUntil = "";
              
            if($voucher->is_redeemable == 1)            
            {               
                if(!empty($voucher_redeem_id))
                {
                    $VoucherRedeem = VoucherRedeem::where('voucher_id', $voucher->id)->where('user_id', $user_id)->where('id', $voucher_redeem_id)->where('usage_status', 0)->first();
                }   
                else
                {
                    $VoucherRedeem = "";
                }    
                
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
                    $VoucherUser = VoucherUser::where('voucher_id', $voucher->id)->where('user_id', $user_id)->first();
                    
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

            if($voucher->is_redeemable == 1)            
            {
                if(VoucherRedeem::where('user_id', $user_id)->where('voucher_id', $voucher_id)->where('id', $voucher_redeem_id)->exists())
                {
                    $voucher->isRedeemed = true;
                }
                else
                {
                    $voucher->isRedeemed = false;
                }
            }
            else
            {
                $voucher->isRedeemed = true;
            }

            if(VoucherUsage::where('user_id', $user_id)->where('voucher_id', $voucher_id)->exists())
            {
                $voucher->isUsed = true;
            }
            else
            {
                $voucher->isUsed = false;
            }

            // display redeem button

            if($user_loyalty_point >= $voucher->loyalty_point && $voucher->is_redeemable == 1)
            {
                $voucher->display_redeem_button = true;
            }
            else
            {
                $voucher->display_redeem_button = false;
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $voucher
            ]);
        }
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'voucher not found',
                'data' => null
            ]);
        }
    }

    public function voucher_details_without_login(Request $request)
    {
        $voucher_id = $request->voucher_id;

        $voucher = Voucher::find($voucher_id);

        if ($voucher)
        {
            $voucher->description_filter = strip_tags($voucher->description);

            if(!empty($voucher->end_date_time))
            {
                $voucher->end_date_foramt = date('d M Y', strtotime($voucher->end_date_time));
            }
            else
            {
                $voucher->end_date_foramt = "";
            }

            // $voucher->start_date = date('d F Y', strtotime($voucher->start_date_time));
            // $voucher->end_date = date('d F Y', strtotime($voucher->end_date_time));

            // services

            $business_service_id = DB::table('voucher_services')->where('voucher_id', $voucher_id)->pluck('business_service_id')->toArray();
            $services = DB::table('business_services')->whereIn('id', $business_service_id)->select('id', 'name')->get();
            $service_name_arr = DB::table('business_services')->whereIn('id', $business_service_id)->pluck('name')->toArray();
            
            $voucher->services_name = implode(', ', $service_name_arr);
            $voucher->services = $services;

            // outlet

            $outlet_id = DB::table('voucher_outlets')->where('voucher_id', $voucher_id)->pluck('outlet_id')->toArray();
            $outlet = Outlet::whereIn('id', $outlet_id)->select('id', 'outlet_name')->get();
            $outlet_name_arr = Outlet::whereIn('id', $outlet_id)->pluck('outlet_name')->toArray();
            
            $voucher->outlet_name = implode(', ', $outlet_name_arr);
            $voucher->outlet = $outlet;

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $voucher
            ]);
        }
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'voucher not found',
                'data' => null
            ]);
        }
    }

    public function past_voucher(Request $request)
    {
        $user_id = Auth::user()->id;

        // returns unique Voucher model rows using whereHas(), regardless of how many related voucher_usage rows match.

        // $past_voucher = Voucher::where('status', 'active')
        //                         ->whereHas('voucher_usage', function ($query) use ($user_id) {
        //                             $query->where('user_id', $user_id);
        //                         })
        //                         ->get();

        $past_voucher = Voucher::join('voucher_usages', 'vouchers.id', '=', 'voucher_usages.voucher_id')
                                ->where('vouchers.status', 'active')
                                ->where('voucher_usages.user_id', $user_id)
                                ->select('vouchers.*')
                                ->get();

        if (!$past_voucher->isEmpty())
        {
            foreach ($past_voucher as $loop_key=>$item) 
            {
                // unique id
                $item->custom_unique_id = $loop_key+1;
            }

            return response()->json([
                'status' => true,
                'message' => 'Past Vouchers List',
                'data' => $past_voucher
            ]);
        }
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => [],
            ]);
        }
    }
}
