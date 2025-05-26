<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoyaltyShop;
use App\LoyaltyShopRedeem;
use App\LoyaltyShopUsage;
use App\Outlet;
use App\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', 'Products');
    }

    public function index(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_product'), 403);

        if(\request()->ajax())
        {
            $products = Product::orderBy('created_at', 'asc')->get();
            
            return \datatables()->of($products)
                ->addColumn('action', function ($row) {
                    $action = '';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_product')) 
                    {
                        $action.= '<a href="' . route('admin.products.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                            data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_product')) 
                    {
                        $action.= '<a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view_product ml-1"
                            data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    }

                    // $action.= ' <a href="javascript:;" class="btn btn-warning btn-circle duplicate-row"
                    // data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.duplicate').'"><i class="fa fa-clone" aria-hidden="true"></i></a>';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_product')) 
                    {
                        $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="'.$row->product_image_url.'" class="img" height="65em" width="65em" /> ';
                })
                ->editColumn('product_name', function ($row) {
                    return ucfirst($row->product_name);
                })
                ->editColumn('status', function ($row) {
                    if($row->status == 'active'){
                        return '<label class="badge badge-success">'.__("app.active").'</label>';
                    }
                    elseif($row->status == 'deactive'){
                        return '<label class="badge badge-danger">'.__("app.deactive").'</label>';
                    }
                })
                ->editColumn('price', function ($row) {
                    return $row->price;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'status'])
                ->toJson();
        }

        return view('admin.product.index');
    }

    public function create(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_product'), 403);

        $outlets = Outlet::orderBy('outlet_name', 'ASC')->get();

        $data = [
            'outlets' => $outlets,
        ];

        return view('admin.product.create', $data);
    }

    public function store(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_product'), 403);

        // return $request->all();

        $request->validate([
            'product_name' => 'required|string|max:255|unique:products,product_name',
            'slug' => 'required|string|max:255|unique:products,slug',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'loyalty_point' => 'required|integer|min:0',
            'outlet_id' => 'required|array',
            'status' => 'required|in:active,deactive',
        ]);

        $product = new Product();
        $product->product_name = $request->product_name;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->slug = $request->slug;
        $product->loyalty_point = $request->loyalty_point;
        $product->status = $request->status;
        $product->save();

        // products outlet start

        if($request->filled('outlet_id'))
        {
            $new_outlet_id = $this->store_product_outlet($request, $product->id);

            $product->outlet_id = implode(',', $new_outlet_id);
            $product->save();
        }

        // products outlet end

        return Reply::dataOnly(['product_id' => $product->id]);
    }

    public function store_images(Request $request) 
    {
        if ($request->hasFile('file')) 
        {
            $product = Product::where('id', $request->product_id)->first();
            $product_images_arr = [];

            foreach ($request->file as $fileData) {
                array_push($product_images_arr, Files::upload($fileData, 'product/'.$product->id));
            }

            $product->image = json_encode($product_images_arr);
            $product->default_image = $product_images_arr[0];
            $product->save();
        }

        return Reply::redirect(route('admin.products.index'), __('messages.createdSuccessfully'));
    }

    public function edit(Request $request, $product_id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_product'), 403);

        $product = Product::findOrFail($product_id);

        if (!$product) {
            return Reply::error(__('messages.somethingWentWrong'));
        }

        $images = [];

        if ($product->image && is_iterable($product->image)) 
        {
            foreach ($product->image as $image) 
            {
                $filePath = public_path('user-uploads/product/' . $product->id . '/' . $image);
                
                if (file_exists($filePath)) 
                {
                    $reqImage['name'] = $image;
                    $reqImage['size'] = filesize($filePath);
                    $reqImage['type'] = mime_content_type($filePath);
                    $images[] = $reqImage;
                }

                // $reqImage['name'] = $image;
                // $reqImage['size'] = filesize(public_path('/user-uploads/product/'.$product->id.'/'.$image));
                // $reqImage['type'] = mime_content_type(public_path('/user-uploads/product/'.$product->id.'/'.$image));
                // $images[] = $reqImage;
            }
        }

        $images = json_encode($images);

        // outlets start

        $outlets = Outlet::orderBy('outlet_name', 'ASC')->get();   

        $selectedOutlets = DB::table('product_outlets')->where('product_id', $product_id)->pluck('outlet_id')->toArray();

        // outlets end

        $data = [
            'product' => $product,
            'outlets' => $outlets,
            'selectedOutlets' => $selectedOutlets,
            'images' => $images,
        ];

        // return $data;

        return view('admin.product.edit', $data);
    }

    public function update(Request $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_product'), 403);

        // return $request->all();

        $request->validate([
            'product_name' => 'required|string|max:255|unique:products,product_name,'.$request->id,
            'slug' => 'required|string|max:255|unique:products,slug,'.$request->id,
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'loyalty_point' => 'required|integer|min:0',
            'outlet_id' => 'required|array',
            'status' => 'required|in:active,deactive',
        ]);

        $product = Product::find($id);
        $product->product_name = $request->product_name;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->status = $request->status;
        $product->slug = $request->slug;
        $product->loyalty_point = $request->loyalty_point;
        $product->save();

        // product outlet start

        if($request->filled('outlet_id'))
        {
            DB::table('product_outlets')->where('product_id', $product->id)->delete();

            $new_outlet_id = $this->store_product_outlet($request, $product->id);

            $product->outlet_id = implode(',', $new_outlet_id);
            $product->save();
        }

        // product outlet end

        return Reply::dataOnly(['product_id' => $product->id, 'defaultImage' => $request->default_image ?? 0]);
    }

    public function update_images(Request $request) 
    {
        $product = Product::where('id', $request->product_id)->first();

        $product_images_arr = [];
        $default_image_index = 0;

        if ($request->hasFile('file')) 
        {
            if ($request->file[0]->getClientOriginalName() !== 'blob') {
                foreach ($request->file as $fileData) {
                    array_push($product_images_arr, Files::upload($fileData, 'product/'.$product->id));
                    if ($fileData->getClientOriginalName() == $request->default_image) {
                        $default_image_index = array_key_last($product_images_arr);
                    }
                }
            }
            if ($request->uploaded_files) {
                $files = json_decode($request->uploaded_files, true);
                foreach ($files as $file) {
                    array_push($product_images_arr, $file['name']);
                    if ($file['name'] == $request->default_image) {
                        $default_image_index = array_key_last($product_images_arr);
                    }
                }
                $arr_diff = array_diff($product->image, $product_images_arr);

                if (sizeof($arr_diff) > 0) {
                    foreach ($arr_diff as $file) {
                        Files::deleteFile($file, 'product/'.$product->id);
                    }
                }
            }
            else {
                if (!is_null($product->image) && sizeof($product->image) > 0) {
                    foreach ($product->image as $file) {
                        Files::deleteFile($file, 'product/'.$product->id);
                    }
                    // Files::deleteFile($product->image[0], 'product/'.$product->id);
                }
            }
        }

        $product->image = json_encode(array_values($product_images_arr));
        $product->default_image = sizeof($product_images_arr) > 0 ? $product_images_arr[$default_image_index] : null;
        $product->save();

        return Reply::redirect(route('admin.products.index'), __('messages.updatedSuccessfully'));
    }

    public static function store_product_outlet($request, $product_id)
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

        $product_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $product_outlets[] = [
                'product_id' => $product_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('product_outlets')->insert($product_outlets);

        return $new_outlet_id;
    }

    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_product'), 403);

        $product = Product::findOrFail($id);

        if (!$product) {
            return Reply::error(__('messages.somethingWentWrong'));
        }

        $data = [
            'product' => $product,
        ];

        return view('admin.product.show', $data);
    }

    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_product'), 403);

        $product = Product::findOrFail($id);

        if($product)
        {
            // Delete old image if exists
            foreach ($product->image as $file) {
                Files::deleteFile($file, 'product/'.$product->id);
            }

            DB::table('product_outlets')->where('product_id', $id)->delete();

            // loyalty shop product delete start

            if(LoyaltyShop::where('product_id', $id)->exists())
            {
                $LoyaltyShop = LoyaltyShop::where('product_id', $id)->get();

                foreach($LoyaltyShop as $item)
                {
                    if ($item->image) 
                    {
                        Files::deleteFile($item->image, 'loyalty-shop');
                    }
                }

                $loyalty_shop_id_arr = LoyaltyShop::where('product_id', $id)->pluck('id')->toArray();
                
                DB::table('loyalty_shop_users')->whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
                DB::table('loyalty_shop_gender')->whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
                DB::table('loyalty_shop_outlets')->whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
                
                LoyaltyShopRedeem::whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
                LoyaltyShopUsage::whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();

                LoyaltyShop::whereIn('loyalty_shop_id', $loyalty_shop_id_arr)->delete();
            }

            // loyalty shop product delete end
        }

        $product->delete();

        // BusinessService::destroy($id);

        return Reply::success(__('messages.recordDeleted'));
    }
}
