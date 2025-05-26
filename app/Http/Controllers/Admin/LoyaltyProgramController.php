<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoyaltyPoint;
use App\LoyaltyProgramHistory;
use App\LoyaltyProgramHistoryDetail;
use App\LoyaltyProgramHour;
use App\Outlet;
use App\User;
use App\Voucher;
use App\VoucherRedeem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoyaltyProgramController extends Controller
{
    // store

    // public function store(Request $request)
    // {
    //     // return $request->all();

    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'date' => 'required|date',
    //             'time' => 'required',
    //             'outlet_id' => 'required',
    //             'td_service_id' => 'required',
    //             'td_service_qty' => 'required',
    //             'category_id' => 'required'
    //         ],
    //         [],
    //         [
    //             'date' => 'Date',
    //             'time' => 'Time',
    //             'outlet_id' => 'Outlet',
    //             'td_service_id' => 'Services',
    //             'td_service_qty' => 'Service Quantity',
    //             'category_id' => 'Categories'
    //         ]
    //     );

    //     if($validator->fails())
    //     {
    //         $error = $validator->errors();

    //         return response()->json(['status' => "error", 'error'=>$error]);
    //     }
    //     else
    //     {
    //         $date = $request->date;
    //         $time = $request->time;
    //         $outlet_id = $request->outlet_id;
    //         $customer_id = $request->customer_id;           
    //         $category_id = $request->category_id ?? '';
    //         $services_id = $request->td_service_id ?? [];
    //         $services_qty = $request->td_service_qty ?? [];

    //         // calculate total service hours start

    //         $totalHours = 0;

    //         for($i=0; $i<count($services_id); $i++)
    //         {
    //             $totalHours += $services_qty[$i] * BusinessServiceController::calculate_single_service_hours($services_id[$i]);
    //         }

    //         // calculate total service hours end

    //         // loyalty program hours start

    //         $LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $customer_id)
    //                                                 ->where('category_id', $category_id)
    //                                                 ->first();

    //         $old_round_no = $LoyaltyProgramHour->round_no ?? 1;

    //         if($LoyaltyProgramHour)
    //         {
    //             $all_total_hours = $LoyaltyProgramHour->total_hours + $totalHours;   
    //             $free_one_reward_voucher_flag = $LoyaltyProgramHour->free_one_reward_voucher_flag;
    //         }
    //         else
    //         {
    //             $all_total_hours = $totalHours;

    //             $LoyaltyProgramHour = new LoyaltyProgramHour();   

    //             $free_one_reward_voucher_flag = 1;
    //         }   

    //         if($all_total_hours >= 20)
    //         {
    //             $new_hours = $all_total_hours - 20;
    //             $old_hours = $totalHours - $new_hours;

    //             $LoyaltyProgramHour->user_id = $customer_id;
    //             $LoyaltyProgramHour->category_id = $category_id;
    //             $LoyaltyProgramHour->total_hours = $all_total_hours - 20;             
    //             // $LoyaltyProgramHour->free_hours += 2;
    //             $LoyaltyProgramHour->free_one_reward_voucher_flag = 1;
    //             $LoyaltyProgramHour->total_round += 1;
    //             $LoyaltyProgramHour->round_no += 1;
    //             $LoyaltyProgramHour->save();

    //             $this->create_free_reward_voucher(1, $customer_id);
    //             $this->create_free_reward_voucher(1, $customer_id);
    //         }
    //         else if($all_total_hours >= 10)
    //         {
    //             $new_hours = $totalHours;
    //             $old_hours = $all_total_hours - $new_hours;

    //             $LoyaltyProgramHour->user_id = $customer_id;
    //             $LoyaltyProgramHour->category_id = $category_id;
    //             $LoyaltyProgramHour->total_hours = $all_total_hours;
    //             // $LoyaltyProgramHour->free_hours += 1;               
    //             $LoyaltyProgramHour->free_one_reward_voucher_flag = ($free_one_reward_voucher_flag == 1) ? 2 : $free_one_reward_voucher_flag;
    //             $LoyaltyProgramHour->save();

    //             if($free_one_reward_voucher_flag == 1)
    //             {
    //                 $this->create_free_reward_voucher(1, $customer_id);
    //             }
    //         }
    //         else
    //         {
    //             $new_hours = $totalHours;
    //             $old_hours = $all_total_hours - $new_hours;

    //             $LoyaltyProgramHour->user_id = $customer_id;
    //             $LoyaltyProgramHour->category_id = $category_id;
    //             $LoyaltyProgramHour->total_hours = $all_total_hours;
    //             $LoyaltyProgramHour->save();
    //         }

    //         $LoyaltyProgramHour->save();

    //         // loyalty program hours end

    //         $db_LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $customer_id)
    //                                                     ->where('category_id', $category_id)
    //                                                     ->first();

    //         $new_round_no = $db_LoyaltyProgramHour->round_no;

    //         if($new_round_no == $old_round_no)
    //         {
    //             $history_round_no_details = [$new_round_no];
    //             $history_hours_details = [$new_hours];
    //         }
    //         else
    //         {
    //             if($all_total_hours == 20)
    //             {
    //                 $history_round_no_details = [$old_round_no];
    //                 $history_hours_details = [$old_hours];
    //             }
    //             else
    //             {
    //                 $history_round_no_details = [$old_round_no, $new_round_no];
    //                 $history_hours_details = [$old_hours, $new_hours];
    //             }
    //         }

    //         // history start

    //         $LoyaltyProgramHistory = new LoyaltyProgramHistory();
    //         $LoyaltyProgramHistory->user_id = $customer_id;
    //         $LoyaltyProgramHistory->outlet_id = $outlet_id;
    //         $LoyaltyProgramHistory->date = $date;
    //         $LoyaltyProgramHistory->time = $time;
    //         $LoyaltyProgramHistory->categories_id = $category_id;
    //         $LoyaltyProgramHistory->services_id = implode(',', $services_id);
    //         $LoyaltyProgramHistory->hours = $totalHours;
    //         $LoyaltyProgramHistory->stamp = $this->calculateStamps($totalHours, $this->get_hours_per_stamp());
    //         $LoyaltyProgramHistory->round_no = implode(',', $history_round_no_details);
    //         $LoyaltyProgramHistory->save();

    //         // history end

    //         // history details start

    //         for($i=0; $i<count($history_round_no_details); $i++)
    //         {
    //             $LoyaltyProgramHistoryDetail = new LoyaltyProgramHistoryDetail();
    //             $LoyaltyProgramHistoryDetail->loyalty_program_history_id = $LoyaltyProgramHistory->id;
    //             $LoyaltyProgramHistoryDetail->user_id = $customer_id;
    //             $LoyaltyProgramHistoryDetail->category_id = $category_id;
    //             $LoyaltyProgramHistoryDetail->round_no = $history_round_no_details[$i];
    //             $LoyaltyProgramHistoryDetail->hours = $history_hours_details[$i];
    //             $LoyaltyProgramHistoryDetail->save();
    //         }


    //         // history details end

    //         // history services start

    //         for($i=0; $i<count($services_id); $i++)
    //         {
    //             DB::table('loyalty_program_history_services')->insert([
    //                 'loyalty_program_history_id' => $LoyaltyProgramHistory->id,
    //                 'service_id' => $services_id[$i],
    //                 'qty' => $services_qty[$i],
    //                 'service_hours' => BusinessServiceController::calculate_single_service_hours($services_id[$i]),
    //             ]);
    //         }

    //         // history services end

    //         // history categories start

    //         // foreach($category_id as $item)
    //         // {
    //         //     DB::table('loyalty_program_history_category')->insert([
    //         //         'loyalty_program_history_id' => $LoyaltyProgramHistory->id,
    //         //         'category_id' => $item,
    //         //     ]);
    //         // }

    //         // history categories end

    //         return response()->json(['status'=>'success', 'message'=>'Data saved successfully']);
    //     }
    // }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'date' => 'required|date',
                'time' => 'required',
                'outlet_id' => 'required',
                'td_service_id' => 'required',
                'td_service_qty' => 'required',
                'category_id' => 'required'
            ],
            [],
            [
                'date' => 'Date',
                'time' => 'Time',
                'outlet_id' => 'Outlet',
                'td_service_id' => 'Services',
                'td_service_qty' => 'Service Quantity',
                'category_id' => 'Categories'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => "error", 'error' => $validator->errors()]);
        }

        $hidden_loyalty_points = $request->hidden_loyalty_points ?? 0;

        $date = $request->date;
        $time = $request->time;
        $outlet_id = $request->outlet_id;
        $customer_id = $request->customer_id;
        $category_id = $request->category_id ?? '';
        $services_id = $request->td_service_id ?? [];
        $services_qty = $request->td_service_qty ?? [];

        // Calculate total service hours
        $totalHours = 0;
        foreach ($services_id as $index => $service_id) {
            $totalHours += $services_qty[$index] * BusinessServiceController::calculate_single_service_hours($service_id);
        }


        $activeRound = new LoyaltyProgramHour();
        $activeRound->user_id = $customer_id;
        $activeRound->category_id = $category_id;
        $activeRound->total_hours = $totalHours;
        $activeRound->status = 1;
        $activeRound->date = $date;
        $activeRound->time = $time;
        $activeRound->outlet_id = $outlet_id;
        $activeRound->save();

        foreach ($services_id as $index => $service_id) {
            DB::table('loyalty_program_history_services')->insert([
                'loyalty_program_history_id'    =>  $activeRound->id,
                'service_id'                    =>  $service_id,
                'qty'                           =>  $services_qty[$index],
                'service_hours'                 =>  BusinessServiceController::calculate_single_service_hours($service_id),
            ]);
        }


        
        if ($hidden_loyalty_points > 0) {
            LoyaltyPointController::coins_add($hidden_loyalty_points, $customer_id);
        }


        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
        exit;

        // if($totalHours <= 0) 
        // {
        //     return response()->json(['status' => 'failed', 'message' => 'Total hours must be greater than zero.']);
        // }
        // else if($totalHours > 20) 
        // {
        //     return response()->json(['status' => 'failed', 'message' => 'Total hours must be less than or equal to 20.']);
        // }
        // else
        // {
        //     $db_LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $customer_id)
        //                                 ->where('category_id', $category_id)
        //                                 ->where('status', 1)
        //                                 ->first();

        //     $db_total_hours = $db_LoyaltyProgramHour->total_hours ?? 0;

        //     $balance_hours = 20 - $db_total_hours;

        //     if($balance_hours < $totalHours)
        //     {
        //         return response()->json(['status' => 'failed', 'message' => 'Total hours must be less than or equal to balance hours.']);
        //     }     
        // }

        // Handle active round
        $activeRound = LoyaltyProgramHour::where('user_id', $customer_id)
            ->where('category_id', $category_id)
            ->where('status', 1)
            ->first();

        // Handle inactive round
        $inactiveRound = LoyaltyProgramHour::where('user_id', $customer_id)
            ->where('category_id', $category_id)
            ->where('status', 2)
            ->latest('round_no')
            ->first();

        if (!$activeRound && !$inactiveRound) {
            // Create a new active round if neither active nor inactive exists
            $activeRound = new LoyaltyProgramHour();
            $activeRound->user_id = $customer_id;
            $activeRound->category_id = $category_id;
            $activeRound->round_no = LoyaltyProgramHour::where('user_id', $customer_id)->where('category_id', $category_id)->max('round_no') + 1 ?? 1;
            $activeRound->total_hours = 0;
            $activeRound->status = 1;
            $activeRound->save();
        }

        if ($inactiveRound) {
            $currentTotalHours = $inactiveRound->total_hours + $totalHours;

            if ($currentTotalHours >= 20) {
                // Cap the inactive round at 20 hours and create a new inactive round
                $remainingHours = $currentTotalHours - 20;

                $inactiveRound->total_hours = 20;
                $inactiveRound->save();

                if ($remainingHours > 0) {
                    $newInactiveRound = new LoyaltyProgramHour();
                    $newInactiveRound->user_id = $customer_id;
                    $newInactiveRound->category_id = $category_id;
                    $newInactiveRound->round_no = $inactiveRound->round_no + 1;
                    $newInactiveRound->total_hours = $remainingHours;
                    $newInactiveRound->status = 2; // Inactive
                    $newInactiveRound->save();

                    $historyRounds = [$inactiveRound->round_no, $newInactiveRound->round_no];
                    $historyHours = [$totalHours - $remainingHours, $remainingHours];

                    $loyalty_program_hours_id = [$inactiveRound->id, $newInactiveRound->id];

                    $history_details_status = [$inactiveRound->status, $newInactiveRound->status];
                } else {
                    $historyRounds = [$inactiveRound->round_no];
                    $historyHours = [$totalHours];

                    $loyalty_program_hours_id = [$inactiveRound->id];

                    $history_details_status = [$inactiveRound->status];
                }
            } else {
                // Update the inactive round with the new total hours
                $inactiveRound->total_hours = $currentTotalHours;
                $inactiveRound->save();

                $historyRounds = [$inactiveRound->round_no];
                $historyHours = [$totalHours];

                $loyalty_program_hours_id = [$inactiveRound->id];

                $history_details_status = [$inactiveRound->status];
            }
        } else {
            $currentTotalHours = $activeRound->total_hours + $totalHours;

            if ($currentTotalHours >= 20) {
                // Cap the active round at 20 hours and create a new inactive round
                $remainingHours = $currentTotalHours - 20;

                if ($activeRound->total_hours == 20) {
                    if ($remainingHours > 0) {
                        $newInactiveRound = new LoyaltyProgramHour();
                        $newInactiveRound->user_id = $customer_id;
                        $newInactiveRound->category_id = $category_id;
                        $newInactiveRound->round_no = $activeRound->round_no + 1;
                        $newInactiveRound->total_hours = $remainingHours;
                        $newInactiveRound->status = 2; // Inactive
                        $newInactiveRound->save();

                        $historyRounds = [$newInactiveRound->round_no];
                        $historyHours = [$totalHours];

                        $loyalty_program_hours_id = [$newInactiveRound->id];

                        $history_details_status = [$newInactiveRound->status];
                    }
                } else {
                    $activeRound->total_hours = 20; // Cap at 20
                    $activeRound->save();

                    if ($remainingHours > 0) {
                        $newInactiveRound = new LoyaltyProgramHour();
                        $newInactiveRound->user_id = $customer_id;
                        $newInactiveRound->category_id = $category_id;
                        $newInactiveRound->round_no = $activeRound->round_no + 1;
                        $newInactiveRound->total_hours = $remainingHours;
                        $newInactiveRound->status = 2; // Inactive
                        $newInactiveRound->save();

                        $historyRounds = [$activeRound->round_no, $newInactiveRound->round_no];
                        $historyHours = [$totalHours - $remainingHours, $remainingHours];

                        $loyalty_program_hours_id = [$activeRound->id, $newInactiveRound->id];

                        $history_details_status = [$activeRound->status, $newInactiveRound->status];
                    } else {
                        $historyRounds = [$activeRound->round_no];
                        $historyHours = [$totalHours];

                        $loyalty_program_hours_id = [$activeRound->id];

                        $history_details_status = [$activeRound->status];
                    }
                }
            } else {
                // Update the active round with the new total hours
                $activeRound->total_hours = $currentTotalHours;
                $activeRound->save();

                $historyRounds = [$activeRound->round_no];
                $historyHours = [$totalHours];

                $loyalty_program_hours_id = [$activeRound->id];

                $history_details_status = [$activeRound->status];
            }
        }

        // Create loyalty program history
        $LoyaltyProgramHistory = new LoyaltyProgramHistory();
        $LoyaltyProgramHistory->user_id = $customer_id;
        $LoyaltyProgramHistory->outlet_id = $outlet_id;
        $LoyaltyProgramHistory->date = $date;
        $LoyaltyProgramHistory->time = $time;
        $LoyaltyProgramHistory->categories_id = $category_id;
        $LoyaltyProgramHistory->services_id = implode(',', $services_id);
        $LoyaltyProgramHistory->hours = $totalHours;
        $LoyaltyProgramHistory->stamp = $this->calculateStamps($totalHours, $this->get_hours_per_stamp());
        $LoyaltyProgramHistory->round_no = implode(',', $historyRounds);
        $LoyaltyProgramHistory->loyalty_program_hours_id = implode(',', $loyalty_program_hours_id);
        // $LoyaltyProgramHistory->status = implode(',', $history_details_status);
        $LoyaltyProgramHistory->created_by = Auth::user()->id;
        $LoyaltyProgramHistory->save();

        // Create loyalty program history details
        foreach ($historyRounds as $index => $round_no) {
            $LoyaltyProgramHistoryDetail = new LoyaltyProgramHistoryDetail();
            $LoyaltyProgramHistoryDetail->loyalty_program_history_id = $LoyaltyProgramHistory->id;
            $LoyaltyProgramHistoryDetail->user_id = $customer_id;
            $LoyaltyProgramHistoryDetail->category_id = $category_id;
            $LoyaltyProgramHistoryDetail->round_no = $round_no;
            $LoyaltyProgramHistoryDetail->hours = $historyHours[$index];
            $LoyaltyProgramHistoryDetail->loyalty_program_hours_id = $loyalty_program_hours_id[$index];
            $LoyaltyProgramHistoryDetail->status = $history_details_status[$index];
            $LoyaltyProgramHistoryDetail->created_by = Auth::user()->id;
            $LoyaltyProgramHistoryDetail->save();
        }

        // Create loyalty program history services
        foreach ($services_id as $index => $service_id) {
            DB::table('loyalty_program_history_services')->insert([
                'loyalty_program_history_id' => $LoyaltyProgramHistory->id,
                'service_id' => $service_id,
                'qty' => $services_qty[$index],
                'service_hours' => BusinessServiceController::calculate_single_service_hours($service_id),
            ]);
        }


        // loyalty coin add start

        if ($hidden_loyalty_points > 0) {
            LoyaltyPointController::coins_add($hidden_loyalty_points, $customer_id);
        }

        // loyalty coin add end

        // return $loyalty_program_hours_id;

        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
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

    // public static function create_free_reward_voucher($hours, $customer_id)
    // {
    //     $title = "Free " . $hours . " hours Voucher";

    //     $voucher = new Voucher();
    //     $voucher->title = $title;
    //     $voucher->slug = str_replace(' ', '-',$title);
    //     $voucher->start_date_time = Carbon::now();
    //     $voucher->end_date_time = Carbon::now()->addMonths(2);
    //     $voucher->open_time = "9:00";
    //     $voucher->close_time = "18:00";
    //     $voucher->max_order_per_customer  = 1;
    //     $voucher->loyalty_point = 0;
    //     $voucher->status = 'active';
    //     $voucher->days = json_encode(["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]);
    //     $voucher->description = $title;
    //     $voucher->uses_limit = 1;    
    //     $voucher->discount_type = "percent";
    //     $voucher->discount = 100;
    //     $voucher->minimum_purchase_amount = 0;
    //     $voucher->max_discount = 0;
    //     $voucher->min_age = 18;
    //     $voucher->max_age = 60;
    //     $voucher->is_customer_specific = 1;
    //     $voucher->is_welcome = 0;
    //     $voucher->voucher_type = 2;
    //     $voucher->save();

    //     // voucher sevices start

    //     $voucher_services = [];

    //     foreach(BusinessService::where('status', 'active')->get() as $item)
    //     {
    //         $new_services_id[] = $item->id;

    //         $voucher_services[] = [
    //             'voucher_id' => $voucher->id,
    //             'business_service_id' => $item->id,
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //         ];
    //     }

    //     DB::table('voucher_services')->insert($voucher_services);

    //     // voucher sevices end

    //     // voucher outlet start

    //     $voucher_outlets = [];

    //     foreach(Outlet::where('status', 'active')->get() as $item)
    //     {
    //         $new_outlet_id[] = $item->id;

    //         $voucher_outlets[] = [
    //             'voucher_id' => $voucher->id,
    //             'outlet_id' => $item->id,
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //         ];
    //     }

    //     DB::table('voucher_outlets')->insert($voucher_outlets);

    //     // voucher outlet end

    //     // voucher users start

    //     $voucher_users = [
    //         'voucher_id' => $voucher->id,
    //         'user_id' => $customer_id,
    //         'created_at' => Carbon::now(),
    //         'updated_at' => Carbon::now(),
    //     ];

    //     DB::table('voucher_users')->insert($voucher_users);

    //     // voucher users end

    //     // voucher gender start

    //     $new_gender = [
    //         'male', 'female', 'others'
    //     ]; 

    //     $voucher_gender = [];

    //     foreach($new_gender as $item)
    //     {
    //         $voucher_gender[] = [
    //             'voucher_id' => $voucher->id,
    //             'gender' => $item,
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //         ];
    //     }

    //     DB::table('voucher_gender')->insert($voucher_gender);

    //     // voucher gender end      

    //     // voucher redeem start

    //     $voucher_redeem = new VoucherRedeem();

    //     $voucher_redeem->voucher_id = $voucher->id;
    //     $voucher_redeem->user_id = $customer_id;

    //     $voucher_redeem->save();

    //     // voucher redeem end

    //     $voucher->service_id = implode(',', $new_services_id);
    //     $voucher->outlet_id = implode(',', $new_outlet_id);
    //     $voucher->user_id = $customer_id;
    //     $voucher->gender = implode(',', $new_gender);
    //     $voucher->used_time += 1;          
    //     $voucher->save();
    // }

    // get_total_hours

    public function get_loyalty_program_progress_tracker(Request $request)
    {
        // return $request->all();

        $customer_id = $request->customer_id;
        $category_id = $request->category_id;

        $db_LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $customer_id)
            ->where('category_id', $category_id)
            ->where('status', 1)
            ->get();

        $total_hours = 0;
        foreach($db_LoyaltyProgramHour as $item){
            if(!empty($item->balance_hours)){
                $total_hours += $item->balance_hours;
            }else{
                $total_hours += $item->total_hours;
            }
        }



        // $loyalty_program_history_details = LoyaltyProgramHistoryDetail::where('user_id', $customer_id)
        //     ->where('category_id', $category_id)
        //     ->where('round_no', $db_LoyaltyProgramHour->round_no ?? '')
        //     ->get();

        // $total_hours = $db_LoyaltyProgramHour->total_hours ?? 0;

        // balance hours for next free hours start

        if ($total_hours < 10) {
            $balance_hours = 10 - $total_hours;
        } else if ($total_hours >= 10 && $total_hours <= 20) {
            $balance_hours = 20 - $total_hours;
        } else {
            $balance_hours = 0;
        }

        $data['db_LoyaltyProgramHour'] = $db_LoyaltyProgramHour;
        $data['hours_per_stamp'] = $this->get_hours_per_stamp();
        $data['total_hours'] = $total_hours;
        $data['balance_hours'] = $balance_hours;
        // $data['loyalty_program_history_details'] = $loyalty_program_history_details;

        // balance hours for next free hours end

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

        foreach ($loyalty_program_history as $key => $item) {
            // category
            $item->category_name = Category::find($item->categories_id ?? '')->name ?? '';

            // services start

            $loyalty_program_history_services = DB::table('loyalty_program_history_services')->where('loyalty_program_history_id', $item->id)->get();

            $loyalty_services_arr = [];

            foreach ($loyalty_program_history_services as $list) {
                $BusinessService = BusinessService::where('id', $list->service_id)->first();

                $list->service_name = $BusinessService->name ?? '';

                $loyalty_services_arr[] = $list->service_name . " (" . $list->qty . ")";
            }

            $item->services_name = implode(', ', $loyalty_services_arr);

            // services end

            // created by name start

            $item->created_by_name = User::find($item->created_by)->name ?? '';

            // created by name end

            $action = '<a href="javascript:;" class="btn btn-danger btn-circle loyalty_program_delete_btn"
                        data-toggle="tooltip" data-row-id="' . $item->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';

            $new_data[] = array(
                $key + 1,
                $item->category_name,
                $item->services_name,
                date('j F Y', strtotime($item->date)),
                date('g:i A', strtotime($item->time)),
                $item->hours,
                $item->created_by_name,
                $action
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

    // delete_loyalty_program_history_table_data

    // public function delete_loyalty_program_history_table_data(Request $request)
    // {
    //     return $request->all();

    //     $history_id = $request->id;

    //     $loyaltyProgramHistory = LoyaltyProgramHistory::find($history_id);

    //     if($loyaltyProgramHistory)
    //     {
    //         $customer_id = $loyaltyProgramHistory->user_id;
    //         $category_id = $loyaltyProgramHistory->categories_id;
    //         $round_no = $loyaltyProgramHistory->round_no;
    //         $hours_to_deduct = $loyaltyProgramHistory->hours;

    //         $round_no_arr = explode(',', $round_no);

    //         if(count($round_no_arr) > 1)
    //         {
    //             return response()->json(['status' => 'failed', 'message' => 'Data can not be deleted']);      
    //         }

    //         foreach($round_no_arr as $item)
    //         {
    //             // Deduct hours from loyalty_program_hours table
    //             $loyaltyProgramHours = LoyaltyProgramHour::where('user_id', $customer_id)
    //                                                         ->where('category_id', $category_id)
    //                                                         ->where('round_no', $item)
    //                                                         ->first();

    //             if ($loyaltyProgramHours) 
    //             {
    //                 if($loyaltyProgramHours->status == 3)
    //                 {
    //                     return response()->json(['status' => 'failed', 'message' => 'Completed Data can not be deleted']);      
    //                 }
    //                 else
    //                 {
    //                     $loyaltyProgramHours->total_hours -= $loyaltyProgramHours->total_hours >= $hours_to_deduct ? $hours_to_deduct : 0;

    //                     if($loyaltyProgramHours->total_hours < 10)
    //                     {
    //                         $loyaltyProgramHours->free_one_reward_voucher_flag = 1;
    //                     }
    //                     else if($loyaltyProgramHours->total_hours >= 10 && $loyaltyProgramHours->total_hours < 20)
    //                     {
    //                         if($loyaltyProgramHours->free_one_reward_voucher_flag == 3)
    //                         {
    //                             $loyaltyProgramHours->free_one_reward_voucher_flag = 2;
    //                         }
    //                         else if($loyaltyProgramHours->free_one_reward_voucher_flag == 2)
    //                         {}
    //                         else if($loyaltyProgramHours->free_one_reward_voucher_flag == 1)
    //                         {}
    //                     }

    //                     $loyaltyProgramHours->save();

    //                     LoyaltyProgramHistory::where('id', $history_id)->delete();
    //                     LoyaltyProgramHistoryDetail::where('loyalty_program_history_id', $history_id)->delete();
    //                     DB::table('loyalty_program_history_services')->where('loyalty_program_history_id', $history_id)->delete();

    //                     return response()->json(['status' => 'success', 'message' => 'Data deleted successfully']);
    //                 }
    //             }
    //         }
    //     }
    //     else
    //     {
    //         return response()->json(['status' => 'failed', 'message' => 'Data not found']);
    //     }
    // }

    public function delete_loyalty_program_history_table_data(Request $request)
    {
        // return $request->all();

        $history_id = $request->id;

        $loyaltyProgramHistory = LoyaltyProgramHistory::find($history_id);

        if ($loyaltyProgramHistory) {
            $customer_id = $loyaltyProgramHistory->user_id;
            $category_id = $loyaltyProgramHistory->categories_id;
            $round_no = $loyaltyProgramHistory->round_no;
            $hours_to_deduct = $loyaltyProgramHistory->hours;

            // DB::table('loyalty_program_history_services')->where('loyalty_program_history_id', $history_id)->delete();            

            // used for history details start

            // $LoyaltyProgramHistoryDetail_row = LoyaltyProgramHistoryDetail::where('loyalty_program_history_id', $history_id)
            //                                                             ->first();

            // LoyaltyProgramHistoryDetail::where('loyalty_program_history_id', $history_id)->delete();

            $LoyaltyProgramHistoryDetail = LoyaltyProgramHistoryDetail::where('user_id', $customer_id)
                ->where('category_id', $category_id)
                // ->where('loyalty_program_hours_id', $LoyaltyProgramHistoryDetail_row->loyalty_program_hours_id)
                ->get();
            $total_hours = 0;
            $key = 1;

            foreach ($LoyaltyProgramHistoryDetail as $i => $item) {

                $data = [];
                $total_hours += $item->hours;
                if ($item->status != 3) {
                    // echo $item->id;
                    if ($total_hours > 20) {
                        if ($total_hours > $item->hours) {
                            $new_total_hours = $total_hours - $item->hours;
                        } else {
                            $new_total_hours = $item->hours - $total_hours;
                        }
                        $key++;
                        $total_hours = 0;
                        // echo $new_total_hours;
                    } else {
                        $new_total_hours = $item->hours;
                    }
                    $data = [
                        'round_no'  =>  $key,
                        'hours'     =>  $new_total_hours
                    ];
                } else {
                    if ($total_hours >= 20) {
                        $key++;
                    }
                    $total_hours = 0;
                }

                print_r($data);
                // if($item->status != 3)
                // {             
                //     if($total_hours > 20)
                //     {                   
                //         $key++;
                //         $total_hours = 0;
                //     }     

                //     // echo $key;                                        
                // }
                // else
                // {
                //     if($item->status == 3)
                //     {
                //         $key = $item->round_no;
                //     }                   
                // }

                // LoyaltyProgramHistoryDetail::where('id', $item->id)
                //                                 ->update(['round_no'=>$key]);
            }

            // used for history details end

            // used for history start

            // LoyaltyProgramHistory::where('id', $history_id)->delete();

            // $LoyaltyProgramHistory = LoyaltyProgramHistory::where('user_id', $customer_id)
            //                                                 ->where('categories_id', $category_id)
            //                                                 ->get();
            // $total_hours_history = 0;
            // $key_history = 1;
            // $prev_key_history = 0;

            // foreach($LoyaltyProgramHistory as $item)
            // {
            //     $total_hours_history += $item->hours;

            //     $prev_key_history = 0;

            //     if($total_hours_history > 20)
            //     {   
            //         $prev_key_history = $key_history;                
            //         $key_history++;
            //         $total_hours_history = 0;
            //     }   

            //     // echo "prev : ".$prev_key_history;
            //     // echo ", key : ".$key_history.", ";

            //     if($prev_key_history != 0)
            //     {
            //         $round_no_history = implode(',', [$prev_key_history, $key_history]);
            //     }
            //     else
            //     {
            //         $round_no_history = implode(',', [$key_history]);
            //     }

            //     // print_r($round_no_history);
            //     // echo "...";

            //     // LoyaltyProgramHistory::where('id', $item->id)
            //     //                             ->update(['round_no'=>$round_no_history]);
            // }

            // used for history end
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data not found']);
        }
    }
}
