<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\Coupon;
use App\CouponRedeem;
use App\CouponUsage;
use App\CouponUser;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Coupon\StoreRequest;
use App\Http\Requests\Coupon\UpdateRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Location;
use App\Outlet;
use App\Role;
use FontLib\Table\Type\fpgm;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.coupons'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_coupon'), 403);
        return view('admin.coupons.index');
    }

    public function data()
    {
        $coupon = Coupon::all();

        // Initialize $category_notif_status to avoid undefined variable error
        $coupon_notif_status = NotificationSettingController::get_notification_settings('coupon_notif_status') ?? false;

        return \datatables()->of($coupon)
            ->addColumn('action', function ($row) use ($coupon_notif_status) {
                $action = '';
               
                if($this->user->can('update_coupon')) {
                    $action.= '<a href="' . route('admin.coupons.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                    data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                }
                
                $action.= '<a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-coupon"
                data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-eye" aria-hidden="true"></i></a> ';

                if($this->user->can('delete_coupon')) {
                    $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row mr-1"
                    data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }

                // $action.= ' <a href="javascript:;" class="btn btn-success btn-circle send-notification"
                // data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="Send Push Notification"><i class="fa fa-bell" aria-hidden="true"></i></a> ';            

                if($coupon_notif_status == true)
                {
                    $action .= '<a href="javascript:;" class="btn btn-success btn-circle send_notification_btn"
                    data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="Send Push Notification"><i class="fa fa-bell" aria-hidden="true"></i></a> ';
                }

                return $action;
            })

            ->editColumn('title', function ($row) {
                return '<span class="badge badge-warning">'.strtoupper($row->coupon_code).'</span>';
            })
            ->editColumn('start_date_time', function ($row) {
                
                return Carbon::parse($row->start_date_time)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format);

            })
            ->editColumn('end_date_time', function ($row) {
                if($row->end_date_time){
                    return Carbon::parse($row->end_date_time)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format);
                }
                return '-';
            })
            ->editColumn('amount', function ($row) {
                if($row->amount && is_null($row->percent)){
                    return $row->amount;
                }
                elseif(is_null($row->amount) && !is_null($row->percent)){
                    return $row->percent.'%';
                }
                elseif(!is_null($row->amount) && !is_null($row->percent)){
                    return __('app.maxAmountOrPercent', ['percent' => $row->percent, 'maxAmount' => $row->amount]);
                }
            })
            ->editColumn('status', function ($row) {
                if($row->status == 'active'){
                    return '<label class="badge badge-success">'.__("app.active").'</label>';
                }
                elseif($row->status == 'inactive'){
                    return '<label class="badge badge-danger">'.__("app.inactive").'</label>';
                }
                elseif($row->status == 'expire'){
                    return '<label class="badge badge-danger">'.__("app.expire").'</label>';
                }
            })

            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'title'])
            ->make(true);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_coupon'), 403);

        $this->days = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];

        $branches  = Location::groupBy('name')->get();
    
        $outlets = Outlet::where('status', 'active')->get();
        $services = BusinessService::where('status', 'active')->get();
        $customers = User::where('status', 'active')->AllCustomers()->get();

        return view('admin.coupons.create', compact('branches', 'outlets','services', 'customers'), $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */

    public function store(StoreRequest $request)
    {
        // return $request->all();

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_coupon'), 403);

        if(!$request->has('days')){
            return Reply::error( __('messages.coupon.selectDay'));
        }

        if ($request->hasFile('coupon_image')) 
        {
            $imageName = time() . '.' . $request->coupon_image->getClientOriginalExtension();
            $request->coupon_image->move(public_path('/user-uploads/coupon-images'), $imageName);
        }
        else
        {
          $imageName = null;  
        }

        $coupon = new Coupon();

        $coupon->title                   = $request->coupon_title;
        $coupon->coupon_code             = strtolower($request->coupon_code);
        $coupon->start_date_time         = Carbon::createFromFormat('Y-m-d H:i a', $request->startDate.' '.$request->startTime)->format('Y-m-d H:i:s');
        $coupon->uses_limit              = $request->uses_time;
        $coupon->points                  = $request->points;
        $coupon->amount                  = $request->amount;
        $coupon->percent                 = $request->percent;
        $coupon->minimum_purchase_amount = ($request->minimum_purchase_amount) ? $request->minimum_purchase_amount : 0;
        $coupon->days                    = json_encode($request->days);
        $coupon->description             =  $request->description;
        $coupon->coupon_image            =  $imageName;
        $coupon->status                  =  $request->status;
        $coupon->short_description       =  $request->short_description;

        if($request->end_time)
        {
            $coupon->end_date_time       = Carbon::createFromFormat('Y-m-d H:i a', $request->endDate.' '.$request->endTime)->format('Y-m-d H:i:s');
        }

        $coupon->min_age = $request->min_age;
        $coupon->max_age = $request->max_age;

        if($request->filled('is_customer_specific'))
        {
            $coupon->is_customer_specific = 1;
        }
        else
        {
            $coupon->is_customer_specific = 0;
        }

        $coupon->save();

        // coupon sevices start

        if($request->filled('services'))
        {
            $new_services_id = $this->store_coupon_services($request, $coupon->id);

            $coupon->service_id = implode(',', $new_services_id);
            $coupon->save();
        }

        // coupon sevices end

        // coupon outlet start

        if($request->filled('outlet_id'))
        {
            $new_outlet_id = $this->store_coupon_outlet($request, $coupon->id);

            $coupon->outlet_id = implode(',', $new_outlet_id);
            $coupon->save();
        }

        // coupon outlet end

        // coupon users start

        if($request->filled('is_customer_specific'))
        {
            if($request->filled('customer_id'))
            {
                $new_customer_id = $this->store_coupon_user($request, $coupon->id);

                // $coupon->user_id = implode(',', $new_customer_id);
                // $coupon->save();
            }
        }

        // coupon users end

        // coupon gender start

        if($request->filled('gender'))
        {
            $new_gender = $this->store_coupon_gender($request, $coupon->id);

            $coupon->gender = implode(',', $new_gender);
            $coupon->save();
        }

        // coupon gender end

        return Reply::redirect(route('admin.coupons.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->coupon = Coupon::findOrFail($id);

        if($this->coupon->days){
            $this->days = json_decode($this->coupon->days);
        }

        return view('admin.coupons.show', $this->data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_coupon'), 403);

       $this->days = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];

        $this->coupon = Coupon::with('customers')->findOrFail($id);
        $this->selectedDays = json_decode($this->coupon->days);
        
        $branches  = Location::groupBy('name')->get();
        $outlets = Outlet::where('status', 'active')->get();
        $services = BusinessService::where('status', 'active')->get();
        $customers = User::where('status', 'active')->AllCustomers()->get();
       
        $this->selectedOutlets = explode(",", $this->coupon->outlet_id);
        $this->selectedServices = explode(",", $this->coupon->service_id);
        $this->selectedCustomers = // explode(",", $this->coupon->user_id)
        $this->selectedCustomers = CouponUser::where('coupon_id', $id)->pluck('user_id')->toArray();
        $this->selectedCustomersGender = explode(",", $this->coupon->gender);

        return view('admin.coupons.edit', compact('branches','services','outlets','customers'), $this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        // return $request->all();

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_coupon'), 403);

         if(!$request->has('days')){
             return Reply::error( __('messages.coupon.selectDay'));
         }

        if ($request->hasFile('coupon_image')) {
            $imageName = time() . '.' . $request->coupon_image->getClientOriginalExtension();
            $request->coupon_image->move(public_path('/user-uploads/coupon-images'), $imageName);
        }else{
            $imageName = null;  
        }

        // return $imageName;

        $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->startDate.' '.$request->startTime)->format('Y-m-d H:i:s');

        $coupon = Coupon::findOrFail($id);

        $coupon->title                   = $request->coupon_title;
        $coupon->coupon_code             = strtolower($request->coupon_code);
        $coupon->start_date_time         = $startDate;
        $coupon->uses_limit              = $request->uses_time;
        $coupon->points                  = $request->points;
        $coupon->amount                  = $request->amount;
        $coupon->percent                 = $request->percent;
        $coupon->minimum_purchase_amount = ($request->minimum_purchase_amount) ? $request->minimum_purchase_amount : 0;
        $coupon->days                    = json_encode($request->days);
        $coupon->status                  =  $request->status;
        $coupon->description             =  $request->description;
        $coupon->short_description       =  $request->short_description;

        if ($request->hasFile('coupon_image')) 
        {
            $coupon->coupon_image         =  $imageName;
        }
        
        if($request->end_time)
        {
            $coupon->end_date_time       = Carbon::createFromFormat('Y-m-d H:i a', $request->endDate.' '.$request->endTime)->format('Y-m-d H:i:s');
        }

        $coupon->min_age = $request->min_age;
        $coupon->max_age = $request->max_age;

        if($request->filled('is_customer_specific'))
        {
            $coupon->is_customer_specific = 1;
        }
        else
        {
            $coupon->is_customer_specific = 0;
        }

        $coupon->save();

        // coupon sevices start

        if($request->filled('services'))
        {
            DB::table('coupon_services')->where('coupon_id', $coupon->id)->delete();

            $new_services_id = $this->store_coupon_services($request, $coupon->id);

            $coupon->service_id = implode(',', $new_services_id);
            $coupon->save();
        }

        // coupon sevices end

        // coupon outlet start

        if($request->filled('outlet_id'))
        {
            DB::table('coupon_outlets')->where('coupon_id', $coupon->id)->delete();

            $new_outlet_id = $this->store_coupon_outlet($request, $coupon->id);

            $coupon->outlet_id = implode(',', $new_outlet_id);
            $coupon->save();
        }

        // coupon outlet end

        // coupon users start

        if($request->filled('is_customer_specific'))
        {
            if($request->filled('customer_id'))
            {
                DB::table('coupon_users')->where('coupon_id', $coupon->id)->delete();

                $new_customer_id = $this->store_coupon_user($request, $coupon->id);

                // $coupon->user_id = implode(',', $new_customer_id);
                // $coupon->save();
            }
        }
        else
        {
            DB::table('coupon_users')->where('coupon_id', $coupon->id)->delete();

            // $coupon->user_id = null;
            // $coupon->save();
        }

        // coupon users end

        // coupon gender start

        if($request->filled('gender'))
        {
            DB::table('coupon_gender')->where('coupon_id', $coupon->id)->delete();

            $new_gender = $this->store_coupon_gender($request, $coupon->id);

            $coupon->gender = implode(',', $new_gender);
            $coupon->save();
        }

        // coupon gender end

        return Reply::redirect(route('admin.coupons.index'), __('messages.updatedSuccessfully'));
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

        $coupon = Coupon::find($id);

        if($coupon)
        {
            DB::table('coupon_services')->where('coupon_id', $id)->delete();
            DB::table('coupon_users')->where('coupon_id', $id)->delete();
            DB::table('coupon_gender')->where('coupon_id', $id)->delete();
            DB::table('coupon_outlets')->where('coupon_id', $id)->delete();

            CouponRedeem::where('coupon_id', $id)->delete();
            CouponUsage::where('coupon_id', $id)->delete();

            $coupon->delete();

            return Reply::success(__('messages.recordDeleted'));
        }
        else
        {
            return Reply::error(__('messages.noRecordFound'));
        }
    }

    public static function store_coupon_services($request, $coupon_id)
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

        $coupon_services = [];

        foreach($new_services_id as $item)
        {
            $coupon_services[] = [
                'coupon_id' => $coupon_id,
                'business_service_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('coupon_services')->insert($coupon_services);

        return $new_services_id;
    }

    public static function store_coupon_outlet($request, $coupon_id)
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

        $coupon_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $coupon_outlets[] = [
                'coupon_id' => $coupon_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('coupon_outlets')->insert($coupon_outlets);

        return $new_outlet_id;
    }

    public static function store_coupon_user($request, $coupon_id)
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

        $coupon_users = [];

        foreach($new_customer_id as $item)
        {
            $coupon_users[] = [
                'coupon_id' => $coupon_id,
                'user_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('coupon_users')->insert($coupon_users);

        // redeem coupon
        // DB::table('coupon_redeems')->insert($coupon_users);

        return $new_customer_id;
    }

    public static function store_coupon_gender($request, $coupon_id)
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

        $coupon_gender = [];

        foreach($new_gender as $item)
        {
            $coupon_gender[] = [
                'coupon_id' => $coupon_id,
                'gender' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('coupon_gender')->insert($coupon_gender);

        return $new_gender;
    }

    public function send_notification(Request $request)
    {
        // return $request->all();

        $coupon_id = $request->coupon_id;

        $coupon = Coupon::find($coupon_id);

        if($coupon)
        {
            if($coupon->is_customer_specific == 0)
            {
                $customers = User::allCustomers()->get();
            }
            else
            {
                $CouponUser_arr = CouponUser::where('coupon_id', $coupon_id)->pluck('user_id')->toArray();
                $customers = User::whereIn('id', $CouponUser_arr)->get();
            }

            return NotificationController::send_notification($request, $customers, $coupon_id, 'coupon_id', 'coupon');

            // return response()->json(['status' => 'success', 'message' => 'Notification send successfully']);
        }
        else
        {
            return response()->json(['status' => 'failed', 'message' => 'Coupon not found']);
        }
    }
}
