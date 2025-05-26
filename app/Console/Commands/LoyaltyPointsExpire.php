<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LoyaltyPointsExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyaltyPointExp:LoyaltyPointsExpire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // coin expired start

        $loyalty_point_settings = DB::table('loyalty_point_settings')->first();

        if ($loyalty_point_settings) 
        {
            $expiredDays = $loyalty_point_settings->loyalty_points_expired_days;

            if($expiredDays && $expiredDays > 0)
            {
                $expirationDate = now()->subDays($expiredDays)->format('Y-m-d');

                $expiredPoints = DB::table('loyalty_points')
                                        ->where('points_type', 'plus')
                                        ->whereDate('created_at', '<=', $expirationDate)
                                        ->where('deducted_at', '=','0')
                                        ->where('available_coins', '!=', 0)
                                        ->orderBy('created_at', 'asc')
                                        ->get();

                foreach($expiredPoints as $item)
                {
                    $expiredPointsTotal = $item->available_coins;

                    DB::table('loyalty_points')
                                ->where('id', $item->id)
                                ->update([
                                    'available_coins' => 0,
                                    'deducted_at' => '1',
                                    'updated_at' => now(),
                                ]);

                    DB::table('loyalty_points')->insert([
                        'user_id' => $item->user_id,
                        'loyalty_points' => $item->available_coins,
                        'points_type' => 'minus',
                        'description' => 'coins expired',
                        'deducted_at' => '1',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $user_data = User::find($item->user_id);
                    
                    if($user_data->loyalty_points >= $expiredPointsTotal)
                    {
                        $user_data->loyalty_points -= $expiredPointsTotal;
                        $user_data->save();
                    }
                    else
                    {
                        $user_data->loyalty_points = 0;
                        $user_data->save();
                    }
                }
            }
        }

        // coin expired end
    }
}
