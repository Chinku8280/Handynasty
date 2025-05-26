<?php

namespace App\Http\Controllers\Api;

use App\Booking;
use App\BookingItem;
use App\BookingTime;
use App\BusinessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingTime\UpdateBookingTime;
use App\Outlet;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function get_booking_times()
    {
        try {
            $bookingTimes = BookingTime::where('status', 'enabled')->get();

            if ($bookingTimes->isEmpty()) {
                return response()->json(['message' => 'No available booking times found.'], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'booking_times' => $bookingTimes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching booking times.', 
                'error' => $e->getMessage(                    
            )], 500);
        }
    }

    // therapist

    public function choose_preference_therapist(Request $request)
    {
        $service_id = $request->service_id;

        $therapist_id_arr = DB::table('business_service_user')->whereIn('business_service_id', $service_id)->pluck('user_id')->toArray();

        $employee = User::whereIn('id', $therapist_id_arr)->select('id', 'name')->get();

        if(!$employee->isEmpty())
        {
            return response()->json([
                'status' => true,
                'message' => 'Therapist List',
                'data' => $employee
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $employee
            ]);
        }
    }

    // book appointment

    public function book_appointment(Request $request)
    {
        // return $request->all();

        $rules = [
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'preference' => 'required',
            'outlet_id' => 'required|exists:outlets,id',
            'service_id' => 'required'
        ];

        if($request->preference == "yes")
        {
            $rules['therapist_id'] = "required|exists:users,id";
        }
        else
        {
            $rules['therapist_id'] = "nullable";
        }

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            // return $this->sendError($validator->errors());

            $errors = $validator->errors()->all();
            foreach($errors as $item)
            {
                return response()->json(['status'=>false, 'message' => $item]);
            }   
        }
        else
        {
            $user_id = Auth()->user()->id;

            $booking = new Booking();
            $booking->user_id = $user_id;
            $booking->booking_date = date('Y-m-d', strtotime($request->booking_date));
            $booking->booking_time = date('H:i:s', strtotime($request->booking_time));
            $booking->preference = $request->preference;
            $booking->outlet_id = $request->outlet_id;
            $booking->status = 'pending';
            $booking->source = "app";
            $booking->payment_status = 'unpaid';
            $booking->additional_notes = $request->remarks;
            $booking->created_by = $user_id;
            
            $result1 = $booking->save();

            if($result1)
            {
                if($request->preference == "yes")
                {
                    if($request->filled('therapist_id'))
                    {
                        DB::table('booking_therapist')->insert([
                            'booking_id' => $booking->id,
                            'therapist_id' => $request->therapist_id
                        ]);
                    }
                }

                $grand_total = 0;
                $total = 0;

                $service_id = $request->service_id;

                for($i=0; $i<count($service_id); $i++)
                {
                    $business_service = BusinessService::find($service_id[$i]);

                    if($business_service)
                    {
                        $service_price = $business_service->price;

                        if($business_service->discount_type == "percent")
                        {
                            $discount_amt = $service_price * ($business_service->discount/100);
                        }
                        else
                        {
                            $discount_amt = $business_service->discount;
                        }

                        $gross_amount = ($service_price - $discount_amt) * 1;
            
                        $booking_items = new BookingItem();
                        $booking_items->booking_id = $booking->id;
                        $booking_items->business_service_id = $service_id[$i];
                        $booking_items->quantity = 1;
                        $booking_items->unit_price = $service_price;                 
                        $booking_items->discount_type = $business_service->discount_type;
                        $booking_items->discount = $business_service->discount;
                        $booking_items->discount_amount = $discount_amt;
                        $booking_items->amount = $gross_amount;
        
                        $booking_items->save();

                        $total += $gross_amount;
                    }
                }
    
                $booking->original_amount = $total;
                $booking->amount_to_pay = $total;
                $booking->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Booking Successfull'
                ]);
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking Failed'
                ]);
            }
        }
    }

    // booking list

    public function booking_list()
    {
        $user_id = Auth()->user()->id;

        $booking = Booking::where('user_id', $user_id)
                        ->where('status', '!=', 'canceled')
                        ->select('id', 'user_id', 'booking_date', 'booking_time', 'outlet_id', 'amount_to_pay', 'status', 'payment_status')
                        ->get();

        if(!$booking->isEmpty())
        {
            foreach($booking as $item)
            {
                // customer
                $item->customer_name = User::find($item->user_id)->name ?? '';

                // booking date & time
                $item->format_booking_date = date('j M Y', strtotime($item->booking_date));
                $item->format_booking_time = date('h:i:s A', strtotime($item->booking_time));

                // outlet
                $item->outlet_name = Outlet::find($item->outlet_id)->outlet_name ?? '';

                // services
                $service_id_arr = BookingItem::where('booking_id', $item->id)->pluck('business_service_id')->toArray();
                $service_name_arr = BusinessService::whereIn('id', $service_id_arr)->pluck('name')->toArray();

                $item->service_name_list = implode(', ', $service_name_arr);
            }

            return response()->json([
                'status' => true,
                'message' => 'Booking List',
                'data' => $booking
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $booking
            ]);
        }
    }

    // booking details

    public function booking_details($booking_id)
    {
        $user_id = Auth()->user()->id;

        $booking = booking::select('id', 'user_id', 'booking_date', 'booking_time', 'outlet_id', 'amount_to_pay', 'status', 'payment_status')
                            ->find($booking_id);

        if($booking)
        {  
            // customer
            $booking->customer_name = User::find($booking->user_id)->name ?? '';

            // booking date & time
            $booking->format_booking_date = date('j M l Y', strtotime($booking->booking_date));
            $booking->format_booking_time = date('h:i A', strtotime($booking->booking_time));

            // outlet
            $outlet = Outlet::find($booking->outlet_id);

            $booking->outlet_name = $outlet->outlet_name ?? '';
            $booking->outlet_address = $outlet->address ?? '';
            $booking->outlet_phone_no = $outlet->phone ?? '';

            // services
            $service_id_arr = BookingItem::where('booking_id', $booking->id)->pluck('business_service_id')->toArray();
            $service_name_arr = BusinessService::whereIn('id', $service_id_arr)->pluck('name')->toArray();

            $booking->service_name_list = implode(', ', $service_name_arr);
            
            return response()->json([
                'status' => true,
                'message' => 'Booking Details',
                'data' => $booking
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ]);
        }
    }

    // cancel booking

    public function cancel_booking(Request $request)
    {
        $booking_id = $request->booking_id;

        $booking = Booking::find($booking_id);

        if($booking)
        {
            Booking::where('id', $booking_id)->update(['status'=>'canceled']);

            return response()->json([
                'status' => true,
                'message' => 'Booking Cancelled',
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
            ]);
        }
    }
}
