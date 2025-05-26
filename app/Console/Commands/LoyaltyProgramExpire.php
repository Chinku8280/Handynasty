<?php

namespace App\Console\Commands;

use App\LoyaltyProgramHistoryDetail;
use App\LoyaltyProgramHour;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LoyaltyProgramExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyaltyProgramExp:LoyaltyProgramExpire';

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
        $loyalty_program_settings = DB::table('loyalty_program_settings')->first();

        if($loyalty_program_settings)
        {
            $expired_days = $loyalty_program_settings->expired_days ?? '';

            if($expired_days && $expired_days > 0)
            {
                $expirationDate = now()->subDays($expired_days)->format('Y-m-d');

                $LoyaltyProgramHour = LoyaltyProgramHour::where('status', 1)
                                                        ->whereDate('created_at', '<=', $expirationDate)
                                                        ->get();

                if(!$LoyaltyProgramHour->isEmpty())
                {
                    foreach($LoyaltyProgramHour as $item)
                    {
                        LoyaltyProgramHistoryDetail::where('status', 1)
                                                    ->where('user_id', $item->user_id)
                                                    ->where('category_id', $item->category_id)
                                                    ->where('round_no', $item->round_no)
                                                    ->update(['status' => 4, 'updated_at' => Carbon::now()]);
                    }

                    LoyaltyProgramHour::where('status', 1)
                                        ->whereDate('created_at', '<=', $expirationDate)
                                        ->update(['status' => 4, 'updated_at' => Carbon::now()]);                  
                }
            }
        }
    }
}
