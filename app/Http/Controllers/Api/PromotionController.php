<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use App\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    // all_promotions

    public function all_promotions()
    {
        // $user_id = Auth::user()->id;
        // $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        // $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        // $user_age = Carbon::parse($user_dob)->age;
        // $user_gender = $user->gender;

        $promotions = Promotion::where('status', 'active')
                                ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                ->get();

        if(!$promotions->isEmpty())
        {
            foreach($promotions as $item)
            {
                $item->description = $item->description;
                $item->description_filter = strip_tags($item->description);
                // $item->image_url = asset("/user-uploads/promotion/".$item->image);

                // check age

                // if($item->max_age >= $user_age && $item->min_age <= $user_age)
                // {
                //     $item->isAgeEligible = true;
                // }
                // else
                // {
                //     $item->isAgeEligible = false;
                // }

                // check gender

                // if(DB::table('promotion_gender')->where('promotion_id', $item->id)->where('gender', $user_gender)->exists())
                // {
                //     $item->isGenderEligible = true;
                // }
                // else
                // {
                //     $item->isGenderEligible = false;
                // }

                // outlet

                $promotion_outlet_id_arr = DB::table('promotion_outlets')->where('promotion_id', $item->id)->pluck('outlet_id')->toArray();
            
                $promotion_outlet_name_arr = Outlet::whereIn('id', $promotion_outlet_id_arr)->pluck('outlet_name')->toArray();

                $item->applicable_outlet_name = implode(', ', $promotion_outlet_name_arr);

                // days

                if($item->days)
                {
                    $days = json_decode($item->days);

                    if(count($days) == 7)
                    {
                        $item->days_applied_on = "All Days";
                    }
                    else
                    {
                        $item->days_applied_on = implode(', ', $days);
                    }
                }

                // services

                $business_service_id = DB::table('promotion_items')->where('promotion_id', $item->id)->pluck('business_service_id')->toArray();
                $services = DB::table('business_services')->whereIn('id', $business_service_id)->pluck('name')->toArray();

                $item->services = implode(', ', $services);
            }

            return response()->json([
                'status' => true,
                'message' => 'Promotions',
                'data' => $promotions
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not Found',
                'data' => $promotions
            ]);
        }
    }

    // promotion_details

    public function promotion_details(Request $request)
    {
        $promotion_id = $request->promotion_id;

        $promotion = Promotion::find($promotion_id);

        if ($promotion)
        {
            $promotion->description = $promotion->description;
            $promotion->description_filter = strip_tags($promotion->description);

            $promotion->new_start_date = date('d F Y', strtotime($promotion->start_date_time));
            $promotion->new_end_date = date('d F Y', strtotime($promotion->end_date_time));

            // services

            $business_service_id = DB::table('promotion_items')->where('promotion_id', $promotion_id)->pluck('business_service_id')->toArray();
            $services = DB::table('business_services')->whereIn('id', $business_service_id)->pluck('name')->toArray();

            $promotion->services = implode(', ', $services);

            // outlet

            $promotion_outlet_id_arr = DB::table('promotion_outlets')->where('promotion_id', $promotion->id)->pluck('outlet_id')->toArray();
            
            $promotion_outlet_name_arr = Outlet::whereIn('id', $promotion_outlet_id_arr)->pluck('outlet_name')->toArray();

            $promotion->outlet_name = implode(', ', $promotion_outlet_name_arr);

            // days

            if($promotion->days)
            {
                $days = json_decode($promotion->days);

                if(count($days) == 7)
                {
                    $promotion->days_applied_on = "All Days";
                }
                else
                {
                    $promotion->days_applied_on = implode(', ', $days);
                }
            }

            // promotin items

            $promotion->promotion_items =  DB::table('promotion_items')->where('promotion_id', $promotion_id)->get();

            return response()->json([
                'status' => true,
                'message' => 'Promotion Details',
                'data' => $promotion
            ]);
        }
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Promotion not found',
                'data' => $promotion
            ]);
        }
    }
}
