<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoyaltyShop;
use App\LoyaltyShopRedeem;
use App\LoyaltyShopUsage;
use App\LoyaltyShopUser;
use App\Outlet;
use App\Product;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoyaltyShopController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', 'Loyalty Shop');
    }

    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_loyalty_shop'), 403);

        if (request()->ajax()) 
        {            
            $loyalty_shop = LoyaltyShop::where('status', 'active')
                                ->orderBy('created_at', 'desc')
                                ->get();

            return datatables()->of($loyalty_shop)
                ->addColumn('action', function ($row) {
                    $action = '';
                    
                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_loyalty_shop')) 
                    {
                        $action .= '<a href="' . route('admin.loyalty-shop.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                            data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_loyalty_shop')) 
                    {
                        $action .= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view_loyalty_shop"
                            data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_loyalty_shop')) 
                    {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-row-loyalty_shop_id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';                  
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . $row->loyalty_shop_image_url . '" class="img" height="65em" width="65em"/> ';
                })
                ->editColumn('title', function ($row) {
                    return ucfirst($row->title);
                })
                // ->editColumn('loyalty_shop_type', function ($row) {
                //     return ucfirst($row->loyalty_shop_type);
                // })
                ->editColumn('start_date_time', function ($row) {                  
                    if(!empty($row->start_date_time))
                    {
                        return Carbon::parse($row->start_date_time)->translatedFormat($this->settings->date_format . ' ' . $this->settings->time_format);
                    }    
                })
                ->editColumn('end_date_time', function ($row) {                   
                    if(!empty($row->end_date_time))
                    {
                        return Carbon::parse($row->end_date_time)->translatedFormat($this->settings->date_format . ' ' . $this->settings->time_format);
                    }  
                })
                // ->editColumn('discount', function ($row) {
                //     if($row->discount_type == "percent")
                //     {                        
                //         return $row->discount . "%";                      
                //     }
                //     else
                //     {
                //         return "$".$row->discount;
                //     }

                //     // return $row->percentage;
                // })
                ->editColumn('loyalty_point', function ($row) {
                    return $row->loyalty_point;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="badge badge-success">' . __("app.active") . '</label>';
                    } elseif ($row->status == 'inactive') {
                        return '<label class="badge badge-danger">' . __("app.inactive") . '</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'title', 'start_date_time', 'end_date_time', 'status'])
                ->toJson();
        }

        return view('admin.loyalty-shop.index');
    }

    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_loyalty_shop'), 403);

        $outlets = Outlet::where('status', 'active')->get();
        $services = BusinessService::where('status', 'active')->get();
        $customers = User::where('status', 'active')->AllCustomers()->get();
        $products = Product::where('status', 'active')->get();

        $data = [
            'outlets' => $outlets,
            'services' => $services,
            'customers' => $customers,
            'products' => $products,
        ];

        return view('admin.loyalty-shop.create', $data);
    }

    public function store(Request $request)
    {
        // return $request->all();

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_loyalty_shop'), 403);

        $rules = [
            'title' => 'required|string|max:255|unique:loyalty_shops,title',
            'slug' => 'required|string|max:255|unique:loyalty_shops,slug',
            'short_description' => 'nullable',
            'applied_between_dates' => 'nullable',
            'loyalty_point' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            // 'discount' => 'required|numeric|min:0',
            // 'discount_type' => 'required|in:percent,amount',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0|gte:min_age',
            'validity' => 'required|integer|min:0',
            'validity_type' => 'required',          
            // 'loyalty_shop_type' => 'required|in:service,product',
            'product_id' => 'required|exists:products,id',
            'outlet_id' => 'required|array',
            'gender' => 'required|array',
        ];       

        if($request->is_customer_specific == 1)
        {
            $rules['customer_id'] = 'required';
        }

        // if($request->loyalty_shop_type == "service")
        // {
        //     $rules['service_id'] = 'required|exists:business_services,id';
        // }
        // else
        // {
        //     $rules['product_id'] = 'required|exists:products,id';
        // }

        $request->validate($rules);

        if($request->filled('applied_between_dates'))
        {
            $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->loyalty_shop_startDate)->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('Y-m-d H:i a', $request->loyalty_shop_endDate)->format('Y-m-d H:i:s');
        }
        else
        {
            $startDate = null;
            $endDate = null;
        }

        // $startTime = Carbon::createFromFormat('H:i a', $request->loyalty_shop_startTime)->format('H:i:s');
        // $endTime  = Carbon::createFromFormat('H:i a', $request->loyalty_shop_endTime)->format('H:i:s');

        $cleanDescription = strip_tags($request->input('description'));      

        // loyalty shop start

        $loyalty_shop = new LoyaltyShop();
        $loyalty_shop->title = $request->title;
        $loyalty_shop->slug = $request->slug;
        $loyalty_shop->short_description = $request->short_description;
        $loyalty_shop->start_date_time = $startDate;
        $loyalty_shop->end_date_time = $endDate;        
        $loyalty_shop->loyalty_point = $request->loyalty_point ?? 0;
        $loyalty_shop->status = $request->status;
        $loyalty_shop->description = $request->input('description');  
        // $loyalty_shop->discount = $request->discount ?? 0;                
        // $loyalty_shop->discount_type = $request->discount_type;        
        $loyalty_shop->min_age = $request->min_age;
        $loyalty_shop->max_age = $request->max_age;
        $loyalty_shop->validity = $request->validity;
        $loyalty_shop->validity_type = $request->validity_type;
        $loyalty_shop->is_redeemable = $request->has('is_redeemable') ? 1 : 2;
        $loyalty_shop->is_welcome = $request->has('is_welcome') ? 1 : 0;
        // $loyalty_shop->loyalty_shop_type = $request->loyalty_shop_type;

        if ($request->hasFile('feature_image')) 
        {
            $loyalty_shop_image = Files::upload($request->feature_image, 'loyalty-shop');
            $loyalty_shop->image = $loyalty_shop_image;
        }

        if ($request->filled('is_customer_specific')) {
            $loyalty_shop->is_customer_specific = 1;          
        } else {
            $loyalty_shop->is_customer_specific = 0;
        }

        // if($request->loyalty_shop_type == 'service')
        // {
        //     $loyalty_shop->service_id = $request->service_id;
        // }
        // else
        // {
        //     $loyalty_shop->product_id = $request->product_id;         
        // }

        $loyalty_shop->product_id = $request->product_id;

        $loyalty_shop->save();

        // loyalty shop end

        // loyalty shop outlet start

        if($request->filled('outlet_id'))
        {
            $new_outlet_id = $this->store_loyalty_shop_outlet($request, $loyalty_shop->id);

            $loyalty_shop->outlet_id = implode(',', $new_outlet_id);
            $loyalty_shop->save();
        }

        // loyalty shop outlet end

        // loyalty shop users start

        if($request->filled('is_customer_specific'))
        {
            if($request->filled('customer_id'))
            {
                $new_customer_id = $this->store_loyalty_shop_user($request, $loyalty_shop->id);

                // $loyalty_shop->user_id = implode(',', $new_customer_id);
                // $loyalty_shop->save();
            }
        }

        // loyalty shop users end

        // loyalty shop gender start

        if($request->filled('gender'))
        {
            $new_gender = $this->store_loyalty_shop_gender($request, $loyalty_shop->id);

            $loyalty_shop->gender = implode(',', $new_gender);
            $loyalty_shop->save();
        }

        return Reply::redirect(route('admin.loyalty-shop.index'), __('messages.createdSuccessfully'));
    }

    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_loyalty_shop'), 403);

        $LoyaltyShop = LoyaltyShop::findOrFail($id);

        return view('admin.loyalty-shop.show', compact('LoyaltyShop'));
    }

    public function edit($loyalty_shop_id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_loyalty_shop'), 403);

        $LoyaltyShop = LoyaltyShop::find($loyalty_shop_id);

        $selectedOutlets = explode(",", $LoyaltyShop->outlet_id);
        // $selectedCustomers = explode(",", $LoyaltyShop->user_id);
        $selectedCustomers = LoyaltyShopUser::where('loyalty_shop_id', $loyalty_shop_id)->pluck('user_id')->toArray();
        $selectedCustomersGender = explode(",", $LoyaltyShop->gender);

        $outlets = Outlet::where('status', 'active')->get();
        $services = BusinessService::where('status', 'active')->get();
        $customers = User::where('status', 'active')->AllCustomers()->get();
        $products = Product::where('status', 'active')->get();

        $data = [
            'LoyaltyShop' => $LoyaltyShop,
            'outlets' => $outlets,
            'services' => $services,
            'customers' => $customers,
            'products' => $products,
            'selectedOutlets' => $selectedOutlets,
            'selectedCustomers' => $selectedCustomers,
            'selectedCustomersGender' => $selectedCustomersGender,
        ];

        return view('admin.loyalty-shop.edit', $data);
    }

    public function update(Request $request, $id)
    {
        // return $request->all();

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_loyalty_shop'), 403);

        $rules = [
            'title' => 'required|string|max:255|unique:loyalty_shops,title,' . (int) $id,
            'slug' => 'required|string|max:255|unique:loyalty_shops,slug,' . (int) $id,
            'short_description' => 'nullable',
            'applied_between_dates' => 'nullable',
            'loyalty_point' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            // 'discount' => 'required|numeric|min:0',
            // 'discount_type' => 'required|in:percent,amount',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0|gte:min_age',
            'validity' => 'required|integer|min:0',
            'validity_type' => 'required',          
            // 'loyalty_shop_type' => 'required|in:service,product',
            'outlet_id' => 'required|array',
            'gender' => 'required|array',
            'product_id' => 'required|exists:products,id',
        ];       

        if($request->is_customer_specific == 1)
        {
            $rules['customer_id'] = 'required';
        }

        // if($request->loyalty_shop_type == "service")
        // {
        //     $rules['service_id'] = 'required|exists:business_services,id';
        // }
        // else
        // {
        //     $rules['product_id'] = 'required|exists:products,id';
        // }

        $request->validate($rules);

        if($request->filled('applied_between_dates'))
        {
            $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->loyalty_shop_startDate)->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('Y-m-d H:i a', $request->loyalty_shop_endDate)->format('Y-m-d H:i:s');
        }
        else
        {
            $startDate = null;
            $endDate = null;
        }

        // $startTime = Carbon::createFromFormat('H:i a', $request->loyalty_shop_startTime)->format('H:i:s');
        // $endTime  = Carbon::createFromFormat('H:i a', $request->loyalty_shop_endTime)->format('H:i:s');

        $cleanDescription = strip_tags($request->input('description'));   
        
        // loyalty shop start

        $loyalty_shop = LoyaltyShop::find($id);
        $loyalty_shop->title = $request->title;
        $loyalty_shop->slug = $request->slug;
        $loyalty_shop->short_description = $request->short_description;
        $loyalty_shop->start_date_time = $startDate;
        $loyalty_shop->end_date_time = $endDate;        
        $loyalty_shop->loyalty_point = $request->loyalty_point ?? 0;
        $loyalty_shop->status = $request->status;
        $loyalty_shop->description = $request->input('description');  
        // $loyalty_shop->discount = $request->discount ?? 0;                
        // $loyalty_shop->discount_type = $request->discount_type;        
        $loyalty_shop->min_age = $request->min_age;
        $loyalty_shop->max_age = $request->max_age;
        $loyalty_shop->validity = $request->validity;
        $loyalty_shop->validity_type = $request->validity_type;
        $loyalty_shop->is_redeemable = $request->has('is_redeemable') ? 1 : 2;
        $loyalty_shop->is_welcome = $request->has('is_welcome') ? 1 : 0;
        // $loyalty_shop->loyalty_shop_type = $request->loyalty_shop_type;

        if ($request->hasFile('feature_image')) 
        {
            Files::deleteFile($loyalty_shop->image, 'loyalty-shop');
            $loyalty_shop_image = Files::upload($request->feature_image, 'loyalty-shop');
            $loyalty_shop->image = $loyalty_shop_image;
        }
        else
        {
            $loyalty_shop_image = $loyalty_shop->image;
        }

        if ($request->filled('is_customer_specific')) {
            $loyalty_shop->is_customer_specific = 1;          
        } else {
            $loyalty_shop->is_customer_specific = 0;
        }

        // if($request->loyalty_shop_type == 'service')
        // {
        //     $loyalty_shop->service_id = $request->service_id;
        //     $loyalty_shop->product_id = null;   
        // }
        // else
        // {
        //     $loyalty_shop->product_id = $request->product_id;
        //     $loyalty_shop->service_id = null;            
        // }

        $loyalty_shop->product_id = $request->product_id;

        $loyalty_shop->save();

        // loyalty shop end

        if($request->filled('is_customer_specific'))
        {
            if($request->filled('customer_id'))
            {       
                
            }
            else
            {
                DB::table('loyalty_shop_users')->where('loyalty_shop_id', $loyalty_shop->id)->delete();
            }
        }
        else
        {
            DB::table('loyalty_shop_users')->where('loyalty_shop_id', $loyalty_shop->id)->delete();
        }

        // loyalty shop outlet start

        if($request->filled('outlet_id'))
        {
            DB::table('loyalty_shop_outlets')->where('loyalty_shop_id', $loyalty_shop->id)->delete();

            $new_outlet_id = $this->store_loyalty_shop_outlet($request, $loyalty_shop->id);

            $loyalty_shop->outlet_id = implode(',', $new_outlet_id);
            $loyalty_shop->save();
        }

        // loyalty shop outlet end

        // loyalty shop users start

        if($request->filled('is_customer_specific'))
        {
            if($request->filled('customer_id'))
            {
                if($request->customer_id[0] != 0)
                {
                        DB::table('loyalty_shop_users')
                                ->where('loyalty_shop_id', $loyalty_shop->id)    
                                ->whereNotIn('user_id', $request->customer_id)
                                ->delete();
                }

                $new_customer_id = $this->store_loyalty_shop_user($request, $loyalty_shop->id);

                // $loyalty_shop->user_id = implode(',', $new_customer_id);
                // $loyalty_shop->save();
            }
        }

        // loyalty shop users end

        // loyalty shop gender start

        if($request->filled('gender'))
        {
            DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $loyalty_shop->id)->delete();
            
            $new_gender = $this->store_loyalty_shop_gender($request, $loyalty_shop->id);

            $loyalty_shop->gender = implode(',', $new_gender);
            $loyalty_shop->save();
        }

        return Reply::redirect(route('admin.loyalty-shop.index'), __('messages.createdSuccessfully'));
    }

    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_loyalty_shop'), 403);

        $LoyaltyShop = LoyaltyShop::find($id);

        if($LoyaltyShop)
        {
            if ($LoyaltyShop->image) 
            {
                Files::deleteFile($LoyaltyShop->image, 'loyalty-shop');
            }

            DB::table('loyalty_shop_users')->where('loyalty_shop_id', $id)->delete();
            DB::table('loyalty_shop_gender')->where('loyalty_shop_id', $id)->delete();
            DB::table('loyalty_shop_outlets')->where('loyalty_shop_id', $id)->delete();
            
            LoyaltyShopRedeem::where('loyalty_shop_id', $id)->delete();
            LoyaltyShopUsage::where('loyalty_shop_id', $id)->delete();

            $LoyaltyShop->delete();

            return Reply::success(__('messages.recordDeleted'));
        }
        else
        {
            return Reply::error(__('messages.noRecordFound'));
        }
    }

    public static function store_loyalty_shop_outlet($request, $loyalty_shop_id)
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

        $loyalty_shop_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $loyalty_shop_outlets[] = [
                'loyalty_shop_id' => $loyalty_shop_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('loyalty_shop_outlets')->insert($loyalty_shop_outlets);

        return $new_outlet_id;
    }

    public static function store_loyalty_shop_user($request, $loyalty_shop_id)
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

        $loyalty_shop_users = [];

        foreach($new_customer_id as $item)
        {
            $exists = DB::table('loyalty_shop_users')
                        ->where('loyalty_shop_id', $loyalty_shop_id)
                        ->where('user_id', $item)
                        ->exists();

            if (!$exists) 
            {
                $loyalty_shop_users[] = [
                    'loyalty_shop_id' => $loyalty_shop_id,
                    'user_id' => $item,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('loyalty_shop_users')->insert($loyalty_shop_users);

        return $new_customer_id;
    }

    public static function store_loyalty_shop_gender($request, $loyalty_shop_id)
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

        $loyalty_shop_gender = [];

        foreach($new_gender as $item)
        {
            $loyalty_shop_gender[] = [
                'loyalty_shop_id' => $loyalty_shop_id,
                'gender' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('loyalty_shop_gender')->insert($loyalty_shop_gender);

        return $new_gender;
    }
}
