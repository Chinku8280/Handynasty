<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use Illuminate\Http\Request;
use App\Promotion;
use App\Helper\Files;
use App\Helper\Reply;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Location;
use App\Outlet;
use App\PromotionItem;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.promotion'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_promotion'), 403);

        if(request()->ajax())
        {
            $promotion = Promotion::get();

            foreach($promotion as $item)
            {
                $promotion_outlet_id_arr = DB::table('promotion_outlets')->where('promotion_id', $item->id)->pluck('outlet_id')->toArray();
                $promotion_outlet_name_arr = Outlet::whereIn('id', $promotion_outlet_id_arr)->pluck('outlet_name')->toArray();
            
                $item->outlet_name = implode(', ', $promotion_outlet_name_arr);
            }

            return datatables()->of($promotion)
                ->addColumn('action', function ($row) {
                    $action = '';

                    if($this->user->can('update_promotion')) {
                        $action.= '<a href="' . route('admin.promotion.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if($this->user->can('read_promotion')) {
                        $action.= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-deal"
                        data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-search" aria-hidden="true"></i></a> ';
                    }

                    if($this->user->can('delete_promotion')) {
                        $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="'.$row->promotion_image_url.'" class="img" height="65em" width="65em"/> ';
                })
                ->editColumn('title', function ($row) {
                    return ucfirst($row->title);
                })
                ->editColumn('start_date_time', function ($row) {
                    return Carbon::parse($row->start_date_time)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format);

                })
                ->editColumn('end_date_time', function ($row) {
                    return Carbon::parse($row->end_date_time)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format);
                })
                ->editColumn('original_amount', function ($row) {
                    return $row->original_amount;
                })
                ->editColumn('deal_amount', function ($row) {
                    return $row->deal_amount;
                })
                ->editColumn('status', function ($row) {
                    if($row->status == 'active'){
                        return '<label class="badge badge-success">'.__("app.active").'</label>';
                    }
                    elseif($row->status == 'inactive'){
                        return '<label class="badge badge-danger">'.__("app.inactive").'</label>';
                    }
                })
                ->editColumn('usage', function ($row) {
                    $used_time = $row->used_time; $uses_limit = $row->uses_limit;
                    if($used_time==''){
                        $used_time = 0;
                    }
                    if($uses_limit==0){
                        $uses_limit = '&infin;';
                    }
                    return $used_time.'/'.$uses_limit;
                })
                ->addColumn('deal_location', function ($row) {
                    // return $row->outlet->outlet_name;
                    return $row->outlet_name;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'status', 'usage'])
                ->toJson();
        }

        return view('admin.promotion.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_promotion'), 403);

        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $outlets  = Outlet::where('status', 'active')->get();
        $services = BusinessService::where('status', 'active')->get();
        return view('admin.promotion.create', compact('days', 'outlets','services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_promotion'), 403);

        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'applied_between_dates' => 'required',
            'open_time' => 'required',
            'close_time' => 'required',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
            'feature_image' => 'nullable',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'gender' => 'required',
            'customer_uses_time' => 'required',
            'uses_time' => 'nullable',
            'choice' => 'required',
            'services' => 'required',
            'locations' => 'required',
            'discount' => 'required'
        ]);
        
        if(!$request->has('days')){
            return Reply::error( __('messages.coupon.selectDay'));
        }

        $services = $request->services;
        $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->deal_startDate)->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('Y-m-d H:i a', $request->deal_endDate)->format('Y-m-d H:i:s');
        $startTime = Carbon::createFromFormat('H:i a', $request->deal_startTime)->format('H:i:s');
        $endTime  = Carbon::createFromFormat('H:i a', $request->deal_endTime)->format('H:i:s');

        $promotion = new Promotion();
        $promotion->title                   = $request->title;
        $promotion->slug                    = $request->slug;
        $promotion->start_date_time         = $startDate;
        $promotion->end_date_time           = $endDate;
        $promotion->open_time               = $startTime;
        $promotion->close_time              = $endTime;
        $promotion->max_order_per_customer  = $request->customer_uses_time;
        $promotion->status                  = $request->status;
        $promotion->days                    = json_encode($request->days);
        $promotion->description             = $request->description;
        // $promotion->outlet_id               = $request->locations;
        $promotion->deal_applied_on         = $request->choice;
        $promotion->discount_type           = $request->discount_type;
        $promotion->percentage              = $request->discount;
        $promotion->uses_limit              = $request->uses_time;
        $promotion->min_age                 = $request->min_age;
        $promotion->max_age                 = $request->max_age;

        if(sizeof($services)>1){
            $promotion->deal_type           = 'Combo';
        }

        if ($request->hasFile('feature_image')) {
            $promotion->image = Files::upload($request->feature_image,'promotion');
        }

        /* Save deal */
        $deal_services = $request->deal_services;
        $prices = $request->deal_unit_price;
        $quantity = $request->deal_quantity;
        $discount = $request->deal_discount;

        $discountAmount = 0;
        $amountToPay    = 0;
        $originalAmount = 0;
        $promotion_items = array();

        foreach ($deal_services as $key=>$service){
            $amount = ($quantity[$key] * $prices[$key]);
            // $unit_price = ($amount-$discount[$key])/$quantity[$key]; /* calculate unit price after deal price ie. after apply deal */
            $promotion_items[] = [
                "business_service_id"   => $deal_services[$key],
                "quantity"              => $quantity[$key],
                // "unit_price"            => $unit_price,
                "unit_price"            => $prices[$key],
                "discount_amount"       => $discount[$key],
                "total_amount"          => $amount-$discount[$key],
            ];
            $originalAmount = ($originalAmount + $amount);
            $discountAmount = ($discountAmount + $discount[$key]);
        }
        $amountToPay = $originalAmount-$discountAmount;

        $promotion->deal_amount             = $amountToPay;
        $promotion->original_amount         = $originalAmount;

        $promotion->save();

        /* Save promotion items */
        for($i=0; $i<count($promotion_items); $i++)
        {
            $promotion_items[$i]['promotion_id'] = $promotion->id;
        }
        DB::table('promotion_items')->insert($promotion_items);

        // promotion outlet start

        if($request->filled('locations'))
        {
            $new_outlet_id = $this->store_promotion_outlet($request, $promotion->id);

            $promotion->outlet_id = implode(',', $new_outlet_id);
            $promotion->save();
        }

        // promotion outlet end

        // promotion gender start

        if($request->filled('gender'))
        {
            $new_gender = $this->store_promotion_gender($request, $promotion->id);

            $promotion->gender = implode(',', $new_gender);
            $promotion->save();
        }

        // promotion gender end

        return Reply::redirect(route('admin.promotion.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_promotion'), 403);

        // $deal = Promotion::with('outlet')->findOrFail($id);
        $deal = Promotion::findOrFail($id);
        $deal_items = PromotionItem::with('businessService')->where('promotion_id', $id)->get();

        if($deal->days){
            $days = json_decode($deal->days);
        }
        $locations = $deal->locations;

        return view('admin.promotion.show', compact('deal', 'days', 'locations', 'deal_items'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    //     $selectedLocations = [];

    //     $deal = Promotion::with('outlet')->findOrFail($id);
    //     $selectedDays = json_decode($deal->days);
    //     $deal->selected_Gender = explode(",", $deal->gender);

    //     $services = BusinessService::all();
    //     $deal_services = PromotionItem::where('promotion_id', $id)->pluck('business_service_id')->toArray();

    //     $deal_items = PromotionItem::with('businessService', 'promotion')->where('promotion_id', $id)->get();
    //     $deal_items_table = view('admin.promotion.promotion_items_edit', compact('deal_items'))->render();

    //     $outlets  = Outlet::where('status', 'active')->get();

    //     return view('admin.promotion.edit', compact('days', 'deal', 'selectedDays', 'services', 'deal_services', 'deal_items_table', 'outlets'));
    // }

    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_promotion'), 403);

        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $selectedLocations = [];

        $deal = Promotion::findOrFail($id);
        $selectedDays = json_decode($deal->days);
        $deal->selected_Gender = explode(",", $deal->gender);

        $services = BusinessService::all();
        $deal_services = PromotionItem::where('promotion_id', $id)->pluck('business_service_id')->toArray();

        $deal_items = PromotionItem::with('businessService', 'promotion')->where('promotion_id', $id)->get();
        $deal_items_table = view('admin.promotion.promotion_items_edit', compact('deal_items'))->render();

        $outlets  = Outlet::all();
        $deal_outlets = DB::table('promotion_outlets')->where('promotion_id', $id)->pluck('outlet_id')->toArray();

        return view('admin.promotion.edit', compact('days', 'deal', 'selectedDays', 'services', 'deal_services', 'deal_items_table', 'outlets', 'deal_outlets'));
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
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_promotion'), 403);

        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'applied_between_dates' => 'required',
            'open_time' => 'required',
            'close_time' => 'required',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
            'feature_image' => 'nullable',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'gender' => 'required',
            'customer_uses_time' => 'required',
            'uses_time' => 'nullable',
            'choice' => 'required',
            'services' => 'required',
            'locations' => 'required',
            'discount' => 'required'
        ]);

        if(!$request->has('days')){
            return Reply::error( __('messages.coupon.selectDay'));
        }

        /* delete all items from deal_items table */
        DB::table('promotion_items')->where('promotion_id', $id)->delete();

        $services = $request->services;
        $startDate = Carbon::createFromFormat('Y-m-d H:i a', $request->deal_startDate)->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('Y-m-d H:i a', $request->deal_endDate)->format('Y-m-d H:i:s');
        $startTime = Carbon::createFromFormat('H:i a', $request->deal_startTime)->format('H:i:s');
        $endTime  = Carbon::createFromFormat('H:i a', $request->deal_endTime)->format('H:i:s');

        $promotion = Promotion::findOrFail($id);
        $promotion->title                   = $request->title;
        $promotion->slug                    = $request->slug;
        $promotion->start_date_time         = $startDate;
        $promotion->end_date_time           = $endDate;
        $promotion->open_time               = $startTime;
        $promotion->close_time              = $endTime;
        $promotion->max_order_per_customer  = $request->customer_uses_time;
        $promotion->status                  = $request->status;
        $promotion->days                    = json_encode($request->days);
        $promotion->description             = $request->description;
        // $promotion->outlet_id               = $request->locations;
        $promotion->deal_applied_on         = $request->choice;
        $promotion->discount_type           = $request->discount_type;
        $promotion->percentage              = $request->discount;
        $promotion->min_age                 = $request->min_age;
        $promotion->max_age                 = $request->max_age;
        $promotion->uses_limit              = $request->uses_time;

        if(sizeof($services)>1){
            $promotion->deal_type           = 'Combo';
        }

        if ($request->hasFile('feature_image')) {
            Files::deleteFile($promotion->image, 'promotion');
            $promotion->image = Files::upload($request->feature_image,'promotion');
        }

        /* Save deal */
        $deal_services = $request->deal_services;
        $prices = $request->deal_unit_price;
        $quantity = $request->deal_quantity;
        $discount = $request->deal_discount;

        $discountAmount = 0;
        $amountToPay    = 0;
        $originalAmount = 0;
        $promotion_items = array();

        foreach ($deal_services as $key=>$service){
            $amount = ($quantity[$key] * $prices[$key]);
            $promotion_items[] = [
                "business_service_id"   => $deal_services[$key],
                "quantity"              => $quantity[$key],
                "unit_price"            => $prices[$key],
                "discount_amount"       => $discount[$key],
                "total_amount"          => $amount-$discount[$key],
            ];
            $originalAmount = ($originalAmount + $amount);
            $discountAmount = ($discountAmount + $discount[$key]);
        }

        $amountToPay = $originalAmount-$discountAmount;

        $promotion->deal_amount             = $amountToPay;
        $promotion->original_amount         = $originalAmount;

        $promotion->save();

        /* Save deal items */
        for($i=0; $i<count($promotion_items); $i++)
        {
            $promotion_items[$i]['promotion_id'] = $promotion->id;
        }
        DB::table('promotion_items')->insert($promotion_items);

        // promotion outlet start

        if($request->filled('locations'))
        {
            DB::table('promotion_outlets')->where('promotion_id', $promotion->id)->delete();

            $new_outlet_id = $this->store_promotion_outlet($request, $promotion->id);

            $promotion->outlet_id = implode(',', $new_outlet_id);
            $promotion->save();
        }

        // promotion outlet end

        // promotion gender start

        if($request->filled('gender'))
        {
            DB::table('promotion_gender')->where('promotion_id', $promotion->id)->delete();

            $new_gender = $this->store_promotion_gender($request, $promotion->id);

            $promotion->gender = implode(',', $new_gender);
            $promotion->save();
        }

        // promotion gender end

        return Reply::redirect(route('admin.promotion.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_promotion'), 403);

        $Promotion = Promotion::findOrFail($id);

        if($Promotion)
        {
            PromotionItem::where('promotion_id', $id)->delete();
            DB::table('promotion_gender')->where('promotion_id', $id)->delete();
            DB::table('promotion_outlets')->where('promotion_id', $id)->delete();

            Files::deleteFile($Promotion->image, 'promotion');
        }

        $Promotion->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

    // public function selectLocation(Request $request)
    // {
    //     $services           = $request->services;
    //     $result_locations   = [];
    //     $selected_location  = '';
    //     $result_array       = [];

    //     for($key=0;$key<count($services);$key++)
    //     {
    //         $ser2 = BusinessService::with('outlet')->where('id', $services[$key])->get();
    //         foreach ($ser2 as $key2 => $value2)
    //         {
    //             $result_locations[] =  $value2->outlet_id;
    //         }
    //     }

    //     $array2 = array_count_values($result_locations);

    //     foreach ($array2 as $k => $v)
    //     {      
    //         // if($array2[$k]==sizeof($services))
    //         // {
    //         //     $result_array[] = $k;
    //         // }

    //         $result_array[] = $k;
    //     }

    //     $locations = Outlet::whereIn('id', $result_array)->get();
    //     foreach ($locations as $location)
    //     {
    //         $selected_location .= '<option value="'.$location->id.'">'.$location->outlet_name.'</option>';
    //     }

    //     return response()->json(['selected_location' => $selected_location]);

    // } /* end of selectLocation() */

    public function selectLocation(Request $request)
    {
        $services           = $request->services;
        $result_locations   = [];
        $selected_location  = '';
        $result_array       = [];

        if(isset($services))
        {
            for($key=0;$key<count($services);$key++)
            {
                // $ser2 = BusinessService::with('outlet')->where('id', $services[$key])->get();
                $ser2 = DB::table('business_services_outlets')->where('business_service_id', $services[$key])->get();
                
                foreach ($ser2 as $key2 => $value2)
                {
                    $result_locations[] =  $value2->outlet_id;
                }
            }
        }

        $array2 = array_count_values($result_locations);

        foreach ($array2 as $k => $v)
        {      
            // if($array2[$k]==sizeof($services))
            // {
            //     $result_array[] = $k;
            // }

            $result_array[] = $k;
        }

        $locations = Outlet::whereIn('id', $result_array)->get();
        foreach ($locations as $location)
        {
            $selected_location .= '<option value="'.$location->id.'">'.$location->outlet_name.'</option>';
        }

        return response()->json(['selected_location' => $selected_location]);

    } /* end of selectLocation() */

    // public function selectServices(Request $request)
    // {
    //     $location = $request->locations;
    //     $selected_service = '';

    //     $locations = Outlet::with('services')->whereIn('id', $location)->get();
    //     foreach ($locations as $key2 => $location){
    //         foreach($location->services as $service){
    //             // $selected_service .= "<option value='".$service->name."'>".$service->name."</option>";
    //             $selected_service .= "<option value='".$service->id."'>".$service->name."</option>";
    //         }
    //     }
    //     return response()->json(['selected_service' => $selected_service]);
    // } /* end of selectServices() */

    public function selectServices(Request $request)
    {
        $location = $request->locations;
        $selected_service = '';
        $uniqueServices = collect();

        if(isset($location))
        {
            // $locations = Outlet::with('services')->whereIn('id', $location)->get();
            $locations = Outlet::with('manyServices')->whereIn('id', $location)->get();

            if (!$locations->isEmpty()) 
            {
                foreach ($locations as $item) 
                {
                    // foreach($item->services as $service){
                    foreach ($item->manyServices as $service) 
                    {
                        if (!$uniqueServices->contains($service->id)) 
                        {
                            $uniqueServices->push($service->id);
                            // $selected_service .= "<option value='".$service->id."'>".$service->name." (".$service->time." ".$service->time_type.")</option>";
                            $selected_service .= "<option value='".$service->id."'>(".$service->time." ".$service->time_type.") ".$service->name."</option>";
                        }
                    }
                }
            }
        }

        return response()->json(['selected_service' => $selected_service]);
    }

    public function resetSelection()
    {
        $all_services_array = '<option value="">Select Services</option>';
        $services = BusinessService::where('status', 'active')->get();
        foreach ($services as $service)
        {
            $all_services_array .= '<option value="'.$service->id.'">'.$service->name.'</option>';
        }

        $all_locations_array = '<option value="">Select Outlet</option>';
        $locations = Outlet::where('status', 'active')->get();
        foreach ($locations as $location)
        {
            $all_locations_array .= '<option value="'.$location->id.'">'.$location->outlet_name.'</option>';
        }

        return response()->json(['all_locations_array' => $all_locations_array, 'all_services_array' => $all_services_array]);
    } /* end of resetSelection()  */


    // public function makeDeal(Request $request)
    // {
    //     $services = $request->services;
    //     $location = $request->locations;

    //     $deal_list = BusinessService::whereIn('id', $services)->with('outlet')
    //     ->whereHas('outlet', function($query) use($location){
    //         $query->whereIn('id', $location);
    //     })->get();

    //     $view = view('admin.promotion.promotion_items', compact('deal_list'))->render();

    //     return response()->json(['view' => $view]);
    // }

    public function makeDeal(Request $request)
    {
        // return $request->all();

        $services = $request->services;
        $location = $request->locations;

        // $deal_list = BusinessService::whereIn('id', $services)->with('outlet')
        // ->whereHas('outlet', function($query) use($location){
        //     $query->whereIn('id', $location);
        // })->get();

        $deal_list = BusinessService::whereIn('business_services.id', $services)->with('manyOutlets')
        ->whereHas('manyOutlets', function($query) use($location){
            $query->whereIn('outlets.id', $location);
        })->get();

        $view = view('admin.promotion.promotion_items', compact('deal_list'))->render();

        return response()->json(['view' => $view]);
    }

    public function new()
    {
        
    }

    public static function store_promotion_outlet($request, $promotion_id)
    {
        $outlet_id = $request->locations;

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

        $promotion_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $promotion_outlets[] = [
                'promotion_id' => $promotion_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('promotion_outlets')->insert($promotion_outlets);

        return $new_outlet_id;
    }

    public static function store_promotion_gender($request, $promotion_id)
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

        $promotion_gender = [];

        foreach($new_gender as $item)
        {
            $promotion_gender[] = [
                'promotion_id' => $promotion_id,
                'gender' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('promotion_gender')->insert($promotion_gender);

        return $new_gender;
    }
}
