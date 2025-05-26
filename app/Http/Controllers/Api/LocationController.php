<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Location;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function store_locations(Request $request)
    {
        $user_id = Auth::user()->id;

        $stores = Location::select('id', 'name', 'email', 'url', 'postalCode', 'address', 'mobile', 'openingTime', 
                        'closingTime', 'image')
                        ->get();

        if(!$stores->isEmpty())
        {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $stores
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $stores
            ]);
        }
    }

    public function store_location_get_direction(Request $request)
    {
        // return $request->all();

        $store_id = $request->store_id;

        $store = Location::find($store_id);

        if($store)
        {
            $get_data = $this->get_latlong_api($store->postalCode);

            $store->latitude = $get_data->results[0]->LATITUDE;
            $store->longitude = $get_data->results[0]->LONGITUDE;

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $store->only('id', 'name', 'postalCode', 'address', 'latitude', 'longitude')
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $store
            ]);
        }
    }

    public function get_latlong_api($postalcode)
    {
        $url = "https://www.onemap.gov.sg/api/common/elastic/search?searchVal=$postalcode&returnGeom=Y&getAddrDetails=Y&pageNum=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
        $result = curl_exec($ch);  
        curl_close($ch);

        return json_decode($result);
    }
}
