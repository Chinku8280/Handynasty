<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\BookingItem;
use App\BusinessService;
use App\CompanySetting;
use App\Coupon;
use App\EmployeeGroup;
use App\Helper\Reply;
use App\Location;
use App\Notifications\BookingCancel;
use App\Notifications\BookingReminder;
use App\TaxSetting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\BookingStatusMultiUpdate;
use App\Http\Requests\Booking\UpdateBooking;
use App\Payment;
use App\PaymentGatewayCredentials;

class BookingController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->credentials = PaymentGatewayCredentials::first();
        $setting = CompanySetting::with('currency')->first();

        view()->share('pageTitle', __('menu.bookings'));
        view()->share('credentials', $this->credentials);
        view()->share('setting', $setting);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        if(\request()->ajax()){
            $bookings = Booking::orderBy('date_time', 'desc');

            if(\request('filter_status') != ""){
                $bookings->where('bookings.status', \request('filter_status'));
            }

            if(\request('filter_customer') != ""){
                $bookings->where('bookings.user_id', \request('filter_customer'));
            }

            if(\request('filter_location') != ""){
                $bookings->leftJoin('booking_items', 'bookings.id', 'booking_items.booking_id')
                    ->leftJoin('business_services', 'booking_items.business_service_id', 'business_services.id')
                    ->leftJoin('locations', 'business_services.location_id', 'locations.id')
                    ->select('bookings.*')
                    ->where('locations.id', request('filter_location'))
                    ->groupBy('bookings.id');
            }

            if(\request('filter_date') != ""){
                $startTime = Carbon::createFromFormat('Y-m-d', request('filter_date'), $this->settings->timezone)->setTimezone('UTC')->startOfDay();
                $endTime = $startTime->copy()->addDay()->subSecond();

                $bookings->whereBetween('bookings.date_time', [$startTime, $endTime]);
            }

            if(\request('filter_booking') != ""){
                if(request()->filter_booking=='deal'){
                    $bookings->where('deal_id', '<>' , '');
                }
                else{
                    $bookings->where('deal_id', null);
                }
            }

            if(!$this->user->is_admin && !$this->user->can('create_booking')){
                ($this->user->is_employee) ? $bookings->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $bookings->where('bookings.user_id', $this->user->id);
            }

            $bookings->get();

            return \datatables()->of($bookings)
                ->editColumn('id', function ($row) {
                    $view = view('admin.booking.list_view', compact('row'))->render();
                    return $view;
                })
                ->rawColumns(['id'])
                ->toJson();
        }

        $customers = User::all();
        $locations = Location::all();
        $status = \request('status');

        return view('admin.booking.index', compact('customers', 'status', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $booking = Booking::with(['coupon', 'users'])->find($id);

        $commonCondition = $booking->payment_status == 'pending' && $booking->status != 'canceled' && $this->credentials->show_payment_options == 'show' && !$this->user->is_admin && !$this->user->is_employee;

        $view = view('admin.booking.show', compact('booking', 'commonCondition'))->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        abort_if(!$this->user->can('update_booking'), 403);

        $selected_booking_user = array();
        $booking_users = Booking::with(['users'])->find($booking->id);
        foreach ($booking_users->users as $key => $user)
        {
            array_push($selected_booking_user, $user->id);
        }

        $tax = TaxSetting::active()->first();
        $employees = User::OtherThanCustomers()->get();
        $businessServices = BusinessService::active()->get();
        $view = view('admin.booking.edit', compact('booking', 'tax', 'businessServices', 'employees', 'selected_booking_user'))->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBooking $request, $id)
    {
        abort_if(!$this->user->can('update_booking'), 403);

        //delete old items and enter new booking_date
        BookingItem::where('booking_id', $id)->delete();

        $employees       = $request->employee_id;
        $services = $request->cart_services;
        $quantity = $request->cart_quantity;
        $prices = $request->cart_prices;
        $discount = $request->cart_discount;
        $payment_status = $request->payment_status;
        $discountAmount = 0;
        $amountToPay = 0;

        $originalAmount = 0;
        $bookingItems = array();

        foreach ($services as $key=>$service){
            $amount = ($quantity[$key] * $prices[$key]);

            $bookingItems[] = [
                "business_service_id" => $service,
                "quantity" => $quantity[$key],
                "unit_price" => $prices[$key],
                "amount" => $amount
            ];

            $originalAmount = ($originalAmount + $amount);
        }

        $amountToPay = $originalAmount;

        $booking = Booking::with('payment')->where('id', $id)->first();

        $taxAmount = 0;

        if($discount > 0){
            if($discount > 100) $discount = 100;

            $discountAmount = (($discount/100) * $originalAmount);
            $amountToPay = ($originalAmount - $discountAmount);
        }

        if($booking->tax_name){
            $taxAmount = $amountToPay * $booking->tax_percent / 100;
            $booking->tax_amount = $taxAmount;
        }

        $amountToPay = ($amountToPay + $taxAmount);

        if (!is_null($request->coupon_id)) {
            $amountToPay -= $request->coupon_amount;
        }

        $amountToPay = round($amountToPay, 2);

        $booking->date_time   = Carbon::createFromFormat('Y-m-d H:i a', $request->booking_date . ' ' . $request->hidden_booking_time)->format('Y-m-d H:i:s');
        $booking->status      = $request->status;
        $booking->original_amount = $originalAmount;
        $booking->discount = $discountAmount;
        $booking->discount_percent = $request->cart_discount;;
        $booking->amount_to_pay = $amountToPay;
        $booking->payment_status = $payment_status;

        $booking->save();

        /* assign employees to this appointment */
        if(!empty($employees))
        {
            $assignedEmployee   = array();
            foreach ($employees as $key=>$employee)
            {
                $assignedEmployee[] = $employees[$key];
            }
            $booking = Booking::find($id);
            $booking->users()->sync($assignedEmployee);
        }

        $total_amount = 0.00;
        foreach ($bookingItems as $key=>$bookingItem){
            $bookingItems[$key]['booking_id'] = $booking->id;
            $total_amount += $bookingItem['amount'];
        }
        $total_amount = round($total_amount, 2);

        if (!$booking->payment) {
            $payment = new Payment();

            $payment->currency_id = $this->settings->currency_id;
            $payment->booking_id = $booking->id;
            $payment->amount = $total_amount;
            $payment->gateway = 'cash';
            $payment->status = $payment_status;
            $payment->paid_on = Carbon::now();
        }
        else {
            $payment = $booking->payment;
            $payment->status = $payment_status;
            $payment->amount = $total_amount;
        }

        $payment->save();

        DB::table('booking_items')->insert($bookingItems);

        $commonCondition = $booking->payment_status == 'pending' && $booking->status != 'canceled' && $this->credentials->show_payment_options == 'show' && !$this->user->is_admin && !$this->user->is_employee;

        $completedBookings = Booking::where('user_id', $booking->user_id)->where('status', 'completed')->count();
        $approvedBookings = Booking::where('user_id', $booking->user_id)->where('status', 'approved')->count();
        $pendingBookings = Booking::where('user_id', $booking->user_id)->where('status', 'pending')->count();
        $canceledBookings = Booking::where('user_id', $booking->user_id)->where('status', 'canceled')->count();
        $inProgressBookings = Booking::where('user_id', $booking->user_id)->where('status', 'in progress')->count();
        $earning = Booking::where('user_id', $booking->user_id)->where('status', 'completed')->sum('amount_to_pay');

        $view = view('admin.booking.show', compact('booking', 'commonCondition'))->render();

        $customerStatsView = view('partials.customer_stats', compact('completedBookings', 'approvedBookings', 'pendingBookings', 'inProgressBookings', 'canceledBookings', 'earning'))->render();

        return Reply::successWithData('messages.updatedSuccessfully', ['status' => 'success', 'view' => $view, 'customerStatsView' => $customerStatsView]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_booking'), 403);

        Booking::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function download($id) {
        $booking = Booking::findOrFail($id);

        if($booking->status != 'completed')
        {
            abort(403);
        }

        if($this->user->is_admin || $this->user->is_employee || $booking->user_id == $this->user->id){
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.booking.receipt',compact('booking') );
            $filename = __('app.receipt').' #'.$booking->id;
//       return $pdf->stream();
            return $pdf->download($filename . '.pdf');
        }
        else{
            abort(403);
        }
    }

    public function requestCancel($id){
        $booking = Booking::findOrFail($id);
        $booking->status = 'canceled';
        $booking->save();

        $commonCondition = $booking->payment_status == 'pending' && $booking->status != 'canceled' && $this->credentials->show_payment_options == 'show' && !$this->user->is_admin && !$this->user->is_employee;
        $view = view('admin.booking.show', compact('booking', 'commonCondition'))->render();

        $admins = User::allAdministrators()->get();

        Notification::send($admins, new BookingCancel($booking));

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function sendReminder(){
        $bookingId = \request('bookingId');
        $booking = Booking::findOrFail($bookingId);
        $customer = User::findOrFail($booking->user_id);
        $customer->notify(new BookingReminder($booking));

        return Reply::success(__('messages.bookingReminderSent'));
    }

    public function multiStatusUpdate(BookingStatusMultiUpdate $request) {
        // Booking::whereIn('id', $request->booking_checkboxes)->forceUpdate([
        //     'status' => $request->change_status
        // ]);

        foreach ($request->booking_checkboxes as $key => $booking_checkbox)
        {
            $booking = Booking::find($booking_checkbox);
            $booking->status = $request->change_status;
            $booking->save();
        }

        // $bookings = Booking::find($request->booking_checkboxes);
        // $bookings->map(function ($booking, $key) use ($request){
        //     $booking->status = $request->change_status;
        // });

        return Reply::dataOnly(['status' => 'success', '']);
    }

    public function updateCoupon(Request $request)
    {
        $couponId = $request->coupon_id;

        $tax = TaxSetting::active()->first();

        $productAmount = $request->cart_services;

        if($request->cart_discount > 0){
            $totalDiscount = ($request->cart_discount / 100) * $productAmount;
            $productAmount -= $totalDiscount;
        }

        $percentAmount = ($tax->percent / 100) * $productAmount;

        $totalAmount   = ($productAmount + $percentAmount);

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $couponData = Coupon::where('coupons.start_date_time', '<=', $currentDate)
            ->where(function ($query) use($currentDate) {
                $query->whereNull('coupons.end_date_time')
                    ->orWhere('coupons.end_date_time', '>=', $currentDate);
            })
            ->where('coupons.id', $couponId)
            ->where('coupons.status', 'active')
            ->first();

        if (!is_null($couponData)  && $couponData->minimum_purchase_amount != 0 && $couponData->minimum_purchase_amount != null && $productAmount < $couponData->minimum_purchase_amount)
        {
            return Reply::errorWithoutMessage();
        }

        if (!is_null($couponData) && $couponData->used_time >= $couponData->uses_limit && $couponData->uses_limit != null && $couponData->uses_limit != 0) {
            return Reply::errorWithoutMessage();
        }

        if (!is_null($couponData)) {
            $days = json_decode($couponData->days);
            $currentDay = Carbon::now()->format('l');
            if (in_array($currentDay, $days)) {
                if (!is_null($couponData->percent) && $couponData->percent != 0) {
                    $percentAmnt = round(($couponData->percent / 100) * $totalAmount, 2);
                    if (!is_null($couponData->amount) && $percentAmnt >= $couponData->amount) {
                        $percentAmnt = $couponData->amount;
                    }
                    return Reply::dataOnly( ['amount' => $percentAmnt, 'couponData' => $couponData]);
                } elseif (!is_null($couponData->amount) && (is_null($couponData->percent) || $couponData->percent == 0)) {
                    return Reply::dataOnly(['amount' => $couponData->amount, 'couponData' => $couponData]);
                }
            } else {
                return Reply::errorWithoutMessage();
            }
        }
        return Reply::errorWithoutMessage();
    }

}
