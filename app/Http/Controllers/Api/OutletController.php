<?php

namespace App\Http\Controllers\Api;

use App\BusinessService;
use Illuminate\Http\Request;
use App\Outlet;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class OutletController extends Controller
{
    public function get_outlets()
    {
        $outlets = Outlet::where('status', 'active')->get();

        if (!$outlets->isEmpty()) {
            foreach ($outlets as $outlet) {
                $outlet->outlet_image = $this->getImageUrl($outlet->image);

                $outlet->outlet_description_filter = strip_tags($outlet->outlet_description);
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $outlets
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $outlets
            ]);
        }
    }

    public function outlets_get_directions(Request $request)
    {
        $outlet_id = $request->outlet_id;

        $outlet = Outlet::find($outlet_id);

        if($outlet)
        {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $outlet->only('id', 'outlet_name', 'address', 'latitude', 'longitude')
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $outlet
            ]);
        }
    }

    public function outlet_details($outlet_id)
    {
        $outlet = Outlet::find($outlet_id);
    
        if ($outlet) 
        {
            $outlet->outlet_image = $this->getImageUrl($outlet->image);
            $outlet->outlet_description_filter = strip_tags($outlet->outlet_description);     

            $business_services_outlets = DB::table('business_services_outlets')->where('outlet_id', $outlet_id)->pluck('business_service_id')->toArray();

            $outlet->services = DB::table('business_services')
                                    ->whereIn('id', $business_services_outlets)
                                    ->where('status', 'active')
                                    ->select('id', 'name')
                                    ->get();
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $outlet                
            ]);
        } 
        else {
            return response()->json([
                'status' => false,
                'message' => 'Outlet not found',
                'data' => null
            ]);
        }
    }
       
    public static function getImageUrl($imageName)
    {
        return asset('/user-uploads/outlet_images/' . $imageName);
    }

    // services

    public function outlet_services(Request $request)
    {
        $outlet_id = $request->outlet_id;

        $business_services_outlets = DB::table('business_services_outlets')->where('outlet_id', $outlet_id)->pluck('business_service_id')->toArray();

        $services = DB::table('business_services')
                        ->whereIn('id', $business_services_outlets)
                        ->where('status', 'active')
                        ->select('id', 'name')
                        ->get();

        if(!$services->isEmpty())
        {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $services
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $services
            ]);
        }
    }
}
