<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoyaltyShop;
use App\LoyaltyShopRedeem;
use App\LoyaltyShopUsage;
use App\LoyaltyShopUser;
use App\Outlet;
use App\User;
use Carbon\Carbon;
use FontLib\Table\Type\fpgm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoyaltyShopProductController extends Controller
{
    public function redeemable_product_list(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $all_LoyaltyShop_products = LoyaltyShop::where('loyalty_shops.status', 'active')
                                        ->where('loyalty_shops.loyalty_shop_type', 'product')
                                        // ->whereDate('loyalty_shops.start_date_time', '<=', date('Y-m-d'))
                                        // ->whereDate('loyalty_shops.end_date_time', '>=', date('Y-m-d'))
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
                                        ->where('loyalty_shops.is_customer_specific', 0)
                                        ->where('loyalty_shops.is_redeemable', 1)
                                        ->Join('products', 'products.id', '=', 'loyalty_shops.product_id')
                                        ->select('loyalty_shops.*')
                                        ->get();

        $loyalty_shop_id_arr = DB::table('loyalty_shop_users')->where('user_id', $user_id)->pluck('loyalty_shop_id')->toArray();

        $customer_LoyaltyShop_products = LoyaltyShop::where('loyalty_shops.status', 'active')
                                        ->where('loyalty_shops.loyalty_shop_type', 'product')
                                        // ->whereDate('loyalty_shops.start_date_time', '<=', date('Y-m-d'))
                                        // ->whereDate('loyalty_shops.end_date_time', '>=', date('Y-m-d'))
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
                                        ->where('loyalty_shops.is_customer_specific', 1)
                                        ->where('loyalty_shops.is_redeemable', 1)
                                        ->whereIn('loyalty_shops.id', $loyalty_shop_id_arr)
                                        ->Join('products', 'products.id', '=', 'loyalty_shops.product_id')
                                        ->select('loyalty_shops.*')
                                        ->get();

        // $LoyaltyShop = $all_LoyaltyShop_products->merge($customer_LoyaltyShop_products);

        $LoyaltyShop = collect()
                        ->concat($all_LoyaltyShop_products)
                        ->concat($customer_LoyaltyShop_products)
                        ->values();

        $filtered_LoyaltyShop = collect();

        if (!$LoyaltyShop->isEmpty())
        {
            foreach ($LoyaltyShop as $loop_key => $item) 
            {                            
                // check loyalty_shop redeem or not

                // if(count(LoyaltyShopRedeem::where('user_id', $user_id)->where('loyalty_shop_id', $item->id)->get()) > 0)
                // {
                //     $item->isRedeemed = true;    
                    
                //     // check loyalty_shop used or not

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

                // end date
                if(!empty($item->end_date_time))
                {
                    $item->end_date_format = date('d M Y', strtotime($item->end_date_time));
                }
                else
                {
                    $item->end_date_format = "";
                }
              
                // unique id
                $item->custom_unique_id = $loop_key+1;
                
                // Apply filter condition
                // if ($item->isRedeemed === false && $item->isUsed === false) 
                // {
                //     $filtered_LoyaltyShop->push($item);
                // }

                $filtered_LoyaltyShop->push($item);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Loyalty Shop redeemable product list',
                'data' => $filtered_LoyaltyShop
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

    public function redeemable_product_list_without_login()
    {   
        $all_LoyaltyShop_products = LoyaltyShop::where('loyalty_shops.status', 'active')
                                        ->where('loyalty_shops.loyalty_shop_type', 'product')
                                        // ->whereDate('loyalty_shops.start_date_time', '<=', date('Y-m-d'))
                                        // ->whereDate('loyalty_shops.end_date_time', '>=', date('Y-m-d'))
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
                                        ->where('loyalty_shops.is_customer_specific', 0)
                                        ->where('loyalty_shops.is_redeemable', 1)
                                        ->Join('products', 'products.id', '=', 'loyalty_shops.product_id')
                                        ->select('loyalty_shops.*')
                                        ->get();

        if (!$all_LoyaltyShop_products->isEmpty())
        {
            foreach ($all_LoyaltyShop_products as $loop_key => $item) 
            {                                           
                // end date
                if(!empty($item->end_date_time))
                {
                    $item->end_date_format = date('d M Y', strtotime($item->end_date_time));
                }
                else
                {
                    $item->end_date_format = "";
                }
              
                // unique id
                $item->custom_unique_id = $loop_key+1;                             
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Loyalty Shop redeemable product list',
                'data' => $all_LoyaltyShop_products
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

    public function product_details(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
      
        $user_loyalty_point = $user->loyalty_points ?? 0;

        $loyalty_shop_id = $request->loyalty_shop_id;
        $loyalty_shop_redeem_id = $request->loyalty_shop_redeem_id ?? '';

        $loyalty_shop = LoyaltyShop::where('id', $loyalty_shop_id)->where('loyalty_shop_type', 'product')->first();

        if ($loyalty_shop)
        {
            $loyalty_shop->description_filter = strip_tags($loyalty_shop->description);

            // end date
            if(!empty($loyalty_shop->end_date_time))
            {
                $loyalty_shop->end_date_foramt = date('d M Y', strtotime($loyalty_shop->end_date_time));     
            }
            else
            {
                $loyalty_shop->end_date_foramt = "";
            }

            // outlet

            $outlet_id = DB::table('loyalty_shop_outlets')->where('loyalty_shop_id', $loyalty_shop_id)->pluck('outlet_id')->toArray();
            $outlet = Outlet::whereIn('id', $outlet_id)->select('id', 'outlet_name')->get();
            $outlet_name_arr = Outlet::whereIn('id', $outlet_id)->pluck('outlet_name')->toArray();
            
            $loyalty_shop->outlet_name = implode(', ', $outlet_name_arr);

            // validity start

            $loyalty_shop->validUntil = "";
              
            if($loyalty_shop->is_redeemable == 1)            
            {               
                if(!empty($loyalty_shop_redeem_id))
                {
                    $loyalty_shop_product_redeem = LoyaltyShopRedeem::where('loyalty_shop_id', $loyalty_shop->id)
                                                                    ->where('user_id', $user_id)
                                                                    ->where('id', $loyalty_shop_redeem_id)
                                                                    ->where('usage_status', 0)
                                                                    ->first();
                }   
                else
                {
                    $loyalty_shop_product_redeem = "";
                }    
                
                if($loyalty_shop_product_redeem)
                {
                    if ($loyalty_shop->validity_type == 'years') {
                    $validUntil = Carbon::parse($loyalty_shop_product_redeem->created_at)->addYears($loyalty_shop->validity);
                    } elseif ($loyalty_shop->validity_type == 'months') {
                        $validUntil = Carbon::parse($loyalty_shop_product_redeem->created_at)->addMonths($loyalty_shop->validity);
                    } else {
                        $validUntil = null;
                    }

                    $loyalty_shop->validUntil = date('d M Y', strtotime($validUntil));           
                }                    
            }
            else
            {
                if($loyalty_shop->is_customer_specific == 1)
                {                         
                    $loyalty_shopUser = LoyaltyShopUser::where('loyalty_shop_id', $loyalty_shop->id)->where('user_id', $user_id)->first();
                    
                    if($loyalty_shopUser)
                    {
                        if ($loyalty_shop->validity_type == 'years') {
                            $validUntil = Carbon::parse($loyalty_shopUser->created_at)->addYears($loyalty_shop->validity);
                        } elseif ($loyalty_shop->validity_type == 'months') {
                            $validUntil = Carbon::parse($loyalty_shopUser->created_at)->addMonths($loyalty_shop->validity);
                        } else {
                            $validUntil = null;
                        }

                        $loyalty_shop->validUntil = date('d M Y', strtotime($validUntil));       
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

                    $loyalty_shop->validUntil = date('d M Y', strtotime($validUntil));                                                                                                   
                }
            }

            // validity end

            // if($loyalty_shop->is_redeemable == 1)            
            // {
            //     if(LoyaltyShopRedeem::where('user_id', $user_id)->where('loyalty_shop_id', $loyalty_shop_id)->where('id', $loyalty_shop_redeem_id)->exists())
            //     {
            //         $loyalty_shop->isRedeemed = true;
            //     }
            //     else
            //     {
            //         $loyalty_shop->isRedeemed = false;
            //     }
            // }
            // else
            // {
            //     $loyalty_shop->isRedeemed = true;
            // }

            // if(LoyaltyShopUsage::where('user_id', $user_id)->where('loyalty_shop_id', $loyalty_shop_id)->exists())
            // {
            //     $loyalty_shop->isUsed = true;
            // }
            // else
            // {
            //     $loyalty_shop->isUsed = false;
            // }

            return response()->json([
                'status' => true,
                'message' => 'Loyalty Shop product details',
                'data' => $loyalty_shop
            ]);
        }
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ]);
        }
    }

    public function product_details_without_login(Request $request)
    {
        $loyalty_shop_id = $request->loyalty_shop_id;

        $loyalty_shop = LoyaltyShop::where('id', $loyalty_shop_id)->where('loyalty_shop_type', 'product')->first();

        if ($loyalty_shop)
        {
            $loyalty_shop->description_filter = strip_tags($loyalty_shop->description);

            // end date
            if(!empty($loyalty_shop->end_date_time))
            {
                $loyalty_shop->end_date_foramt = date('d M Y', strtotime($loyalty_shop->end_date_time));     
            }
            else
            {
                $loyalty_shop->end_date_foramt = "";
            }

            // outlet

            $outlet_id = DB::table('loyalty_shop_outlets')->where('loyalty_shop_id', $loyalty_shop_id)->pluck('outlet_id')->toArray();
            $outlet = Outlet::whereIn('id', $outlet_id)->select('id', 'outlet_name')->get();
            $outlet_name_arr = Outlet::whereIn('id', $outlet_id)->pluck('outlet_name')->toArray();
            
            $loyalty_shop->outlet_name = implode(', ', $outlet_name_arr);

            return response()->json([
                'status' => true,
                'message' => 'Loyalty Shop product details',
                'data' => $loyalty_shop
            ]);
        }
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ]);
        }
    }

    public function redeem_products(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $loyalty_shop_id = $request->loyalty_shop_id;
        $loyalty_shop = LoyaltyShop::find($loyalty_shop_id);


        if($loyalty_shop)
        {      
            if($loyalty_shop->is_redeemable == 1)  
            {                       
                if($loyalty_shop->is_customer_specific == 0)
                {
                    $customer_can_redeem = "yes";
                }
                else
                {
                    if(LoyaltyShopUser::where('user_id', $user_id)->where('loyalty_shop_id', $loyalty_shop_id)->exists())
                    {
                        $customer_can_redeem = "yes";
                    }
                    else
                    {
                        $customer_can_redeem = "no";
                    }
                }

                if((!empty($loyalty_shop->start_date_time) && !empty($loyalty_shop->end_date_time) && strtotime(date('Y-m-d')) >= strtotime($loyalty_shop->start_date_time) && strtotime(date('Y-m-d')) <= strtotime($loyalty_shop->end_date_time)) || (empty($loyalty_shop->start_date_time) || empty($loyalty_shop->end_date_time)))
                {
                    // if($loyalty_shop->max_age >= $user_age && $loyalty_shop->min_age <= $user_age)
                    // {
                    //     if(DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $loyalty_shop_id)->where('gender', $user_gender)->exists())
                    //     {       

                            if($customer_can_redeem == "yes")
                            {
                                // if(count(LoyaltyShopRedeem::where('user_id', $user_id)->where('loyalty_shop_id', $loyalty_shop_id)->get()) == 0)
                                // { 

                                    if($user->loyalty_points >= $loyalty_shop->loyalty_point)
                                    {
                                        $loyalty_shop_redeem = new LoyaltyShopRedeem;

                                        $loyalty_shop_redeem->loyalty_shop_id = $loyalty_shop_id;
                                        $loyalty_shop_redeem->user_id = $user_id;

                                        $result = $loyalty_shop_redeem->save();

                                        if($result)
                                        {                                             
                                            // coin minus from user start

                                            if ($loyalty_shop->loyalty_point > 0)
                                            {
                                                $user->loyalty_points -= $loyalty_shop->loyalty_point;
                                                $user->save();

                                                LoyaltyPointController::coins_used($loyalty_shop->loyalty_point);
                                            }
                            
                                            // coin minus from user end

                                            return response()->json([
                                                'status' => true,
                                                'message' => "Loyalty Shop Product is Redeemed",
                                            ]);
                                        }
                                        else
                                        {
                                            return response()->json([
                                                'status' => false,
                                                'message' => "Loyalty Shop Product is not Redeemed",
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

                                // }
                                // else
                                // {
                                //     return response()->json([
                                //         'status' => false,
                                //         'message' => "You have already redeemed this Loyalty Shop Product",
                                //     ]);
                                // }
                            }
                            else
                            {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'You can not redeem this Loyalty Shop Product'
                                ]);
                            }

                    //     }
                    //     else
                    //     {
                    //         return response()->json([
                    //             'status' => false,
                    //             'message' => 'You are not eligible to use this loyalty shop product'
                    //         ]);
                    //     }   
                    // }
                    // else
                    // {
                    //     return response()->json([
                    //         'status' => false,
                    //         'message' => 'Your age is not eligible to use this loyalty shop product'
                    //     ]);
                    // }   

                }
                else
                {
                    return response()->json([
                        'status' => false,
                        'message' => "Loyalty Shop Product expired or not started"
                    ]);
                }                                              
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => "Loyalty Shop Product is not redeemable",
                ]);
            }
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => "Loyalty Shop Product not found",
            ]);
        }
    }

    public function after_redeem_products(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $redeemed_loyalty_shops = LoyaltyShopRedeem::join('loyalty_shops', 'loyalty_shop_redeems.loyalty_shop_id', '=', 'loyalty_shops.id')
                                            ->where('loyalty_shop_redeems.user_id', $user_id)
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
                                            ->whereNotIn('id', function ($query) use ($user_id) {
                                                $query->select('loyalty_shop_id')
                                                        ->from('loyalty_shop_usages')
                                                        ->where('user_id', $user_id);
                                            })
                                            ->get();

        $without_redeem_customer_loyalty_shops = LoyaltyShop::where('status', 'active')
                                            // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                            // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                            ->where('is_redeemable', 2)
                                            ->where('is_customer_specific', 1)
                                            ->whereHas('loyalty_shop_user', function ($query) use ($user_id) {
                                                $query->where('user_id', $user_id);
                                            })
                                            ->whereNotIn('id', function ($query) use ($user_id) {
                                                $query->select('loyalty_shop_id')
                                                        ->from('loyalty_shop_usages')
                                                        ->where('user_id', $user_id);
                                            })                                  
                                            ->get();

        $available_loyalty_shop_product = collect()
                                        ->concat($redeemed_loyalty_shops)
                                        ->concat($without_redeem_loyalty_shops)
                                        ->concat($without_redeem_customer_loyalty_shops)
                                        ->values();

        $filtered_available_loyalty_shop_product = collect();

        if (!$available_loyalty_shop_product->isEmpty()) 
        {
            foreach($available_loyalty_shop_product as $item)
            {
                // loyalty shop redeem id
                if(!isset($item->loyalty_shop_redeem_id))
                {
                    $item->loyalty_shop_redeem_id = "";
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

                if(DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $item->id)->where('gender', $user_gender)->exists())
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

                // validity start

                $item->validUntil = "";
                
                if($item->is_redeemable == 1)            
                {                      
                    $LoyaltyShopRedeem = LoyaltyShopRedeem::where('loyalty_shop_id', $item->id)->where('user_id', $user_id)->where('id', $item->loyalty_shop_redeem_id)->where('usage_status', 0)->first();
                    
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
                        $LoyaltyShopUser = LoyaltyShopUser::where('loyalty_shop_id', $item->id)->where('user_id', $user_id)->first();
                        
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

                if (is_null($item->image)) 
                {
                    $item->loyalty_shop_image_url = asset('img/no-image.jpg');
                }
                else
                {
                    $item->loyalty_shop_image_url = asset('/user-uploads/loyalty-shop/' . $item->image);
                }
                
                // Apply filter condition           
                // if ($item->isAgeEligible === true && $item->isGenderEligible === true) 
                // {
                //     $filtered_available_loyalty_shop_product->push($item);
                // }   

                // Apply filter condition
                $filtered_available_loyalty_shop_product->push($item);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'My Loyalty Shop Product List',
                'data' => $filtered_available_loyalty_shop_product
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

    public function past_product(Request $request)
    {
        $user_id = Auth::user()->id;

        $used_loyalty_shop_product = LoyaltyShopUsage::join('loyalty_shops', 'loyalty_shop_usages.loyalty_shop_id', '=', 'loyalty_shops.id')
                ->where('loyalty_shops.status', 'active')
                ->where('loyalty_shop_usages.user_id', $user_id)
                ->select('loyalty_shops.*', 'loyalty_shop_usages.id as loyalty_shop_usage_id', 'loyalty_shop_usages.created_at as used_on_date')
                ->get();

        if (!$used_loyalty_shop_product->isEmpty())
        {
            foreach ($used_loyalty_shop_product as $loop_key=>$item) 
            {
                // unique id
                $item->custom_unique_id = $loop_key+1;

                $item->used_on_date_format = Carbon::parse($item->used_on_date)->format('j F Y');

                if (is_null($item->image)) 
                {
                    $item->loyalty_shop_image_url = asset('img/no-image.jpg');
                }
                else
                {
                    $item->loyalty_shop_image_url = asset('/user-uploads/loyalty-shop/' . $item->image);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Past Loyalty Shop Product List',
                'data' => $used_loyalty_shop_product
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

    public function welcome_product(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

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

        if (!$all_welcome_products->isEmpty())
        {
            foreach($all_welcome_products as $item)
            {
                // if(count(LoyaltyShopRedeem::where('user_id', $user_id)->where('loyalty_shop_id', $item->id)->get()) > 0)
                // {
                //     $item->isRedeemed = true;

                //     // check used or not

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
                //     $filtered_products->push($item);
                // }

                // Apply filter condition
                // if ($item->isRedeemed === false && $item->isUsed === false) 
                // {
                //     $filtered_products->push($item);
                // }

                $filtered_products->push($item);
            }

            return response()->json([
                'status' => true,
                'message' => 'Welcome Product List',
                'data' => $filtered_products
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
