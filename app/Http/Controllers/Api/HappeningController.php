<?php

namespace App\Http\Controllers\Api;

use App\Happening;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HappeningController extends Controller
{
    // all_happenings

    public function all_happenings()
    {
        // $user_id = Auth::user()->id;
        // $user = Auth::user();

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        // $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        // $user_age = Carbon::parse($user_dob)->age;
        // $user_gender = $user->gender;

        $happenings = Happening::where('status', 'active')
                                // ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                // ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                ->get();

        if(!$happenings->isEmpty())
        {
            foreach($happenings as $item)
            {
                $item->description = $item->description;
                $item->description_filter = strip_tags($item->description);
                $item->image_url = asset("/user-uploads/happenings/".$item->image);

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

                // if(DB::table('happening_gender')->where('happening_id', $item->id)->where('gender', $user_gender)->exists())
                // {
                //     $item->isGenderEligible = true;
                // }
                // else
                // {
                //     $item->isGenderEligible = false;
                // }
            }

            return response()->json([
                'status' => true,
                'message' => 'Happenings',
                'data' => $happenings
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not Found',
                'data' => $happenings
            ]);
        }
    }

    // happening_details

    public function happening_details(Request $request)
    {
        $happening_id = $request->happening_id;

        $happening = Happening::find($happening_id);

        if ($happening)
        {
            $happening->image_url = asset("/user-uploads/happenings/".$happening->image);

            if(!empty($happening->start_date_time))
            {
                $happening->start_date = date('d M Y', strtotime($happening->start_date_time));
                $happening->start_time = date('h:i A', strtotime($happening->start_date_time));
            }
            if(!empty($happening->end_date_time))
            {
                $happening->end_date = date('d M Y', strtotime($happening->end_date_time));
                $happening->end_time = date('h:i A', strtotime($happening->end_date_time));
            }

            // outlet

            $outlet_id = DB::table('happening_outlets')->where('happening_id', $happening_id)->pluck('outlet_id')->toArray();
            $outlet = Outlet::whereIn('id', $outlet_id)->select('id', 'outlet_name')->get();
            $outlet_name_arr = Outlet::whereIn('id', $outlet_id)->pluck('outlet_name')->toArray();
            
            $happening->outlet_name = implode(', ', $outlet_name_arr);
            $happening->outlet = $outlet;

            return response()->json([
                'status' => true,
                'message' => 'Happening Details',
                'data' => $happening->only('id', 'title', 'image', 'description', 'image_url', 'start_date', 'end_date', 'start_time', 'end_time', 'outlet_name', 'outlet')
            ]);
        }
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Happening not found',
                'data' => $happening
            ]);
        }
    }
}
