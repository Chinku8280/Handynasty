<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Reply;
use App\Booking;
use App\BusinessService;
use App\Category;
use App\Location;
use Illuminate\Support\Facades\DB;
use App\Payment;
use App\Role;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Arr;

class ReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.reports'));
    }

    public function index() {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $labels = [
            'Today' => 'today',
            'Yesterday' => 'yesterday',
            'Last 7 Days' => 'lastWeek',
            'Last 30 Days' => 'lastThirtyDays',
            'This Month' => 'thisMonth',
            'Last Month' => 'lastMonth'
        ];

        $customers = User::all();
        $locations = Location::all();
        $staffs = User::select('id', 'name')->with('roles')->whereHas('roles', function($q){
            $q->where('name', '<>', 'customer');
        })->get();
        $status = \request('status');
        $services = BusinessService::select('name')->groupBy('name')->get();

        return view('admin.report.layout', compact(['labels', 'customers', 'staffs', 'services', 'status','locations']));
    }

    public function earningReportChart(Request $request) {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $payments = Payment::where('status', 'completed', 'booking')
            ->whereDate('paid_on', '>=', Carbon::createFromFormat('Y-m-d', $request->startDate))
            ->whereDate('paid_on', '<=', Carbon::createFromFormat('Y-m-d', $request->endDate))
            ->whereHas('booking.items.businessService.location', function($query) use($request){
                $query->where('id', $request->location);
            })
            ->groupBy('year', 'month')
            ->orderBy('amount', 'ASC')
            ->get(
                [
                    DB::raw('DATE_FORMAT(paid_on,"%D-%M-%Y") as pay_date'),
                    DB::raw('DATE_FORMAT(paid_on,"%M/%y") as date'),
                    DB::raw('YEAR(paid_on) year, MONTH(paid_on) month'),
                    DB::raw('sum(amount) as total')
                ]
            );

        $graphData = [];
            foreach($payments as $key2=>$payment){
                $payments[$key2]->total = $payment->total;
                $graphData[] = $payment;
            }

        usort(
            $graphData, function ($a, $b) {
                $t1 = strtotime($a->pay_date);
                $t2 = strtotime($b->pay_date);
                return $t1 - $t2;
            }
        );

        $labels = [];
        foreach($graphData as $gData){
            $labels[] = $gData->date;
        }

        $earnings = [];
        foreach($graphData as $gData){
            $earnings[] = round($gData->total, 2);
        }

        return Reply::dataOnly(['labels' => $labels, 'data' => $earnings, 'status' => 'success']);
    }

    public function earningTable(Request $request){
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $bookings = Booking::where('payment_status', 'completed')
            ->with('completedPayment', 'items')
            ->whereHas('completedPayment', function ($query) use ($request) {
                $query->whereDate('paid_on', '>=', Carbon::createFromFormat('Y-m-d', $request->startDate))
                ->whereDate('paid_on', '<=', Carbon::createFromFormat('Y-m-d', $request->endDate));
            })
            ->whereHas('items.businessService.location', function($query) use($request){
                $query->where('id', $request->location);
            })
            ->get();

        return \datatables()->of($bookings)
            ->editColumn('user_id', function ($row) {
                return ucwords($row->user->name);
            })
            ->editColumn('amount_to_pay', function ($row) {
                return $this->settings->currency->currency_symbol.number_format((float)$row->amount_to_pay, 2, '.', '');
            })
            ->editColumn('date_time', function ($row) {
                return $row->completedPayment->paid_on->translatedFormat($this->settings->date_format);
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'image', 'status'])
            ->toJson();
    }

    public function salesReportChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $labels = [];
        $sales = [];
        $servicesArr = [];

        $services = BusinessService::with('location')
        ->whereHas('location', function($q) use($request){
            $q->where('id', $request->location);
        })->select('id', 'slug', 'name')->orderBy('name')->get();

        $bookings = Booking::with('items')->whereHas('items.businessService.location', function($query) use($request){
            $query->where('id', $request->location);
        })->whereMonth('date_time', $request->month)->whereYear('date_time', $request->year)->where('payment_status', 'completed')->get();

        foreach ($services as $service) {
            $servicesArr = Arr::add($servicesArr, $service->id, ['name' => $service->name, 'sales' => 0]);
            $labels[] = $service->name;
        }

        foreach ($bookings as $booking) {
            foreach ($booking->items as $item) {
                if (isset($servicesArr[$item->business_service_id])) {
                    $servicesArr[$item->business_service_id]['sales'] += $item->quantity;
                }
                
            }
        }

        foreach ($servicesArr as $service) {
            $sales[] = $service['sales'];
        }

        return Reply::dataOnly(['labels' => $labels, 'data' => $sales, 'status' => 'success']);
    }

    public function salesTable(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $services = BusinessService::with('bookingItems', 'bookingItems.booking', 'location')
        ->whereHas('bookingItems.booking', function ($q) use ($request) {
            $q->whereMonth('date_time', $request->month)
            ->whereYear('date_time', $request->year)
            ->where('payment_status', 'completed');
        })
        ->whereHas('location', function($q) use($request){
            $q->where('id', $request->location);
        })
        ->orderBy('name')
        ->get();

        // make booking items collection
        $items = [];
        foreach ($services as $service) {
            $bookingItems = $service->bookingItems()->whereHas('booking', function ($q) use ($request) {
                $q->whereMonth('date_time', $request->month)
                ->whereYear('date_time', $request->year)
                ->where('payment_status', 'completed');
            })->get();

            foreach ($bookingItems as $bookingItem) {
                $items[] = $bookingItem;
            }
        }

        return \datatables()->of(collect($items))
            ->editColumn('service_name', function ($row) {
                return ucwords($row->businessService->name);
            })
            ->editColumn('customer_name', function ($row) {
                return $row->booking->user->name;
            })
            ->editColumn('sales', function ($row) {
                return $row->quantity;
            })
            ->editColumn('amount', function ($row) {
                $taxAmount = $row->booking->tax_percent ? ($row->quantity * $row->unit_price * $row->booking->tax_percent / 100) : 0;
                $discountAmount = ($row->quantity * $row->unit_price * $row->booking->discount_percent / 100);

                $finalAmount = ($row->quantity * $row->unit_price) + $taxAmount - $discountAmount;
                return $this->settings->currency->currency_symbol.number_format((float) $finalAmount, 2, '.', '');
            })
            ->editColumn('paid_on', function ($row) {
                return $row->booking->completedPayment->paid_on->translatedFormat($this->settings->date_format);
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'image', 'status'])
            ->toJson();
    }

    public function tabularTable(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        // dd($request->all());


        $items = [];
        $bookings = Booking::with('users', 'items', 'user', 'payment');

        if($request->from_date && $request->to_date){
            $bookings->whereDate('date_time', '>=', $request->from_date)->whereDate('date_time', '<=', $request->to_date);
        }
        if($request->customer_id){
            $bookings->where('user_id', $request->customer_id);
        }
        if($request->service_name){
            $bookings->whereHas('items.businessService', function ($q) use ($request) {
                $q->where('name', $request->service_name);
            });
        }
        if($request->employee_id){
            $bookings->whereHas('users', function ($q) use ($request) {
                $q->where('id', $request->employee_id);
            });
        }
        if($request->booking_status){
            $bookings->where('status', $request->booking_status);
        }
        if($request->booking_type){
            if($request->booking_type=='deal'){
                $bookings->where('deal_id', '<>' , '');
            }
            else{
                $bookings->where('deal_id', null);
            }
        }
        if($request->location){
            $bookings->whereHas('items.businessService.location', function($query) use($request){
                $query->where('id', $request->location);
            });
        }
        if ($request->payment){
            $bookings->where('payment_status', $request->payment);
        }

        $bookings = $bookings->orderBy('id', 'desc')->get();
        $total=0;
        foreach ($bookings as $booking){
            $total += $booking->amount_to_pay;
            $items[] = $booking;
        }

        return \datatables()->of(collect($items))
            ->editColumn('service_name', function ($row) {
                $booking_items = '<ol>';
                foreach($row->items as $item)
                {
                    $booking_items .= '<li>'.$item->businessService->name.' <b> X'.$item->quantity.'</b></li>';
                }
                $booking_items .= '</ol>';
                return $booking_items;
            })
            ->editColumn('customer_name', function ($row) {
                return '<i class="icon-user"></i> '.$row->user->name;
            })
            ->editColumn('employee_name', function ($row) {
                $booking_users = '';
                foreach($row->users as $user){
                    $booking_users .= '<i class="icon-user"></i> '. ucfirst($user->name).' &nbsp;&nbsp;';
                }
                if($booking_users==''){ return __('app.notAvailable'); }
                return $booking_users;
            })
            ->editColumn('amount', function ($row) {

                $status = $row->payment!=null ? $row->payment->status : '';
                if($status=='completed'){
                    return '<i data-toggle="tooltip" data-original-title="'.__('app.payment')." ".__('app.done').'" style="color:green" class="fa fa-check-square"></i> '.$this->settings->currency->currency_symbol.$row->amount_to_pay;
                }
                else{
                    return ' <i data-toggle="tooltip" data-original-title="'.__('app.payment')." ".__('app.pending').'" style="color:red" class="fa fa-times-circle"></i> '.$this->settings->currency->currency_symbol.$row->amount_to_pay;
                }
            })
            ->editColumn('booking_time', function ($row) {
                if($this->validateDate($row->date_time)){
                    return $row->date_time->translatedFormat($this->settings->time_format);
                }
                return __('app.notAvailable');
            })
            ->editColumn('booking_source', function ($row) {
                return $row->source;
            })
            ->editColumn('booking_status', function ($row) {

                $style ='';
                if($row->status=='approved'){
                    $style = 'border-info text-info';
                }
                if($row->status=='approved'){
                    $style = 'border-info text-info';
                }
                elseif($row->status=='completed'){
                    $style = 'border-success text-success';
                }
                elseif($row->status=='pending'){
                    $style = 'border-warning text-warning';
                }
                elseif($row->status=='in progress'){
                    $style = 'border-primary text-primary';
                }
                else{
                    $style = 'border-danger text-danger';
                }
                return '<span class="text-uppercase small border badge-pill '.$style.'">'.$row->status.'</span>';
            })
            ->editColumn('booking_date', function ($row) {
                if($this->validateDate($row->date_time)){
                    return $row->date_time->translatedFormat($this->settings->date_format);
                }
                return __('app.notAvailable');
            })
            ->addIndexColumn()
            ->rawColumns(['employee_name', 'customer_name', 'booking_status', 'service_name', 'amount'])
            ->with('sums', $this->settings->currency->currency_symbol.$total)
            ->make(true);
    }

    public function userTypeChart()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Role::withoutGlobalScopes()
        ->withCount('users')
        ->groupBy('display_name')
        ->get();

        foreach($record as $row)
        {
            $data['label'][] = $row->display_name;
            $data['data'][] = $row->users_count;
        }
        return Reply::dataOnly(['data' => $data]);

    }

    public function serviceTypeChart()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Category::withCount('services')->get();
        foreach($record as $row)
        {
            $data['label'][] = $row->name;
            $data['data'][] = (int) $row->services_count;
        }
        return Reply::dataOnly(['data' => $data]);

    }

    public function bookingSourceChart()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Booking::groupBy('source')
                ->get([
                    'source as booking_source',
                    DB::raw('(select count(bookings.source) from `bookings` where source=booking_source) as countSource')
                ]);

        foreach($record as $row)
        {
            $data['label'][] = $row->booking_source;
            $data['data'][] = $row->countSource;
        }

        return Reply::dataOnly(['data' => $data]);

    }

    public function bookingPerDayChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Booking::select('id', 'date_time', 'source', DB::raw('count(*) as total'))->whereDate('date_time', Carbon::createFromFormat('Y-m-d', $request->booking_date))
        ->groupBy('source')
        ->get();

        foreach($record as $row)
        {
            $data['label'][] = $row->source;
            $data['data'][] = (int) $row->total;
        }
        return Reply::dataOnly(['data' => $data]);

    }

    public function bookingPerMonthChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];
        $check_month = [];
        $day_array = [];
        $no_of_days = Carbon::createFromFormat('Y-m', $request->booking_month)->daysInMonth;

        for ($i=1; $i <= $no_of_days ; $i++)
        {
            array_push($day_array, $i);
        }

        $record = Booking::select('id','date_time', DB::raw('DATE(date_time) as date'), DB::raw('count(*) as total'))
        ->whereMonth('date_time', Carbon::createFromFormat('Y-m', $request->booking_month))
        ->whereYear('date_time', Carbon::createFromFormat('Y-m', $request->booking_month))
        ->groupBy('date')
        ->get();

        foreach ($day_array as $key1 => $value)
        {
            /* if month is availble in table */
            foreach($record as $key2 => $row)
            {
                if(in_array(Carbon::parse($row->date)->format('d'), $day_array) && $day_array[$key1]==Carbon::parse($row->date)->format('d'))
                {
                    array_push($check_month, Carbon::parse($row->date)->format('d'));
                    $data['label'][] = $day_array[$key1];
                    $data['data'][] = (int) $row->total;
                }
            }
            /* if month is not availble in table */
            if(in_array($day_array[$key1], $day_array) && !in_array($day_array[$key1], $check_month))
            {
                $data['label'][] = $day_array[$key1];
                $data['data'][] = 0;

            }
        }
        return Reply::dataOnly(['data' => $data]);
    }

    public function bookingPerYearChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];
        $check_month = [];
        $months_array = [1,2,3,4,5,6,7,8,9,10,11,12];

        $record = Booking::whereYear('date_time', $request->booking_year)
        ->groupBy('year','month')
        ->get(
            [
                DB::raw('COUNT(id) as `total_bookings`'),
                DB::raw('YEAR(date_time) year, MONTH(date_time) month')
            ]
        );

        foreach ($months_array as $key1 => $value)
        {
            /* if month is available on table */
            foreach($record as $key2 => $row)
            {
                if(in_array($row->month, $months_array) && $row->month==$months_array[$key1])
                {
                    array_push($check_month, $row->month);
                    $data['label'][] = DateTime::createFromFormat('m', $row->month)->format('M');
                    $data['data'][] = (int) $row->total_bookings;
                }
            }
            /* if month is not available on table */
            if(in_array($months_array[$key1], $months_array) && !in_array($months_array[$key1], $check_month))
            {
                $data['label'][] = DateTime::createFromFormat('m', $months_array[$key1])->format('M');
                $data['data'][] = 0;
            }
        }
        return Reply::dataOnly(['data' => $data]);
    }

    public function paymentPerDayChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Payment::select('id', 'paid_on', 'gateway', DB::raw('sum(amount) as total'))
        ->where('status', 'completed')
        ->whereDate('paid_on', Carbon::createFromFormat('Y-m-d', $request->payment_date))
        ->groupBy('gateway')->get();

        foreach($record as $row)
        {
            $data['label'][] = $row->gateway;
            $data['data'][] = (int) $row->total;
        }
        return Reply::dataOnly(['data' => $data]);
    }

    public function paymentPerMonthChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];
        $check_month = [];
        $no_of_days = Carbon::createFromFormat('Y-m', $request->payment_month)->daysInMonth;
        $day_array = [];

        for ($i=1; $i<=$no_of_days; $i++)
        {
            array_push($day_array, $i);
        }

        $record = Payment::select('paid_on', DB::raw('DATE(paid_on) as date'),  DB::raw('sum(amount) as total'))
        ->where('status', 'completed')
        ->whereMonth('paid_on', Carbon::createFromFormat('Y-m', $request->payment_month))
        ->whereYear('paid_on', Carbon::createFromFormat('Y-m', $request->payment_month))
        ->groupBy('date')
        ->get();

        foreach ($day_array as $key1 => $value)
        {
            /* if day is available in table */
            foreach($record as $key2 => $row)
            {

                if(in_array(Carbon::parse($row->paid_on)->format('d'), $day_array) && $day_array[$key1]==Carbon::parse($row->paid_on)->format('d'))
                {
                    array_push($check_month, Carbon::parse($row->paid_on)->format('d'));
                    $data['label'][] = $day_array[$key1];
                    $data['data'][] = (int) $row->total;
                }
            }
            /* if day is not available in table */
            if(in_array($day_array[$key1], $day_array) && !in_array($day_array[$key1], $check_month))
            {
                $data['label'][] = $day_array[$key1];
                $data['data'][] = 0;

            }
        }
        return Reply::dataOnly(['data' => $data]);

    }

    public function paymentPerYearChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];
        $check_month = [];
        $months_array = [1,2,3,4,5,6,7,8,9,10,11,12];

        $record = Payment::whereYear('paid_on', $request->payment_year)
        ->where('status', 'completed')
        ->groupBy('year','month')
        ->get(
            [
                DB::raw('SUM(amount) as `total_amount`'),
                DB::raw('YEAR(paid_on) year, MONTH(paid_on) month')
            ]
        );

        foreach ($months_array as $key1 => $value)
        {
            /* if month is availble on table */
            foreach($record as $key2 => $row)
            {
                if(in_array($row->month, $months_array) && $row->month==$months_array[$key1])
                {
                    array_push($check_month, $row->month);
                    $data['label'][] = DateTime::createFromFormat('m', $row->month)->format('M');
                    $data['data'][] = (int) $row->total_amount;
                }
            }
            /* if month is not available in table */
            if(in_array($months_array[$key1], $months_array) && !in_array($months_array[$key1], $check_month))
            {
                $data['label'][] = DateTime::createFromFormat('m', $months_array[$key1])->format('M');
                $data['data'][] = 0;
            }
        }
        return Reply::dataOnly(['data' => $data]);
    }

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

} /* end of class */
