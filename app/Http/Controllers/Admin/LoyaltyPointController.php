<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoyaltyPoint;
use App\User;

class LoyaltyPointController extends Controller
{
    // add coins

    public static function coins_add($total_loyalty_coins, $customer_id)
    {
        $user = User::find($customer_id);

        if($user)
        {
            $user->increment('loyalty_points', $total_loyalty_coins);
            $user->save();

            $loyaltyPoint = new LoyaltyPoint();
            $loyaltyPoint->user_id = $customer_id;
            $loyaltyPoint->loyalty_points = $total_loyalty_coins;
            $loyaltyPoint->points_type = 'plus';
            $loyaltyPoint->description = "Coins added by Admin";
            $loyaltyPoint->available_coins = $total_loyalty_coins;
            $loyaltyPoint->save();
        }
    }
}
