<?php

namespace App\Http\Controllers\Api;

use App\BusinessService;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoyaltyProgramHistory;
use App\LoyaltyProgramHistoryDetail;
use App\LoyaltyProgramHour;
use App\Outlet;
use App\Voucher;
use App\VoucherRedeem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoyaltyProgramController extends Controller
{
    // loyalty_program_progress_tracker

    public static function get_hours_per_stamp()
    {
        $loyalty_program_settings = DB::table('loyalty_program_settings')->first();

        return $loyalty_program_settings->hours_per_stamp ?? 1;
    }


    public function loyalty_program_progress_tracker(Request $request)
    {

        $user_id = Auth::user()->id;
        $category_id = $request->category_id;

        $db_LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $user_id)
            ->where('category_id', $category_id)
            ->where('status', 1)
            ->orderBy('created_at', 'asc') // optional but useful
            ->get();

        $total_hours = 0;
        foreach ($db_LoyaltyProgramHour as $item) {
            $total_hours += $item->total_hours;
        }

        $totalCircles = 20;
        $hoursPerCircle = $this->get_hours_per_stamp(); // e.g., 1
        $maxUsableHours = $totalCircles * $hoursPerCircle;
        $usableHours = min($total_hours, $maxUsableHours);
        $totalUnits = $usableHours / $hoursPerCircle;
        $filledCircles = floor($totalUnits);
        $hasHalfCircle = fmod($totalUnits, 1) >= 0.5;

        $slots = [];
        $circleCount = 0;

        // Step 1: Fill slots from $db_LoyaltyProgramHour
        $remainingHours = $usableHours;
        $historyIndex = 0;
        while ($remainingHours > 0 && $circleCount < $totalCircles) {
            $circleFilled = 0;
            $circleHistoryIds = [];

            while ($historyIndex < count($db_LoyaltyProgramHour) && $circleFilled < $hoursPerCircle) {
                $entry = $db_LoyaltyProgramHour[$historyIndex];

                $take = min($entry->total_hours, $hoursPerCircle - $circleFilled);
                $circleFilled += $take;
                $remainingHours -= $take;

                $circleHistoryIds[] = $entry->id;

                // Subtract used time from the record
                $db_LoyaltyProgramHour[$historyIndex]->total_hours -= $take;
                if ($db_LoyaltyProgramHour[$historyIndex]->total_hours <= 0) {
                    $historyIndex++;
                }
            }

            $filledPercent = ($circleFilled / $hoursPerCircle) * 100;
            $circleCount++;
            $slots[] = [
                'filled' => min($filledPercent, 100),
                'history_ids' => $circleHistoryIds,
                'circle_text' => $circleCount
            ];
        }

        // Step 2: Add empty slots if needed
        for ($i = $circleCount; $i < $totalCircles; $i++) {
            $circleCount++;
            $slots[] = [
                'filled' => 0,
                'history_ids' => [],
                'circle_text' => $circleCount
            ];
        }

        if ($total_hours < 10) {
            $balance_hours = 10 - $total_hours;
        } else if ($total_hours >= 10 && $total_hours <= 20) {
            $balance_hours = 20 - $total_hours;
        } else {
            $balance_hours = 0;
        }

        $loyalty_program_settings = DB::table('loyalty_program_settings')->first();


        $data['total_hours'] = $total_hours;
        $data['balance_hours'] = $balance_hours;
        $data['hours_per_stamp'] = $this->get_hours_per_stamp();
        $data['setting_description'] = $loyalty_program_settings->description ?? '';
        $data['slots'] = $slots;
        $data['LoyaltyProgramHour'] = $db_LoyaltyProgramHour;
        $data['setting_expired_days'] = $expired_days ?? '';
        $data['expire_date'] = $expire_date ?? '';



        return response()->json([
            'status'    =>  true,
            'message'   =>  'Progress Tracker',
            'data'      =>  $data
        ]);
        exit;

        $user_id = Auth::user()->id;
        $category_id = $request->category_id;

        $db_LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $user_id)
            ->where('category_id', $category_id)
            ->where('status', 1)
            ->first();

        $loyalty_program_settings = DB::table('loyalty_program_settings')->first();

        $total_hours = $db_LoyaltyProgramHour->total_hours ?? 0;
        $hours_per_stamp = $loyalty_program_settings->hours_per_stamp ?? 1;

        // balance hours for next free hours start

        if ($total_hours < 10) {
            $balance_hours = 10 - $total_hours;
        } else if ($total_hours >= 10 && $total_hours <= 20) {
            $balance_hours = 20 - $total_hours;
        } else {
            $balance_hours = 0;
        }

        // balance hours for next free hours end

        $loyalty_program_history_details = LoyaltyProgramHistoryDetail::where('user_id', $user_id)
            ->where('category_id', $category_id)
            ->where('round_no', $db_LoyaltyProgramHour->round_no ?? '')
            ->get();

        $loyalty_program_stamp_text_settings = DB::table('loyalty_program_stamp_text_settings')->get();

        // start

        // Example variables to calculate total hours and hours per stamp
        $totalHours = $total_hours;
        $hoursPerStamp = $hours_per_stamp; // Assume hours per stamp are 2, adjust according to your requirement
        $totalSlots = 20; // We will always have 20 slots

        // Prepare slots data
        $slots = [];
        $accumulatedHours = 0; // Track hours for filling slots
        $currentSlot = 0; // Start filling from the first slot
        $stampIds = []; // Track history IDs for each slot

        // Iterate over the history entries and fill slots
        foreach ($loyalty_program_history_details as $historyEntry) {
            $hoursInEntry = $historyEntry->hours;
            $historyId = $historyEntry->loyalty_program_history_id;

            while ($hoursInEntry > 0 && $currentSlot < $totalSlots) {
                // Remaining hours to fill in the current slot
                $remainingHoursInSlot = $hoursPerStamp - $accumulatedHours;

                // If current history entry can fully fill the slot
                if ($hoursInEntry >= $remainingHoursInSlot) {
                    $accumulatedHours += $remainingHoursInSlot;
                    $hoursInEntry -= $remainingHoursInSlot;

                    // Assign history ID to this slot
                    if (!isset($stampIds[$currentSlot])) {
                        $stampIds[$currentSlot] = [];
                    }
                    $stampIds[$currentSlot][] = $historyId;

                    // Append a fully filled slot
                    $slots[] = [
                        'filled' => 100,
                        'history_ids' => $stampIds[$currentSlot]
                    ];

                    // Move to next slot and reset accumulated hours
                    $currentSlot++;
                    $accumulatedHours = 0;
                } else {
                    // Partially fill the current slot
                    $accumulatedHours += $hoursInEntry;

                    // Assign history ID to this slot
                    if (!isset($stampIds[$currentSlot])) {
                        $stampIds[$currentSlot] = [];
                    }
                    $stampIds[$currentSlot][] = $historyId;

                    // Use up all the hours in this history entry
                    $hoursInEntry = 0;
                }
            }
        }

        // Check if there's a partially filled slot
        if ($accumulatedHours > 0 && $currentSlot < $totalSlots) {
            $fillPercentage = ($accumulatedHours / $hoursPerStamp) * 100;
            $slots[] = [
                'filled' => $fillPercentage,
                'history_ids' => $stampIds[$currentSlot],
            ];
            $currentSlot++;
        }

        // Fill remaining slots with empty values
        while ($currentSlot < $totalSlots) {
            $slots[] = [
                'filled' => 0,
                'history_ids' => []
            ];
            $currentSlot++;
        }

        // end

        for ($i = 0; $i < count($slots); $i++) {
            if (isset($loyalty_program_stamp_text_settings[$i])) {
                $slots[$i]['circle_text'] = $loyalty_program_stamp_text_settings[$i]->stamp_text ?? '';
            } else {
                $slots[$i]['circle_text'] = '';
            }
        }

        // expire date start

        $expired_days = $loyalty_program_settings->expired_days ?? '';
        $start_date = $db_LoyaltyProgramHour->created_at ?? '';
        if (!empty($start_date)) {
            $expire_date = Carbon::parse($start_date)->addDays($expired_days)->format('Y-m-d');
            $days_left = Carbon::now()->diffInDays(Carbon::parse($expire_date), false);
            $days_left = $days_left > 0 ? $days_left : 0;
            // $days_left = $days_left;
        } else {
            $expire_date = '';
            $days_left = '';
        }

        // expire date end

        $data['total_hours'] = $total_hours;
        $data['balance_hours'] = $balance_hours;
        $data['hours_per_stamp'] = $hours_per_stamp;
        $data['setting_description'] = $loyalty_program_settings->description ?? '';
        $data['loyalty_program_history_details'] = $loyalty_program_history_details;
        $data['slots'] = $slots;
        $data['LoyaltyProgramHour'] = $db_LoyaltyProgramHour;
        $data['setting_expired_days'] = $expired_days ?? '';
        $data['expire_date'] = $expire_date ?? '';
        $data['expire_days_left'] = $days_left;

        return response()->json([
            'status' => true,
            'message' => 'Progress Tracker',
            'data' => $data
        ]);
    }

    public function loyalty_program_progress_tracker_without_login(Request $request)
    {
        $user_id = "";
        $category_id = $request->category_id;

        $db_LoyaltyProgramHour = "";

        $loyalty_program_settings = DB::table('loyalty_program_settings')->first();

        $total_hours = 0;
        $hours_per_stamp = $loyalty_program_settings->hours_per_stamp ?? 1;

        // balance hours for next free hours start

        if ($total_hours < 10) {
            $balance_hours = 10 - $total_hours;
        } else if ($total_hours >= 10 && $total_hours <= 20) {
            $balance_hours = 20 - $total_hours;
        } else {
            $balance_hours = 0;
        }

        // balance hours for next free hours end

        $loyalty_program_history_details = [];

        $loyalty_program_stamp_text_settings = DB::table('loyalty_program_stamp_text_settings')->get();

        // start

        // Example variables to calculate total hours and hours per stamp
        $totalHours = $total_hours;
        $hoursPerStamp = $hours_per_stamp; // Assume hours per stamp are 2, adjust according to your requirement
        $totalSlots = 20; // We will always have 20 slots

        // Prepare slots data
        $slots = [];
        $accumulatedHours = 0; // Track hours for filling slots
        $currentSlot = 0; // Start filling from the first slot
        $stampIds = []; // Track history IDs for each slot

        // Check if there's a partially filled slot
        if ($accumulatedHours > 0 && $currentSlot < $totalSlots) {
            $fillPercentage = ($accumulatedHours / $hoursPerStamp) * 100;
            $slots[] = [
                'filled' => $fillPercentage,
                'history_ids' => $stampIds[$currentSlot],
            ];
            $currentSlot++;
        }

        // Fill remaining slots with empty values
        while ($currentSlot < $totalSlots) {
            $slots[] = [
                'filled' => 0,
                'history_ids' => []
            ];
            $currentSlot++;
        }

        // end

        for ($i = 0; $i < count($slots); $i++) {
            if (isset($loyalty_program_stamp_text_settings[$i])) {
                $slots[$i]['circle_text'] = $loyalty_program_stamp_text_settings[$i]->stamp_text ?? '';
            } else {
                $slots[$i]['circle_text'] = '';
            }
        }

        // expire date start

        $expired_days = $loyalty_program_settings->expired_days ?? '';

        // expire date end

        $data['total_hours'] = $total_hours;
        $data['balance_hours'] = $balance_hours;
        $data['hours_per_stamp'] = $hours_per_stamp;
        $data['setting_description'] = $loyalty_program_settings->description ?? '';
        $data['loyalty_program_history_details'] = [];
        $data['slots'] = $slots;
        $data['LoyaltyProgramHour'] = null;
        $data['setting_expired_days'] = $expired_days ?? '';
        $data['expire_date'] = null;
        $data['expire_days_left'] = null;

        return response()->json([
            'status' => true,
            'message' => 'Progress Tracker',
            'data' => $data
        ]);
    }

    // loyalty_program_session_details

    public function loyalty_program_session_details(Request $request)
    {
        $user_id = Auth::user()->id;

        $db_LoyaltyProgramHour = LoyaltyProgramHour::where('id', $request->filled('loyalty_program_history_id'))->first();
        $loyalty_program_history_services = DB::table('loyalty_program_history_services')
            ->where('loyalty_program_history_id', $db_LoyaltyProgramHour->id)
            ->select('service_id', 'service_hours', 'qty')
            ->get();

        foreach ($loyalty_program_history_services as $service) {
            $service->service_name = BusinessService::find($service->service_id)->name ?? '';
            $service->total_service_hours = $service->service_hours * $service->qty;
        }

        // outlet
        $outlet = Outlet::find($db_LoyaltyProgramHour->outlet_id);

        // category
        $category = Category::find($db_LoyaltyProgramHour->category_id ?? '');


        $result[] = [
            'date_of_visit'     =>  date('d M Y', strtotime($db_LoyaltyProgramHour->date)),
            'time'              =>  date('h:i a', strtotime($db_LoyaltyProgramHour->time)),
            'outlet_name'       =>  $outlet->outlet_name ?? '',
            'category_name'     =>  $category->name ?? '',
            'services'          =>  $loyalty_program_history_services
        ];

        return response()->json([
            'status'    =>  true,
            'message'   => 'Session Details',
            'data'      =>  $result
        ]);


        exit;

        if ($request->filled('loyalty_program_history_id')) {
            $loyalty_program_history_id_arr = $request->loyalty_program_history_id;

            $loyalty_program_history = LoyaltyProgramHistory::where('user_id', $user_id)
                ->whereIn('id', $loyalty_program_history_id_arr)
                ->get();

            $result = [];
            foreach ($loyalty_program_history as $item) {
                // outlet
                $outlet = Outlet::find($item->outlet_id);

                // category
                $category = Category::find($item->categories_id ?? '');

                // services start

                $loyalty_program_history_services = DB::table('loyalty_program_history_services')
                    ->where('loyalty_program_history_id', $item->id)
                    ->select('service_id', 'service_hours', 'qty')
                    ->get();

                foreach ($loyalty_program_history_services as $service) {
                    $service->service_name = BusinessService::find($service->service_id)->name ?? '';
                    $service->total_service_hours = $service->service_hours * $service->qty;
                }

                // services end

                $result[] = [
                    'date_of_visit' => date('d M Y', strtotime($item->date)),
                    'time' => date('h:i a', strtotime($item->time)),
                    'outlet_name' => $outlet->outlet_name ?? '',
                    'category_name' => $category->name ?? '',
                    'services' => $loyalty_program_history_services
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Session Deatils',
                'data' => $result
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ]);
        }
    }

    // loyalty_program_recent_visits

    public function loyalty_program_recent_visits(Request $request)
    {
        $user_id = Auth::user()->id;

        $loyalty_program_history = DB::table('loyalty_program_hours')->where('user_id', $user_id)->orderBy('created_at', 'desc')->get();

        foreach ($loyalty_program_history as $item) {
            $item->new_date_format = date('j F Y', strtotime($item->date));

            // time start

            $minutes_to_add = $item->total_hours * 60; // Convert hours to minutes
            $new_time = date('h:i A', strtotime("+$minutes_to_add minutes", strtotime($item->time)));

            $item->new_time_format = date('h:i A', strtotime($item->time)) . " - " . $new_time;

            // time end

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

            // category
            $item->category_name = Category::find($item->category_id ?? '')->name ?? '';
        }

        return response()->json([
            'status' => true,
            'message' => 'Recent Visits',
            'data' => $loyalty_program_history
        ]);



        exit;
        $user_id = Auth::user()->id;

        $loyalty_program_history = DB::table('loyalty_program_history')->where('user_id', $user_id)->orderBy('created_at', 'desc')->get();

        foreach ($loyalty_program_history as $item) {
            $item->new_date_format = date('j F Y', strtotime($item->date));

            // time start

            $minutes_to_add = $item->hours * 60; // Convert hours to minutes
            $new_time = date('h:i A', strtotime("+$minutes_to_add minutes", strtotime($item->time)));

            $item->new_time_format = date('h:i A', strtotime($item->time)) . " - " . $new_time;

            // time end

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

            // category
            $item->category_name = Category::find($item->categories_id ?? '')->name ?? '';
        }

        return response()->json([
            'status' => true,
            'message' => 'Recent Visits',
            'data' => $loyalty_program_history
        ]);
    }

    public function loyalty_program_recent_visit_details(Request $request)
    {

        $user_id = Auth::user()->id;

        $history_id = $request->history_id;

        $loyalty_program_history =  LoyaltyProgramHour::find($history_id);

        if ($loyalty_program_history) {
            // outlet
            $outlet = Outlet::find($loyalty_program_history->outlet_id);

            // category
            $category = Category::find($loyalty_program_history->category_id ?? '');

            // services start

            $loyalty_program_history_services = DB::table('loyalty_program_history_services')
                ->where('loyalty_program_history_id', $history_id)
                ->select('service_id', 'service_hours', 'qty')
                ->get();

            foreach ($loyalty_program_history_services as $service) {
                $service->service_name = BusinessService::find($service->service_id)->name ?? '';
                $service->total_service_hours = $service->service_hours * $service->qty;
            }

            // services end

            $data = [
                'history_id'        =>  (int) $history_id,
                'date_of_visit'     =>  date('d M Y', strtotime($loyalty_program_history->date)),
                'time'              =>  date('h:i a', strtotime($loyalty_program_history->time)),
                'outlet_name'       =>  $outlet->outlet_name ?? '',
                'category_name'     =>  $category->name ?? '',
                'services'          =>  $loyalty_program_history_services
            ];

            return response()->json([
                'status' => true,
                'message' => 'Recent visit Details',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ]);
        }


        exit;



        $user_id = Auth::user()->id;

        $history_id = $request->history_id;

        $loyalty_program_history = LoyaltyProgramHistory::find($history_id);

        if ($loyalty_program_history) {
            // outlet
            $outlet = Outlet::find($loyalty_program_history->outlet_id);

            // category
            $category = Category::find($loyalty_program_history->categories_id ?? '');

            // services start

            $loyalty_program_history_services = DB::table('loyalty_program_history_services')
                ->where('loyalty_program_history_id', $history_id)
                ->select('service_id', 'service_hours', 'qty')
                ->get();

            foreach ($loyalty_program_history_services as $service) {
                $service->service_name = BusinessService::find($service->service_id)->name ?? '';
                $service->total_service_hours = $service->service_hours * $service->qty;
            }

            // services end

            $data = [
                'history_id' => (int) $history_id,
                'date_of_visit' => date('d M Y', strtotime($loyalty_program_history->date)),
                'time' => date('h:i a', strtotime($loyalty_program_history->time)),
                'outlet_name' => $outlet->outlet_name ?? '',
                'category_name' => $category->name ?? '',
                'services' => $loyalty_program_history_services
            ];

            return response()->json([
                'status' => true,
                'message' => 'Recent visit deatils',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ]);
        }
    }

    // loyalty_program_reward_voucher

    public function loyalty_program_reward_voucher(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'round_no' => 'required',
                'category_id' => 'required',
                'reward_type' => 'required'
            ],
            [],
            [
                'round_no' => 'Round no',
                'category_id' => 'Categories'
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            foreach ($errors as $item) {
                return response()->json(['status' => false, 'message' => $item]);
            }
        }

        $user_id = Auth::user()->id;
        $category_id = $request->category_id;
        $round_no = $request->round_no;
        $reward_type = $request->reward_type;

        $category = Category::find($category_id);
        $category_name = $category->name ?? '';

        $db_LoyaltyProgramHour = LoyaltyProgramHour::where('user_id', $user_id)
            ->where('category_id', $category_id)
            ->where('round_no', $round_no)
            ->where('status', 1)
            ->first();

        if ($db_LoyaltyProgramHour) {
            if ($db_LoyaltyProgramHour->free_one_reward_voucher_flag == 1) {
                if ($reward_type == 1) {
                    if ($db_LoyaltyProgramHour->total_hours >= 10) {
                        $db_LoyaltyProgramHour->free_one_reward_voucher_flag = 2;
                        $db_LoyaltyProgramHour->save();

                        $this->create_free_reward_voucher(1, $user_id, $category_name);

                        return response()->json([
                            'status' => true,
                            'message' => "Voucher is Redeemed",
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'You total hours is less than 10'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have to redeemed 1st reward voucher'
                    ]);
                }
            } else if ($db_LoyaltyProgramHour->free_one_reward_voucher_flag == 2) {
                if ($reward_type == 2) {
                    if ($db_LoyaltyProgramHour->total_hours == 20) {
                        $db_LoyaltyProgramHour->free_one_reward_voucher_flag = 3;
                        $db_LoyaltyProgramHour->save();

                        $this->create_free_reward_voucher(1, $user_id, $category_name);

                        return response()->json([
                            'status' => true,
                            'message' => "Voucher is Redeemed",
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'You total hours is less than 20'
                        ]);
                    }
                } else if ($reward_type == 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have already redeemed this voucher'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have to redeemed 2nd reward voucher'
                    ]);
                }
            } else if ($db_LoyaltyProgramHour->free_one_reward_voucher_flag == 3) {
                if ($reward_type == 3) {
                    if ($db_LoyaltyProgramHour->total_hours == 20) {
                        $db_LoyaltyProgramHour->free_one_reward_voucher_flag = 4;
                        $db_LoyaltyProgramHour->status = 3;
                        $db_LoyaltyProgramHour->save();

                        LoyaltyProgramHistoryDetail::where('user_id', $user_id)
                            ->where('category_id', $category_id)
                            ->where('status', 1)
                            ->where('round_no', $round_no)
                            ->update(['status' => 3]);

                        $this->create_free_reward_voucher(1, $user_id, $category_name);

                        $inactiveRound = LoyaltyProgramHour::where('user_id', $user_id)
                            ->where('category_id', $category_id)
                            ->where('status', 2)
                            ->oldest('round_no')
                            ->first();

                        if ($inactiveRound) {
                            $inactiveRound->status = 1;
                            $inactiveRound->save();

                            LoyaltyProgramHistoryDetail::where('user_id', $user_id)
                                ->where('category_id', $category_id)
                                ->where('status', 2)
                                ->where('round_no', $inactiveRound->round_no)
                                ->update(['status' => 1]);
                        }

                        return response()->json([
                            'status' => true,
                            'message' => "Voucher is Redeemed",
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'You total hours is less than 20'
                        ]);
                    }
                } else if ($reward_type == 1 || $reward_type == 2) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have already redeemed this voucher'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have to redeemed 2nd reward voucher'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'All reward vouchers are redeemed in this round'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ]);
        }
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

    public static function create_free_reward_voucher($hours, $customer_id, $category_name)
    {
        if (!empty($category_name)) {
            // $title = "Free " . $hours . " hour " . $category_name . " Voucher";
            $title = $category_name;
        } else {
            $title = "Free " . $hours . " hour Voucher";
        }

        $voucher = new Voucher();
        $voucher->title = $title;
        $voucher->slug = str_replace(' ', '-', $title);
        $voucher->start_date_time = Carbon::now();
        $voucher->end_date_time = Carbon::now()->addMonths(12);
        // $voucher->open_time = "9:00";
        // $voucher->close_time = "18:00";
        $voucher->max_order_per_customer  = 1;
        $voucher->loyalty_point = 0;
        $voucher->status = 'active';
        $voucher->days = json_encode(["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]);
        $voucher->description = $title;
        // $voucher->uses_limit = 1;    
        $voucher->discount_type = "percent";
        $voucher->discount = 100;
        $voucher->minimum_purchase_amount = 0;
        $voucher->max_discount = 0;
        $voucher->min_age = 18;
        $voucher->max_age = 60;
        $voucher->is_customer_specific = 1;
        $voucher->validity = 12;
        $voucher->validity_type = "months";
        $voucher->is_redeemable = 1;
        $voucher->is_welcome = 0;
        $voucher->voucher_type = 2;
        $voucher->save();

        // voucher sevices start

        $voucher_services = [];

        foreach (BusinessService::where('status', 'active')->get() as $item) {
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

        foreach (Outlet::where('status', 'active')->get() as $item) {
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
            'male',
            'female',
            'others'
        ];

        $voucher_gender = [];

        foreach ($new_gender as $item) {
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
}
