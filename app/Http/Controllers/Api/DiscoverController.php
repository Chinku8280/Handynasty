<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Coupon;
use App\CouponUser;
use App\Discover;
use App\Happening;
use App\Promotion;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Controller;

class DiscoverController extends Controller
{
    public function all_promotions()
    {
        $promotions = Promotion::where('status', 'active')->get();

        if (!$promotions->isEmpty()) {
            foreach ($promotions as $promotion) {
                $promotion->description = strip_tags($promotion->description);
                $promotion->image = $this->getPromotionImageUrl($promotion->image);
                $promotion->end_date_time = date('d M Y', strtotime($promotion->end_date_time));
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $promotions
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $promotions
            ]);
        }
    }

    public function all_happenings()
    {
        $happenings = Discover::where('status', 'active')->get();

        if (!$happenings->isEmpty()) {
            foreach ($happenings as $happening) {
                $happening->description = strip_tags($happening->description);
                $happening->image = $this->getImageUrl($happening->image);
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $happenings
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $happenings
            ]);
        }
    }

    private function getImageUrl($imageName)
    {
        $baseUrl = env('APP_URL');

        return $baseUrl . '/user-uploads/happenings/' . $imageName;
    }

    private function getPromotionImageUrl($imageName)
    {
        $baseUrl = env('APP_URL');

        return $baseUrl . '/user-uploads/promotion/' . $imageName;
    }
}
