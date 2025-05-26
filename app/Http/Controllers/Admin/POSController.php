<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\BusinessService;
use App\Category;
use App\Coupon;
use App\Helper\Reply;
use App\Http\Requests\Pos\StorePos;
use App\Location;
use App\TaxSetting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Payment;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.pos'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $services = BusinessService::active()->get();
        $categories = Category::active()
            ->with(['services' => function ($query) {
                $query->active();
            }])->get();
        $locations = Location::all();
        $tax = TaxSetting::active()->first();
        $employees = User::OtherThanCustomers()->get();

        return view('admin.pos.create', compact('services', 'categories', 'locations', 'tax', 'employees'));
    }

    /**
     * @param StorePos $request
     * @return array
     */
    public function store(StorePos $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $dateTime      = Carbon::createFromFormat('Y-m-d H:i a', $request->pos_date.' '.$request->pos_time)->format('Y-m-d H:i:s');
        
        // edited at is newer than created at
        $tax = TaxSetting::active()->first();

        $services       = $request->cart_services;
        $quantity       = $request->cart_quantity;
        $prices         = $request->cart_prices;
        $discount       = $request->cart_discount;
        $cart_discount  = $request->cart_discount;
        $taxAmount      = 0;
        $discountAmount = 0;
        $amountToPay    = 0;
        $originalAmount = 0;
        $bookingItems   = array();
        $employees      = $request->employee;

        if($cart_discount==''){ $cart_discount = 0; }

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
        if($discount > 0){
            if($discount > 100) $discount = 100;

            $discountAmount = (($discount/100) * $originalAmount);

            $amountToPay = ($originalAmount - $discountAmount);
        }

        if(!is_null($tax) && $tax->percent > 0){
            $taxAmount = (($tax->percent/100) * $amountToPay);
        }

        $amountToPay = ($amountToPay + $taxAmount);

        if (!is_null($request->coupon_id)) {
            $amountToPay -= $request->coupon_amount;
        }

        $amountToPay = round($amountToPay, 2);

        $booking = new Booking();
        $booking->user_id          = $request->user_id;
        $booking->date_time        = $dateTime;
        $booking->status           = 'approved';
        $booking->payment_gateway  = $request->payment_gateway;
        $booking->original_amount  = $originalAmount;
        $booking->discount         = $discountAmount;
        $booking->discount_percent = $cart_discount;
        $booking->payment_status   = 'completed';

        if(!is_null($tax) && $tax->percent > 0) {
            $booking->tax_name    = $tax->tax_name;
            $booking->tax_percent = $tax->percent;
            $booking->tax_amount  = $taxAmount;
        }
        // Coupon Details added
        if (!is_null($request->coupon_id)) {
            $booking->coupon_id       = $request->coupon_id;
            $booking->coupon_discount = $request->coupon_amount;

            $coupon = Coupon::findOrFail($request->coupon_id);
            $coupon->used_time = ($coupon->used_time + 1);
            $coupon->save();
        }
        $booking->amount_to_pay = $amountToPay;
        $booking->save();

        foreach ($bookingItems as $key=>$bookingItem){
            $bookingItems[$key]['booking_id'] = $booking->id;
        }

        DB::table('booking_items')->insert($bookingItems);

        /* assign employees to this appointment */
        if($employees)
        {
            $assignedEmployee   = array();
            foreach ($employees as $key=>$employee)
            {
                $assignedEmployee[] = $employees[$key];
            }
            $booking->users()->attach($assignedEmployee);
        }

        // create payment
        $payment = new Payment();
        $payment->currency_id = $this->settings->currency_id;
        $payment->booking_id  = $booking->id;
        $payment->amount      = $booking->amount_to_pay;
        $payment->gateway     = $booking->payment_gateway;
        $payment->status      = $booking->payment_status;
        $payment->paid_on     = $booking->utc_date_time;

        $payment->save();

        return Reply::redirect(route('admin.bookings.index'), __('messages.createdSuccessfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function selectCustomer(){
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        return view('admin.pos.select_customer');
    }

    public function searchCustomer(Request $request){
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $searchTerm = $request->q;
        $users = User::where('name', 'like', $searchTerm.'%')
            ->orWhere('mobile', 'like', '%'.$searchTerm.'%')
            ->orWhere('email', 'like', '%'.$searchTerm.'%')
            ->get();

        $items = [];
        foreach ($users as $user){
            $items[] = ['id'=>$user->id, 'full_name' => $user->name, 'email' => $user->email, 'mobile' => $user->formatted_mobile];
        }

        $json = [
            'total_count' => count($users),
            'incomplete_results' => false,
            'items' => $items
        ];

        return json_encode($json);
//        return view('admin.pos.select_customer');
    }

    public function addCart(){

    }

    public function filterServices(Request $request) {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        if ($request->category_id !== '0') {
            $categories = Category::where('id', $request->category_id)
                ->active()
                ->with([
                    'services' => function($query) use($request) {
                        if ($request->location_id !== '0') {
                            $query->active()->where('location_id', $request->location_id);
                        }
                        else {
                            $query->active();
                        }
                    }
                ])->get();
        }
        else {
            $categories = Category::active()
                ->with([
                    'services' => function($query) use($request) {
                        if ($request->location_id !== '0') {
                            $query->active()->where('location_id', $request->location_id);
                        }
                        else {
                            $query->active();
                        }
                    }
                ])->get();
        }

        $view = view('admin.pos.filtered_services', compact('categories'))->render();

        return Reply::dataOnly(['view' => $view]);
    }

    /**
     * @param Request $request
     * @return $this|array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function applyCoupon(Request $request)
    {
        $couponTitle = strtolower($request->coupon);
        $products    = $request->cart_services;
        $tax         = TaxSetting::active()->first();

        $productAmount = 0;

        foreach ($products as $key => $product)
        {
            $productData = BusinessService::findOrFail($product[0]);
            if($productData->discount_type == 'percent'){
                $percentPrice = ($productData->discount / 100) * $productData->price;
            }else{
                $percentPrice = $productData->discount;
            }

            $productAmount += ($productData->price - $percentPrice) * $product[1];
        }


        if($request->cart_discount > 0){
            $totalDiscount = ($request->cart_discount / 100) * $productAmount;
            $discountProductAmount = ($productAmount-$totalDiscount);
        }
        else{
            $discountProductAmount = $productAmount;
        }

        $percentAmount = ($tax->percent / 100) * $discountProductAmount;
        $totalAmount   = ($discountProductAmount + $percentAmount);

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $couponData = Coupon::where('coupons.start_date_time', '<=', $currentDate)
            ->where(function ($query) use($currentDate) {
                $query->whereNull('coupons.end_date_time')
                    ->orWhere('coupons.end_date_time', '>=', $currentDate);
            })
            ->where('coupons.title', $couponTitle)
            ->where('coupons.status', 'active')
            ->first();

        if (!is_null($couponData)  && $couponData->minimum_purchase_amount != 0 && $couponData->minimum_purchase_amount != null && $productAmount < $couponData->minimum_purchase_amount)
        {
            return Reply::error(__('messages.coupon.minimumAmount').' '.$this->settings->currency->currency_symbol.$couponData->minimum_purchase_amount);
        }

        if (!is_null($couponData) && $couponData->used_time >= $couponData->uses_limit && $couponData->uses_limit != null && $couponData->uses_limit != 0) {
            return Reply::error(__('messages.coupon.usedMaximun'));
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
                    return response(Reply::successWithData(__('messages.coupon.couponApplied'), ['amount' => $percentAmnt, 'couponData' => $couponData]));
                } elseif (!is_null($couponData->amount) && (is_null($couponData->percent) || $couponData->percent == 0)) {
                    return response(Reply::successWithData(__('messages.coupon.couponApplied'), ['amount' => $couponData->amount, 'couponData' => $couponData]));
                }
            } else {
                return response(Reply::error(__('messages.coupon.notMatched')));
            }
        }
        return Reply::error(__('messages.coupon.notMatched'));

    }


    /**
     * @param Request $request
     * @return $this|array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateCoupon(Request $request)
    {
        $couponTitle = strtolower($request->coupon);
        $products    = $request->cart_services;
        $tax         = TaxSetting::active()->first();

        $productAmount = 0;

        foreach ($products as $key => $product)
        {
            $productData = BusinessService::findOrFail($product[0]);
            if($productData->discount_type == 'percent'){
                $percentPrice = ($productData->discount / 100) * $productData->price;
            }else{
                $percentPrice = $productData->discount;
            }

            $productAmount += ($productData->price - $percentPrice) * $product[1];
        }


       if($request->cart_discount > 0){
           $totalDiscount = ($request->cart_discount / 100) * $productAmount;
            $discountProductAmount = ($productAmount - $totalDiscount);
        }
        else{
            $discountProductAmount = $productAmount;
        }

        $percentAmount = ($tax->percent / 100) * $discountProductAmount;

        $totalAmount   = ($discountProductAmount + $percentAmount);

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $couponData = Coupon::where('coupons.start_date_time', '<=', $currentDate)
            ->where(function ($query) use($currentDate) {
                $query->whereNull('coupons.end_date_time')
                    ->orWhere('coupons.end_date_time', '>=', $currentDate);
            })
            ->where('coupons.title', $couponTitle)
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
                    return response(Reply::dataOnly( ['amount' => $percentAmnt, 'couponData' => $couponData]));
                } elseif (!is_null($couponData->amount) && (is_null($couponData->percent) || $couponData->percent == 0)) {
                    return response(Reply::dataOnly(['amount' => $couponData->amount, 'couponData' => $couponData]));
                }
            } else {
                return Reply::errorWithoutMessage();
            }
        }
        return Reply::errorWithoutMessage();
    }
}
