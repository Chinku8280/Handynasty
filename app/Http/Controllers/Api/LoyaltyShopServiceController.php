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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoyaltyShopServiceController extends Controller
{
    public function redeemable_service_list(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $all_LoyaltyShop_services = LoyaltyShop::where('loyalty_shops.status', 'active')
                                        ->where('loyalty_shops.loyalty_shop_type', 'service')
                                        ->whereDate('loyalty_shops.start_date_time', '<=', date('Y-m-d'))
                                        ->whereDate('loyalty_shops.end_date_time', '>=', date('Y-m-d'))
                                        ->where('loyalty_shops.is_customer_specific', 0)
                                        ->where('loyalty_shops.is_redeemable', 1)
                                        ->Join('business_services', 'business_services.id', '=', 'loyalty_shops.service_id')
                                        ->select('loyalty_shops.*')
                                        ->get();

        $loyalty_shop_id_arr = DB::table('loyalty_shop_users')->where('user_id', $user_id)->pluck('loyalty_shop_id')->toArray();

        $customer_LoyaltyShop_services = LoyaltyShop::where('loyalty_shops.status', 'active')
                                        ->where('loyalty_shops.loyalty_shop_type', 'service')
                                        ->whereDate('loyalty_shops.start_date_time', '<=', date('Y-m-d'))
                                        ->whereDate('loyalty_shops.end_date_time', '>=', date('Y-m-d'))
                                        ->where('loyalty_shops.is_customer_specific', 1)
                                        ->where('loyalty_shops.is_redeemable', 1)
                                        ->whereIn('loyalty_shops.id', $loyalty_shop_id_arr)
                                        ->Join('business_services', 'business_services.id', '=', 'loyalty_shops.service_id')
                                        ->select('loyalty_shops.*')
                                        ->get();

        // $LoyaltyShop = $all_LoyaltyShop_services->merge($customer_LoyaltyShop_services);

        $LoyaltyShop = collect()
                        ->concat($all_LoyaltyShop_services)
                        ->concat($customer_LoyaltyShop_services)
                        ->values();

        $filtered_LoyaltyShop = collect();

        if (!$LoyaltyShop->isEmpty())
        {
            foreach ($LoyaltyShop as $loop_key => $item) 
            {                            
                // check loyalty_shop redeem or not

                if(count(LoyaltyShopRedeem::where('user_id', $user_id)->where('loyalty_shop_id', $item->id)->get()) > 0)
                {
                    $item->isRedeemed = true;    
                    
                    // check loyalty_shop used or not

                    if(LoyaltyShopUsage::where('user_id', $user_id)->where('loyalty_shop_id', $item->id)->exists())
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

                if(DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $item->id)->where('gender', $user_gender)->exists())
                {
                    $item->isGenderEligible = true;
                }
                else
                {
                    $item->isGenderEligible = false;
                }

                // end date
                $item->end_date_format = date('d M Y', strtotime($item->end_date_time));
              
                // unique id
                $item->custom_unique_id = $loop_key+1;
                
                // Apply filter condition
                if ($item->isRedeemed === false && $item->isUsed === false) 
                {
                    $filtered_LoyaltyShop->push($item);
                }
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Loyalty Shop redeemable service list',
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

    public function service_details(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
      
        $user_loyalty_point = $user->loyalty_points ?? 0;

        $loyalty_shop_id = $request->loyalty_shop_id;
        $loyalty_shop_redeem_id = $request->loyalty_shop_redeem_id ?? '';

        $loyalty_shop = LoyaltyShop::where('id', $loyalty_shop_id)->where('loyalty_shop_type', 'service')->first();

        if ($loyalty_shop)
        {
            $loyalty_shop->description_filter = strip_tags($loyalty_shop->description);

            $loyalty_shop->end_date_foramt = date('d M Y', strtotime($loyalty_shop->end_date_time));      

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

            if($loyalty_shop->is_redeemable == 1)            
            {
                if(LoyaltyShopRedeem::where('user_id', $user_id)->where('loyalty_shop_id', $loyalty_shop_id)->where('id', $loyalty_shop_redeem_id)->exists())
                {
                    $loyalty_shop->isRedeemed = true;
                }
                else
                {
                    $loyalty_shop->isRedeemed = false;
                }
            }
            else
            {
                $loyalty_shop->isRedeemed = true;
            }

            if(LoyaltyShopUsage::where('user_id', $user_id)->where('loyalty_shop_id', $loyalty_shop_id)->exists())
            {
                $loyalty_shop->isUsed = true;
            }
            else
            {
                $loyalty_shop->isUsed = false;
            }

            return response()->json([
                'status' => true,
                'message' => 'Loyalty Shop service details',
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
}
