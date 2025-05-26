<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PrivacyPolicy;
use App\ServiceBanner;
use App\TermsAndCondition;
use App\WhoWeArePage;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    // get_privacy_policy

    public function get_privacy_policy()
    {
        $result = PrivacyPolicy::latest()->first();
    
        if (!$result) 
        {
            return response()->json([
                'status' => false, 
                'message' => 'Privacy Policy not found'
            ]);
        }
        else
        {
            $privacy_policy = strip_tags($result->privacy_policy);
    
            return response()->json([
                'status' => true,
                'message' => 'Privacy Policy',
                'data' => $privacy_policy
            ]);
        }
    }

    // who_we_are

    public function who_we_are()
    {
        $result = WhoWeArePage::latest()->first();
    
        if (!$result) 
        {
            return response()->json([
                'status' => false, 
                'message' => 'Data not found'
            ]);
        }
        else
        {
            $result->image_url = asset('/user-uploads/who-we-are/' . $result->image);
            $result->description_filter = strip_tags($result->description);

            return response()->json([
                'status' => true,
                'message' => 'Who are we',
                'data' => $result
            ]);
        }
    }

    // get_terms_and_condition

    public function get_terms_and_condition()
    {
        $result = TermsAndCondition::latest()->first();
    
        if (!$result) 
        {
            return response()->json([
                'status' => false, 
                'message' => 'Terms And Condition not found'
            ]);
        }
        else
        {
            $terms_condition = $result->terms_condition;
    
            return response()->json([
                'status' => true,
                'message' => 'Terms And Condition',
                'data' => $terms_condition
            ]);
        }
    } 

    public function getServiceBanner_1()
    {
        $bannerImage = ServiceBanner::where('banner_type', 1)->select('image as img')->get();

        if (!$bannerImage) 
        {
            return response()->json([
                'status' => false, 
                'message' => 'Data not found'
            ]);
        }
        else
        {
            $image_arr = [];

            foreach ($bannerImage as $item)
            {
                $item->img = asset("user-uploads/service-banner-1/".$item->img);

                $image_arr[] = $item->img;
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Service Banner',
                'data' => $bannerImage
            ]);
        }
    }

    public function getServiceBanner_2()
    {
        $bannerImage = ServiceBanner::where('banner_type', 2)->select('image as img')->get();

        if (!$bannerImage) 
        {
            return response()->json([
                'status' => false, 
                'message' => 'Data not found'
            ]);
        }
        else
        {
            $image_arr = [];

            foreach ($bannerImage as $item)
            {
                $item->img = asset("user-uploads/service-banner-2/".$item->img);

                $image_arr[] = $item->img;
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Service Banner',
                'data' => $bannerImage
            ]);
        }
    }
    
    private function getImageUrl($imageName)
    {
        $baseUrl = env('APP_URL');
        
        $imageName = ltrim($imageName, '/');
        
        return $baseUrl . '/' . $imageName;
    }

    public function get_ServiceBanner_settings()
    {
        $service_banner_settings = DB::table('service_banner_settings')->first();

        if($service_banner_settings)
        {
            if($service_banner_settings->service_banner_two_status == 1)
            {
                $service_banner_two_status = true;
            }
            else
            {
                $service_banner_two_status = false;
            }
        }
        else
        {
            $service_banner_two_status = false;
        }

        $data = [
            'service_banner_two_status' => $service_banner_two_status,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Service Banner Settings',
            'data' => $data
        ]);
    }   
}
