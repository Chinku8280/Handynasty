<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\Category;
use App\Outlet;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Service\StoreService;
use App\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\CreateService;
use App\LoyaltyShop;
use App\LoyaltyShopRedeem;
use App\LoyaltyShopUsage;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class BusinessServiceController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.services'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_business_service'), 403);

        $categories = Category::orderBy('name', 'ASC')->get();

        // Initialize $category_notif_status to avoid undefined variable error
        $service_notif_status = NotificationSettingController::get_notification_settings('service_notif_status') ?? false;

        if(\request()->ajax()){

            if($request->filled('category_id'))
            {
                $category_id = $request->category_id ?? '';
                $services = BusinessService::where('category_id', $category_id)->with('users')->orderBy('order_level', 'desc')->get();
            }
            else
            {
                $services = BusinessService::with('users')->orderBy('order_level', 'desc')->get();
            }

            return \datatables()->of($services)
                ->addColumn('action', function ($row) use ($service_notif_status) {
                    $action = '';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_business_service')) {
                        $action.= '<a href="' . route('admin.business-services.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                          data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_business_service')) {
                        $action.= ' <a href="javascript:;" class="btn btn-warning btn-circle duplicate-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.duplicate').'"><i class="fa fa-clone" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_business_service')) {
                        $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row mr-1"
                          data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    if($service_notif_status == true)
                    {
                        $action .= '<a href="javascript:;" class="btn btn-success btn-circle send_notification_btn"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="Send Push Notification"><i class="fa fa-bell" aria-hidden="true"></i></a> ';
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="'.$row->service_image_url.'" class="img" height="65em" width="65em" /> ';
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->editColumn('status', function ($row) {
                    if($row->status == 'active'){
                        return '<label class="badge badge-success">'.__("app.active").'</label>';
                    }
                    elseif($row->status == 'deactive'){
                        return '<label class="badge badge-danger">'.__("app.deactive").'</label>';
                    }
                })
                // ->editColumn('outlet_id', function ($row) {
                //     return ucfirst($row->outlet->outlet_name);
                // })
                ->editColumn('category_id', function ($row) {
                    return ucfirst($row->category->name);
                })
                ->editColumn('price', function ($row) {
                    return $row->price;
                })
                ->editColumn('users', function ($row) {
                        $user_list = '';
                        foreach ($row->users as $key => $user) {
                            $user_list .= '<span style="margin:0.3em; padding:0.3em" class="badge badge-primary">'.$user->name.'</span>';
                        }
                        return $user_list=='' ? '--' : $user_list;
                })
                ->editColumn('time', function ($row) {
                    return $row->time.' '.$row->time_type;
                })
                ->addColumn('hidden_input', function ($row) {
                    return '<input type="hidden" name="service_id[]" value="' . $row->id . '">';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'status', 'users', 'hidden_input', 'time'])
                ->setRowAttr([
                    'class' => 'ui-sortable-handle',
                    'data-id' => function($row) {
                        return $row->id;
                    }
                ])
                ->toJson();
        }
        

        $data['categories'] = $categories;

        return view('admin.business_service.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateService $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_business_service'), 403);

        $categories = Category::orderBy('name', 'ASC')->get();
        $outlets = Outlet::orderBy('outlet_name', 'ASC')->get();
        $locations = Location::orderBy('name', 'ASC')->get(); // not used
        // $employees = User::AllEmployees()->get();
        $employees = User::AllTherapist()->get();

        $variables = compact('categories', 'outlets', 'locations', 'employees');

        if ($request->service_id) 
        {
            $service = BusinessService::where('id', $request->service_id)->first();
            $variables = Arr::add($variables, 'service', $service);

            $selectedOutlets = DB::table('business_services_outlets')->where('business_service_id', $request->service_id)->pluck('outlet_id')->toArray();
            $variables = Arr::add($variables, 'selectedOutlets', $selectedOutlets);

            $selectedUsers = array();
            $users = BusinessService::with(['users'])->find($request->service_id);
            foreach ($users->users as $key => $user)
            {
                array_push($selectedUsers, $user->id);
            }

            $variables = Arr::add($variables, 'selectedUsers', $selectedUsers);
        }

        return view('admin.business_service.create', $variables);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreService $request)
    {
        // return $request->all();

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_business_service'), 403);

        $service = new BusinessService();
        $service->name = $request->name;
        $service->short_description = $request->short_description;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->time = $request->time ?? 0;
        $service->time_type = $request->time_type;
        // $service->discount = $request->discount;
        // $service->discount_type = $request->discount_type;
        // $service->location_id = $request->location_id;
        $service->category_id = $request->category_id;
        // $service->outlet_id = $request->outlet_id;
        $service->slug = $request->slug;
        $service->loyalty_point = $request->loyalty_point;

        if($request->filled('is_popular'))
        {
            $service->is_popular = 1;
        }
        else
        {
            $service->is_popular = 2;
        }

        $service->save();

        // Assign services to users start

        $employee_ids = $request->employee_ids;
        if($employee_ids)
        {
            $employees   = array();
            foreach ($employee_ids as $key => $service_id)
            {
                $employees[] = $employee_ids[$key];
            }
            $service->users()->attach($employees);
        }

        // Assign services to users end

        // services outlet start

        if($request->filled('outlet_id'))
        {
            $new_outlet_id = $this->store_service_outlet($request, $service->id);
        }

        // services outlet end

        return Reply::dataOnly(['serviceID' => $service->id]);
        // return Reply::redirect(route('admin.business-services.index'), __('messages.updatedSuccessfully'));
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
    public function edit(BusinessService $businessService)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_business_service'), 403);

        $categories = Category::orderBy('name', 'ASC')->get();
        $outlets = Outlet::orderBy('outlet_name', 'ASC')->get();
        $locations = Location::orderBy('name', 'ASC')->get();  // not used
        // $employees = User::AllEmployees()->get();
        $employees = User::AllTherapist()->get();

        $images = [];

        if ($businessService->image && is_iterable($businessService->image)) {
            foreach ($businessService->image as $image) {
                $reqImage['name'] = $image;
                $reqImage['size'] = filesize(public_path('/user-uploads/service/'.$businessService->id.'/'.$image));
                $reqImage['type'] = mime_content_type(public_path('/user-uploads/service/'.$businessService->id.'/'.$image));
                $images[] = $reqImage;
            }
        }

        $images = json_encode($images);

        // push all previous assigned services to an array start

        $selectedUsers = array();
        $users = BusinessService::with(['users'])->find($businessService->id);
        foreach ($users->users as $key => $user)
        {
            array_push($selectedUsers, $user->id);
        }

        // push all previous assigned services to an array end

        // outlets start

        $selectedOutlets = DB::table('business_services_outlets')->where('business_service_id', $businessService->id)->pluck('outlet_id')->toArray();
        $category_outlets_id = DB::table('category_outlets')->where('category_id', $businessService->category_id)->pluck('outlet_id')->toArray();
        $category_outlets = Outlet::whereIn('id', $category_outlets_id)->where('status', 'active')->get();

        // outlets end

        return view('admin.business_service.edit', compact('businessService', 'categories', 'outlets', 'locations', 'images', 'employees', 'selectedUsers', 'selectedOutlets', 'category_outlets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreService $request, $id)
    {
        // return $request->all();
        
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_business_service'), 403);

        $service = BusinessService::find($id);
        $service->name = $request->name;
        $service->short_description = $request->short_description;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->time = $request->time ?? 0;
        $service->time_type = $request->time_type;
        // $service->discount = $request->discount;
        // $service->discount_type = $request->discount_type;
        $service->category_id = $request->category_id;
        // $service->location_id = $request->location_id;
        // $service->outlet_id = $request->outlet_id;
        $service->status = $request->status;
        $service->slug = $request->slug;
        $service->loyalty_point = $request->loyalty_point;

        if($request->filled('is_popular'))
        {
            $service->is_popular = 1;
        }
        else
        {
            $service->is_popular = 2;
        }
        
        $service->save();

        // Assign services to users start

        $employee_ids = $request->employee_ids;
        if($employee_ids)
        {
            $employees   = array();
            foreach ($employee_ids as $key => $service_id)
            {
                $employees[] = $employee_ids[$key];
            }
            $service->users()->sync($employees);
        }
        else{
            $service->users()->detach();
        }

        // Assign services to users end

        // service outlet start

        if($request->filled('outlet_id'))
        {
            DB::table('business_services_outlets')->where('business_service_id', $service->id)->delete();

            $new_outlet_id = $this->store_service_outlet($request, $service->id);
        }

        // service outlet end

        return Reply::dataOnly(['serviceID' => $service->id, 'defaultImage' => $request->default_image ?? 0]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_business_service'), 403);

        $BusinessService = BusinessService::findOrFail($id);

        if($BusinessService)
        {
            // Delete old image if exists
            foreach ($BusinessService->image as $file) {
                Files::deleteFile($file, 'service/'.$BusinessService->id);
            }

            DB::table('business_service_user')->where('business_service_id', $id)->delete();
            DB::table('business_services_outlets')->where('business_service_id', $id)->delete();


            // loyalty shop service delete start

            if(LoyaltyShop::where('service_id', $id)->exists())
            {
                $LoyaltyShop = LoyaltyShop::where('service_id', $id)->get();

                foreach($LoyaltyShop as $item)
                {
                    if ($item->image) 
                    {
                        Files::deleteFile($item->image, 'loyalty-shop');
                    }
                }

                $loyalty_shop_id_arr = LoyaltyShop::where('service_id', $id)->pluck('id')->toArray();
                
                DB::table('loyalty_shop_users')->whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
                DB::table('loyalty_shop_gender')->whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
                DB::table('loyalty_shop_outlets')->whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
                
                LoyaltyShopRedeem::whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
                LoyaltyShopUsage::whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();

                LoyaltyShop::whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
            }

            // loyalty shop service delete end
        }

        $BusinessService->delete();

        // BusinessService::destroy($id);

        return Reply::success(__('messages.recordDeleted'));
    }

    public function storeImages(Request $request) {
        if ($request->hasFile('file')) {
            $service = BusinessService::where('id', $request->service_id)->first();
            $service_images_arr = [];

            foreach ($request->file as $fileData) {
                array_push($service_images_arr, Files::upload($fileData, 'service/'.$service->id));
            }
            $service->image = json_encode($service_images_arr);
            $service->default_image = $service_images_arr[0];
            $service->save();
        }

        return Reply::redirect(route('admin.business-services.index'), __('messages.createdSuccessfully'));
    }

    public function updateImages(Request $request) {
        $service = BusinessService::where('id', $request->service_id)->first();

        $service_images_arr = [];
        $default_image_index = 0;

        if ($request->hasFile('file')) {
            if ($request->file[0]->getClientOriginalName() !== 'blob') {
                foreach ($request->file as $fileData) {
                    array_push($service_images_arr, Files::upload($fileData, 'service/'.$service->id));
                    if ($fileData->getClientOriginalName() == $request->default_image) {
                        $default_image_index = array_key_last($service_images_arr);
                    }
                }
            }
            if ($request->uploaded_files) {
                $files = json_decode($request->uploaded_files, true);
                foreach ($files as $file) {
                    array_push($service_images_arr, $file['name']);
                    if ($file['name'] == $request->default_image) {
                        $default_image_index = array_key_last($service_images_arr);
                    }
                }
                $arr_diff = array_diff($service->image, $service_images_arr);

                if (sizeof($arr_diff) > 0) {
                    foreach ($arr_diff as $file) {
                        Files::deleteFile($file, 'service/'.$service->id);
                    }
                }
            }
            else {
                if (!is_null($service->image) && sizeof($service->image) > 0) {
                    foreach ($service->image as $file) {
                        Files::deleteFile($file, 'service/'.$service->id);
                    }
                    // Files::deleteFile($service->image[0], 'service/'.$service->id);
                }
            }
        }

        $service->image = json_encode(array_values($service_images_arr));
        $service->default_image = sizeof($service_images_arr) > 0 ? $service_images_arr[$default_image_index] : null;
        $service->save();

        return Reply::redirect(route('admin.business-services.index'), __('messages.updatedSuccessfully'));
    }


    public static function store_service_outlet($request, $service_id)
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

        $service_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $service_outlets[] = [
                'business_service_id' => $service_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('business_services_outlets')->insert($service_outlets);

        return $new_outlet_id;
    }

    // get_services_by_category

    public function get_services_by_category(Request $request)
    {
        // return $request->all();

        $category_id = $request->category_id  ?? '';

        $services = BusinessService::where('category_id', $category_id)->where('status', 'active')->get();

        $data['services'] = $services;

        return response()->json($data);
    }

    public function get_services_by_category_outlet(Request $request)
    {
        // return $request->all();

        $category_id = $request->category_id  ?? '';
        $outlet_id = $request->outlet_id  ?? '';

        // $services = BusinessService::where('category_id', $category_id)->where('status', 'active')->get();

        $services = BusinessService::join('business_services_outlets', 'business_services.id', '=', 'business_services_outlets.business_service_id')
                        ->where('business_services.category_id', $category_id)
                        ->where('business_services.status', 'active')
                        ->where('business_services_outlets.outlet_id', $outlet_id)
                        ->select('business_services.*') // select only the service fields
                        ->get();

        $data['services'] = $services;

        return response()->json($data);
    }

    // get_total_hours_by_services

    public function get_total_hours_by_services(Request $request)
    {
        // return $request->all();

        $services_id = $request->services_id ?? [];

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

        $data['totalHours'] = number_format($totalHours, 1);

        return response()->json($data);
    }

    // calculate_total_services_hours

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

    // calculate_single_service_hours

    public static function calculate_single_service_hours($service_id)
    {
        // return $request->all();

        $services = BusinessService::where('id', $service_id)->get();

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

        return number_format($totalHours, 1);
    }

    // get_service_details

    public function get_service_details(Request $request)
    {
        // return $request->all();

        $service_id = $request->service_id ?? [];

        $service = DB::table('business_services')->where('id', $service_id)
                                    ->where('status', 'active')
                                    ->select('id', 'name', 'loyalty_point')
                                    ->first();

        $service->hours = $this->calculate_single_service_hours($service_id);
        
        $data['service'] = $service;

        return response()->json($data);
    }

    public function sort_update(Request $request)
    {
        // return $request->all();

        $service_id = $request->service_id;

        $j = count($service_id);

        for($i=0; $i<count($service_id); $i++)
        {
            // $arr[] = [
            //     'id' => $service_id[$i],
            //     'order_level' => $j
            // ];

            $service = BusinessService::find($service_id[$i]);
            $service->order_level = $j;
            $service->save();

            $j--;
        }

        // return $arr;

        return back()->with('success', __('messages.updatedSuccessfully'));
    }

    public function send_notification(Request $request)
    {
        // return $request->all();

        $service_id = $request->service_id;

        $service = BusinessService::find($service_id);

        if($service)
        {
            $customers = User::allCustomers()->get();

            return NotificationController::send_notification($request, $customers, $service_id, 'service_id', 'service');

            // return response()->json(['status' => 'success', 'message' => 'Notification send successfully']);
        }
        else
        {
            return response()->json(['status' => 'failed', 'message' => 'Service not found']);
        }
    }
}
