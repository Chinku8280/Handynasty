<?php

namespace App\Http\Controllers\Api;

use App\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\LoyaltyPoint;
use App\User;
use App\VoucherRedeem;
use App\VoucherUsage;
use App\VoucherUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoyaltyPointController extends Controller
{
    // coin used for add order (coin minus)

    public static function coins_used($total_loyalty_coins)
    {
        $result = DB::table('loyalty_points')
                    ->where('user_id', Auth::user()->id)
                    ->where('points_type', 'plus')
                    ->where('available_coins', '!=', 0)
                    ->orderBy('created_at', 'asc')
                    ->get();

        $temp_coins = $total_loyalty_coins;

        foreach($result as $item)
        {
            $db_available_coins = $item->available_coins;
            $db_used_coins = $item->used_coins;
            $used_coins = 0;
            
            if($db_available_coins >= $temp_coins)
            {
                $used_coins = $temp_coins;
                $db_available_coins -= $used_coins;               
                $temp_coins -= $used_coins;

                $db_used_coins += $used_coins;

                DB::table('loyalty_points')
                    ->where('id', $item->id)
                    ->update(['available_coins' => $db_available_coins, 'used_coins' => $db_used_coins]);

                break;
            }
            else
            {
                $used_coins = $db_available_coins;
                $db_available_coins -= $used_coins;
                $temp_coins -= $used_coins;

                $db_used_coins += $used_coins;

                DB::table('loyalty_points')
                    ->where('id', $item->id)
                    ->update(['available_coins' => $db_available_coins, 'used_coins' => $used_coins]);
            }
        }

        DB::table('loyalty_points')->insert([
            'user_id' => Auth::user()->id,
            'loyalty_points' => $total_loyalty_coins,
            'points_type' => 'minus',
            'description' => "coins used for voucher redeemed",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    // loyalty_point

    public function loyalty_point()
    {
        $user_id = Auth::user()->id;

        $user = User::find($user_id);
      
        $loyalty_point = $user->loyalty_points ?? 0;

        $data['loyalty_point'] = $loyalty_point;
        
        return response()->json([
            'status' => true,
            'message' => 'Loyalty Coin',
            'data' => $data
        ]);
    }
}
