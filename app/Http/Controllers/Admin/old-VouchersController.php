<?php

namespace App\Http\Controllers\Admin;

use App\Location;
use App\BusinessService;
use App\Voucher;
use App\voucherItem;
use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Voucher\StoreRequest;
use App\Http\Requests\Voucher\UpdateRequest;
use App\Outlet;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VouchersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.vouchers'));
    }


    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_voucher'), 403);

        if (request()->ajax()) {
            $vouchers = Voucher::where('voucher_type', 1)->get();

            return datatables()->of($vouchers)
                ->addColumn('action', function ($row) {
                    $action = '';

                    if ($this->user->can('update_voucher')) {
                        $action .= '<a href="' . route('admin.vouchers.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                            data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    $action .= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-voucher"
                        data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a> ';

                    $action .= ' <a href="javascript:;" class="btn btn-success btn-circle send-notification"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="Send Push Notification"><i class="fa fa-bell" aria-hidden="true"></i></a> ';

                    if ($this->user->can('delete_voucher')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . $row->voucher_image_url . '" class="img" height="65em" width="65em"/> ';
                })
                ->editColumn('title', function ($row) {
                    return ucfirst($row->title);
                })
                ->editColumn('start_date_time', function ($row) {
                    return Carbon::parse($row->start_date_time)->translatedFormat($this->settings->date_format . ' ' . $this->settings->time_format);
                })
                ->editColumn('end_date_time', function ($row) {
                    return Carbon::parse($row->end_date_time)->translatedFormat($this->settings->date_format . ' ' . $this->settings->time_format);
                })
                ->editColumn('percentage', function ($row) {
                    if($row->discount_type == "percent")
                    {
                        if(!empty($row->max_discount))
                        {
                            return $row->discount . "% OR maximum amount " . $row->max_discount;
                        }
                        else
                        {
                            return $row->discount . "%";
                        }
                    }
                    else
                    {
                        return $row->discount;
                    }

                    // return $row->percentage;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="badge badge-success">' . __("app.active") . '</label>';
                    } elseif ($row->status == 'inactive') {
                        return '<label class="badge badge-danger">' . __("app.inactive") . '</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'title', 'start_date_time', 'end_date_time', 'percentage', 'status'])
                ->toJson();
        }

        return view('admin.vouchers.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_voucher'), 403);
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $branches  = Location::groupBy('name')->get();

        $outlets = Outlet::where('status', 'active')->get();
        $services = BusinessService::where('status', 'active')->get();
        $customers = User::where('status', 'active')->AllCustomers()->get();

        return view('admin.vouchers.create', compact('days', 'branches', 'outlets', 'services', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        // return $request->all();

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_voucher'), 403);

        if (!$request->has('days'))
        {
            return Reply::error(__('messages.coupon.selectDay'));
        }

        $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->voucher_startDate)->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('Y-m-d H:i a', $request->voucher_endDate)->format('Y-m-d H:i:s');
        $startTime = Carbon::createFromFormat('H:i a', $request->voucher_startTime)->format('H:i:s');
        $endTime  = Carbon::createFromFormat('H:i a', $request->voucher_endTime)->format('H:i:s');

        $voucher = new Voucher();
        $voucher->title                   = $request->title;
        $voucher->slug                    = $request->slug;
        $voucher->start_date_time         = $startDate;
        $voucher->end_date_time           = $endDate;
        $voucher->open_time               = $startTime;
        $voucher->close_time              = $endTime;
        $voucher->max_order_per_customer  = $request->customer_uses_time;
        $voucher->loyalty_point           = $request->loyalty_point ?? 0;
        $voucher->status                  = $request->status;
        $voucher->days                    = json_encode($request->days);
        $cleanDescription = strip_tags($request->input('description'));
        $voucher->description             = $cleanDescription;
        // $voucher->branch                  = $request->branches;
        // $voucher->percentage              = $request->discount;
        $voucher->uses_limit              = $request->uses_time;    

        if ($request->hasFile('feature_image')) {
            $voucher->image = Files::upload($request->feature_image, 'voucher');
        }

        $voucher->discount_type = $request->discount_type;
        $voucher->discount = $request->discount ?? 0;
        $voucher->minimum_purchase_amount = $request->minimum_purchase_amount ?? 0;
        $voucher->max_discount = $request->max_discount ?? 0;
        $voucher->min_age = $request->min_age;
        $voucher->max_age = $request->max_age;

        if($request->filled('is_customer_specific'))
        {
            $voucher->is_customer_specific = 1;
            $voucher->is_welcome = 0;          
        }
        else
        {
            $voucher->is_customer_specific = 0;
            $voucher->is_welcome = $request->has('is_welcome') ? 1 : 0;
        }

        $voucher->save();

        // voucher sevices start

        if($request->filled('services'))
        {
            $new_services_id = $this->store_voucher_services($request, $voucher->id);

            $voucher->service_id = implode(',', $new_services_id);
            $voucher->save();
        }

        // voucher sevices end

        // voucher outlet start

        if($request->filled('outlet_id'))
        {
            $new_outlet_id = $this->store_voucher_outlet($request, $voucher->id);

            $voucher->outlet_id = implode(',', $new_outlet_id);
            $voucher->save();
        }

        // voucher outlet end

        // voucher users start

        if($request->filled('is_customer_specific'))
        {
            if($request->filled('customer_id'))
            {
                $new_customer_id = $this->store_voucher_user($request, $voucher->id);

                $voucher->user_id = implode(',', $new_customer_id);
                $voucher->save();
            }
        }

        // voucher users end

        // voucher gender start

        if($request->filled('gender'))
        {
            $new_gender = $this->store_voucher_gender($request, $voucher->id);

            $voucher->gender = implode(',', $new_gender);
            $voucher->save();
        }

        // voucher gender end

        return Reply::redirect(route('admin.vouchers.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $voucher = Voucher::with('branch')->findOrFail($id);
        // $voucher_items = voucherItem::with('businessService')->where('voucher_id', $id)->get();

        if ($voucher->days) {
            $days = json_decode($voucher->days);
        }
        $branchs = $voucher->branch;

        return view('admin.vouchers.show', compact('voucher', 'days', 'branchs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_voucher'), 403);
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $selectedbranchs = [];
        $voucher = Voucher::findOrFail($id);
        $selectedDays = json_decode($voucher->days);

        // $services = BusinessService::all();
        // $voucher_services = voucherItem::where('voucher_id', $id)->pluck('business_service_id')->toArray();
        // $voucher_items = voucherItem::with('businessService', 'voucher')->where('voucher_id', $id)->get();
        // $voucher_items_table = view('admin.vouchers.voucher_items_edit', compact('voucher_items'))->render();

        $outlets = Outlet::where('status', 'active')->get();
        $services = BusinessService::where('status', 'active')->get();
        $customers = User::where('status', 'active')->AllCustomers()->get();
       
        $selectedOutlets = explode(",", $voucher->outlet_id);
        $selectedServices = explode(",", $voucher->service_id);
        $selectedCustomers = explode(",", $voucher->user_id);
        $selectedCustomersGender = explode(",", $voucher->gender);

        return view('admin.vouchers.edit', compact('days', 'voucher', 'selectedDays', 'services','outlets','customers', 'selectedOutlets', 'selectedServices', 'selectedCustomers', 'selectedCustomersGender'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        // return $request->all();

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_voucher'), 403);

        if (!$request->has('days')) {
            return Reply::error(__('messages.voucher.selectDay'));
        }

        $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->voucher_startDate)->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('Y-m-d H:i a', $request->voucher_endDate)->format('Y-m-d H:i:s');
        $startTime = Carbon::createFromFormat('H:i a', $request->voucher_startTime)->format('H:i:s');
        $endTime  = Carbon::createFromFormat('H:i a', $request->voucher_endTime)->format('H:i:s');

        $voucher = Voucher::findOrFail($id);
        $voucher->title                   = $request->title;
        $voucher->slug                    = $request->slug;
        $voucher->start_date_time         = $startDate;
        $voucher->end_date_time           = $endDate;
        $voucher->open_time               = $startTime;
        $voucher->close_time              = $endTime;
        $voucher->max_order_per_customer  = $request->customer_uses_time;
        $voucher->loyalty_point           = $request->loyalty_point ?? 0;
        $voucher->status                  = $request->status;
        $voucher->days                    = json_encode($request->days);
        $cleanDescription = strip_tags($request->input('description'));
        $voucher->description             = $cleanDescription;
        // $voucher->percentage              = $request->discount;
        $voucher->uses_limit              = $request->uses_time;

        if ($request->hasFile('feature_image')) {
            $voucher->image = Files::upload($request->feature_image, 'voucher');
        }

        $voucher->discount_type = $request->discount_type;
        $voucher->discount = $request->discount ?? 0;
        $voucher->minimum_purchase_amount = $request->minimum_purchase_amount ?? 0;
        $voucher->max_discount = $request->max_discount ?? 0;
        $voucher->min_age = $request->min_age;
        $voucher->max_age = $request->max_age;

        if($request->filled('is_customer_specific'))
        {
            $voucher->is_customer_specific = 1;
            $voucher->is_welcome = 0;          
        }
        else
        {
            $voucher->is_customer_specific = 0;
            $voucher->is_welcome = $request->has('is_welcome') ? 1 : 0;
        }

        $voucher->save();

        // voucher sevices start

        if($request->filled('services'))
        {
            DB::table('voucher_services')->where('voucher_id', $voucher->id)->delete();

            $new_services_id = $this->store_voucher_services($request, $voucher->id);

            $voucher->service_id = implode(',', $new_services_id);
            $voucher->save();
        }

        // voucher sevices end

        // voucher outlet start

        if($request->filled('outlet_id'))
        {
            DB::table('voucher_outlets')->where('voucher_id', $voucher->id)->delete();
            
            $new_outlet_id = $this->store_voucher_outlet($request, $voucher->id);

            $voucher->outlet_id = implode(',', $new_outlet_id);
            $voucher->save();
        }

        // voucher outlet end

        // voucher users start

        if($request->filled('is_customer_specific'))
        {
            if($request->filled('customer_id'))
            {
                DB::table('voucher_users')->where('voucher_id', $voucher->id)->delete();

                $new_customer_id = $this->store_voucher_user($request, $voucher->id);

                $voucher->user_id = implode(',', $new_customer_id);
                $voucher->save();
            }
        }
        else
        {
            DB::table('voucher_users')->where('voucher_id', $voucher->id)->delete();

            $voucher->user_id = null;
            $voucher->save();
        }

        // voucher users end

        // voucher gender start

        if($request->filled('gender'))
        {
            DB::table('voucher_gender')->where('voucher_id', $voucher->id)->delete();

            $new_gender = $this->store_voucher_gender($request, $voucher->id);

            $voucher->gender = implode(',', $new_gender);
            $voucher->save();
        }

        // voucher gender end

        return Reply::redirect(route('admin.vouchers.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_coupon'), 403);

        $Voucher = Voucher::find($id);

        if($Voucher)
        {
            DB::table('voucher_services')->where('voucher_id', $id)->delete();
            DB::table('voucher_gender')->where('voucher_id', $id)->delete();
            DB::table('voucher_users')->where('voucher_id', $id)->delete();
            DB::table('voucher_outlets')->where('voucher_id', $id)->delete();

            $Voucher->delete();

            return Reply::success(__('messages.recordDeleted'));
        }
        else
        {
            return Reply::error(__('messages.noRecordFound'));
        }
    }


    public static function store_voucher_services($request, $voucher_id)
    {
        $services = $request->services;

        $new_services_id = [];

        if($services[0] == 0)
        {
            foreach(BusinessService::where('status', 'active')->get() as $item)
            {
                $new_services_id[] = $item->id;
            }
        }
        else
        {
            $new_services_id = $services;
        }

        $voucher_services = [];

        foreach($new_services_id as $item)
        {
            $voucher_services[] = [
                'voucher_id' => $voucher_id,
                'business_service_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('voucher_services')->insert($voucher_services);

        return $new_services_id;
    }

    public static function store_voucher_outlet($request, $voucher_id)
    {
        $outlet_id = $request->outlet_id;

        $new_outlet_id = [];

        if($outlet_id[0] == 0)
        {
            foreach(Outlet::where('status', 'active')->get() as $item)
            {
                $new_outlet_id[] = $item->id;
            }
        }
        else
        {
            $new_outlet_id = $outlet_id;
        }

        $voucher_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $voucher_outlets[] = [
                'voucher_id' => $voucher_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('voucher_outlets')->insert($voucher_outlets);

        return $new_outlet_id;
    }

    public static function store_voucher_user($request, $voucher_id)
    {
        $customer_id = $request->customer_id;

        $new_customer_id = [];

        if($customer_id[0] == 0)
        {
            foreach(User::where('status', 'active')->AllCustomers()->get() as $item)
            {
                $new_customer_id[] = $item->id;
            }
        }
        else
        {
            $new_customer_id = $customer_id;
        }

        $voucher_users = [];

        foreach($new_customer_id as $item)
        {
            $voucher_users[] = [
                'voucher_id' => $voucher_id,
                'user_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('voucher_users')->insert($voucher_users);

        return $new_customer_id;
    }

    public static function store_voucher_gender($request, $voucher_id)
    {
        $gender = $request->gender;

        $new_gender = [];

        if($gender[0] == "all")
        {                
            $new_gender = [
                'male', 'female', 'others'
            ];      
        }
        else
        {
            $new_gender = $gender;
        }

        $voucher_gender = [];

        foreach($new_gender as $item)
        {
            $voucher_gender[] = [
                'voucher_id' => $voucher_id,
                'gender' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('voucher_gender')->insert($voucher_gender);

        return $new_gender;
    }

} /* end of class */
