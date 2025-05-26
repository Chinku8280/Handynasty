<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoyaltyProgramHistory;
use App\LoyaltyProgramHistoryDetail;
use App\LoyaltyProgramHour;
use App\Outlet;
use App\User;
use App\Voucher;
use App\VoucherRedeem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoyaltyProgramController extends Controller
{
    // store

    public function store(Request $request)
    {
        // return $request->all();

        $validator = Validator::make(
            $request->all(),
            [
                'date' => 'required|date',
                'time' => 'required',
                'outlet_id' => 'required',
                'services_id' => 'required',
                'category_id' => 'required'
            ],
            [],
            [
                'date' => 'Date',
                'time' => 'Time',
                'outlet_id' => 'Outlet',
                'services_id' => 'Services',
                'category_id' => 'Categories'
            ]
        );

        if($validator->fails())
        {
            $error = $validator->errors();

            return response()->json(['status' => "error", 'error'=>$error]);
        }
        else
        {
            $date = $request->date;
            $time = $request->time;
            $outlet_id = $request->outlet_id;
            $customer_id = $request->customer_id;
            $services_id = $request->services_id ?? [];
            $category_id = $request->category_id ?? [];

            // calculate total service hours start

            $totalHours = $this->calculate_total_services_hours($services_id);

            // calculate total service hours end

            // loyalty program hours start

            $LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $customer_id)->first();
            $old_round_no = $LoyaltyProgramHour->round_no ?? 1;

            if($LoyaltyProgramHour)
            {
                $all_total_hours = $LoyaltyProgramHour->total_hours + $totalHours;               
            }
            else
            {
                $all_total_hours = $totalHours;

                $LoyaltyProgramHour = new LoyaltyProgramHour();              
            }

            if($all_total_hours >= 20)
            {
                $new_hours = $all_total_hours - 20;
                $old_hours = $totalHours - $new_hours;

                $LoyaltyProgramHour->user_id = $customer_id;
                $LoyaltyProgramHour->total_hours = $all_total_hours - 20;
                // $LoyaltyProgramHour->free_hours += 2;
                $LoyaltyProgramHour->total_round += 1;
                $LoyaltyProgramHour->round_no += 1;
                $LoyaltyProgramHour->save();
                
                $this->create_free_reward_voucher(1, $customer_id);
                $this->create_free_reward_voucher(1, $customer_id);
            }
            else if($all_total_hours >= 10)
            {
                $new_hours = $totalHours;
                $old_hours = $totalHours - $new_hours;

                $LoyaltyProgramHour->user_id = $customer_id;
                $LoyaltyProgramHour->total_hours = $all_total_hours;
                // $LoyaltyProgramHour->free_hours += 1;
                $LoyaltyProgramHour->save();

                $this->create_free_reward_voucher(1, $customer_id);
            }
            else
            {
                $new_hours = $totalHours;
                $old_hours = $totalHours - $new_hours;

                $LoyaltyProgramHour->user_id = $customer_id;
                $LoyaltyProgramHour->total_hours = $all_total_hours;
                $LoyaltyProgramHour->save();
            }
            
            $LoyaltyProgramHour->save();

            // loyalty program hours end

            $db_LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $customer_id)->first();
            $new_round_no = $db_LoyaltyProgramHour->round_no;

            if($new_round_no == $old_round_no)
            {
                $history_round_no_details = [$new_round_no];
                $history_hours_details = [$new_hours];
            }
            else
            {
                $history_round_no_details = [$old_round_no, $new_round_no];
                $history_hours_details = [$old_hours, $new_hours];
            }

            // history start

            $LoyaltyProgramHistory = new LoyaltyProgramHistory();
            $LoyaltyProgramHistory->user_id = $customer_id;
            $LoyaltyProgramHistory->outlet_id = $outlet_id;
            $LoyaltyProgramHistory->date = $date;
            $LoyaltyProgramHistory->time = $time;
            $LoyaltyProgramHistory->categories_id = implode(',', $category_id);
            $LoyaltyProgramHistory->services_id = implode(',', $services_id);
            $LoyaltyProgramHistory->hours = $totalHours;
            $LoyaltyProgramHistory->stamp = $this->calculateStamps($totalHours, $this->get_hours_per_stamp());
            $LoyaltyProgramHistory->round_no = implode(',', $history_round_no_details);
            $LoyaltyProgramHistory->save();

            // history end

            // history details start

            for($i=0; $i<count($history_round_no_details); $i++)
            {
                $LoyaltyProgramHistoryDetail = new LoyaltyProgramHistoryDetail();
                $LoyaltyProgramHistoryDetail->loyalty_program_history_id = $LoyaltyProgramHistory->id;
                $LoyaltyProgramHistoryDetail->user_id = $customer_id;
                $LoyaltyProgramHistoryDetail->round_no = $history_round_no_details[$i];
                $LoyaltyProgramHistoryDetail->hours = $history_hours_details[$i];
                $LoyaltyProgramHistoryDetail->save();
            }
            

            // history details end

            // history services start

            foreach($services_id as $item)
            {
                DB::table('loyalty_program_history_services')->insert([
                    'loyalty_program_history_id' => $LoyaltyProgramHistory->id,
                    'service_id' => $item,
                    'service_hours' => $this->calculate_total_services_hours([$item]),
                ]);
            }

            // history services end

            // history categories start

            foreach($category_id as $item)
            {
                DB::table('loyalty_program_history_category')->insert([
                    'loyalty_program_history_id' => $LoyaltyProgramHistory->id,
                    'category_id' => $item,
                ]);
            }

            // history categories end

            return response()->json(['status'=>'success', 'message'=>'Data saved successfully']);
        }
    }

    // calculate total service hours

    public static function calculate_total_services_hours($services_id)
    {
        $services = BusinessService::whereIn('id', $services_id)->get();

        $totalHours = 0;

        foreach($services as $item)
        {
            $time = $item->time;
            $timeType = $item->time_type;

            switch($timeType) {
                case 'hours':
                    $totalHours += $time;
                    break;
                case 'minutes':
                    $totalHours += $time / 60;
                    break;
                case 'days':
                    $totalHours += $time * 24;
                    break;
            }
        }

        return $totalHours;
    }

    public static function get_hours_per_stamp()
    {
        $loyalty_program_settings = DB::table('loyalty_program_settings')->first();

        return $loyalty_program_settings->hours_per_stamp ?? 1;
    }

    // Function to calculate stamps based on total hours
    public static function calculateStamps($totalHours, $hours_per_stamp) 
    {
        $stamps = $totalHours / $hours_per_stamp;
        return number_format($stamps, 1);
    }

    public static function create_free_reward_voucher($hours, $customer_id)
    {
        $title = "Free " . $hours . " hours Voucher";

        $voucher = new Voucher();
        $voucher->title = $title;
        $voucher->slug = str_replace(' ', '-',$title);
        $voucher->start_date_time = Carbon::now();
        $voucher->end_date_time = Carbon::now()->addMonths(2);
        $voucher->open_time = "9:00";
        $voucher->close_time = "18:00";
        $voucher->max_order_per_customer  = 1;
        $voucher->loyalty_point = 0;
        $voucher->status = 'active';
        $voucher->days = json_encode(["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]);
        $voucher->description = $title;
        $voucher->uses_limit = 1;    
        $voucher->discount_type = "percent";
        $voucher->discount = 100;
        $voucher->minimum_purchase_amount = 0;
        $voucher->max_discount = 0;
        $voucher->min_age = 18;
        $voucher->max_age = 60;
        $voucher->is_customer_specific = 1;
        $voucher->is_welcome = 0;
        $voucher->voucher_type = 2;
        $voucher->save();

        // voucher sevices start

        $voucher_services = [];

        foreach(BusinessService::where('status', 'active')->get() as $item)
        {
            $new_services_id[] = $item->id;

            $voucher_services[] = [
                'voucher_id' => $voucher->id,
                'business_service_id' => $item->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('voucher_services')->insert($voucher_services);
        
        // voucher sevices end

        // voucher outlet start

        $voucher_outlets = [];

        foreach(Outlet::where('status', 'active')->get() as $item)
        {
            $new_outlet_id[] = $item->id;

            $voucher_outlets[] = [
                'voucher_id' => $voucher->id,
                'outlet_id' => $item->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('voucher_outlets')->insert($voucher_outlets);

        // voucher outlet end

        // voucher users start

        $voucher_users = [
            'voucher_id' => $voucher->id,
            'user_id' => $customer_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        DB::table('voucher_users')->insert($voucher_users);

        // voucher users end

        // voucher gender start

        $new_gender = [
            'male', 'female', 'others'
        ]; 

        $voucher_gender = [];

        foreach($new_gender as $item)
        {
            $voucher_gender[] = [
                'voucher_id' => $voucher->id,
                'gender' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('voucher_gender')->insert($voucher_gender);

        // voucher gender end      

        // voucher redeem start

        $voucher_redeem = new VoucherRedeem();

        $voucher_redeem->voucher_id = $voucher->id;
        $voucher_redeem->user_id = $customer_id;

        $voucher_redeem->save();

        // voucher redeem end

        $voucher->service_id = implode(',', $new_services_id);
        $voucher->outlet_id = implode(',', $new_outlet_id);
        $voucher->user_id = $customer_id;
        $voucher->gender = implode(',', $new_gender);
        $voucher->used_time += 1;          
        $voucher->save();
    }

    // get_total_hours

    public function get_total_hours(Request $request)
    {
        // return $request->all();

        $customer_id = $request->customer_id;

        $db_LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $customer_id)->first();

        $data['db_LoyaltyProgramHour'] = $db_LoyaltyProgramHour;
        $data['hours_per_stamp'] = $this->get_hours_per_stamp();

        $data['loyalty_program_history_details'] = LoyaltyProgramHistoryDetail::where('user_id', $customer_id)
                                                    ->where('round_no', $db_LoyaltyProgramHour->round_no ?? '')
                                                    ->get();

        return response()->json($data);
    }

    // get_loyalty_program_history_table_data

    public function get_loyalty_program_history_table_data(Request $request)
    {
        $customer_id = $request->customer_id;

        $loyalty_program_history = LoyaltyProgramHistory::where('user_id', $customer_id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        // data table

        $new_data = [];

        foreach($loyalty_program_history as $key => $item)
        {
            $loyalty_services_id_arr = DB::table('loyalty_program_history_services')->where('loyalty_program_history_id', $item->id)->pluck('service_id')->toArray();
            $loyalty_services_name_arr = BusinessService::whereIn('id', $loyalty_services_id_arr)->pluck('name')->toArray();
            $item->services_name = implode(', ', $loyalty_services_name_arr);

            $new_data[] = array(
                $key+1,
                $item->services_name,
                date('j F Y', strtotime($item->date)),
                date('g:i A', strtotime($item->time)),
                $item->hours,                               
            );
        }

        $output = array(
            "draw"              => request()->draw,
            "recordsTotal"      => $loyalty_program_history->count(),
            "recordsFiltered"   => $loyalty_program_history->count(),
            "data"              => $new_data,
        );

        echo json_encode($output);
    }
}
