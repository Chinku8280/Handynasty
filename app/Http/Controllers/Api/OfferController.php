<?php

namespace App\Http\Controllers\Api;

use App\offer;
use App\offerItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class OfferController extends Controller
{
    public function getAllOffers(Request $request)
    {
        $user = Auth::user();

        $offers = offer::where('status', 'active')            
            ->get();

        if (!$offers->isEmpty()) 
        {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $offers
            ]);
        } 
        else 
        {
            return response()->json([
                'status' => false,
                'message' => 'Offers Data not found',
                'data' => $offers
            ]);
        }
    }
}
