<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\Category;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Category\StoreCategory;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.categories'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_category'), 403);
        
        // $category_notif_status = NotificationSettingController::get_notification_settings('category_notif_status') ?? false;
        // dd($category_notif_status);

        if(\request()->ajax()){
            $categories = Category::orderBy('order_level', 'desc')->get();

            // Initialize $category_notif_status to avoid undefined variable error
            $category_notif_status = NotificationSettingController::get_notification_settings('category_notif_status') ?? false;

            return \datatables()->of($categories)
                ->addColumn('action', function ($row) use ($category_notif_status) {
                    $action = '';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_category')) {
                        $action.= '<a href="' . route('admin.categories.edit', [$row->id]) . '" class="btn btn-primary btn-circle mr-1"
                        data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }
                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_category')) {
                        $action.= '<a href="javascript:;" class="btn btn-danger btn-circle delete-row mr-1"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    if($category_notif_status == true)
                    {
                        $action .= '<a href="javascript:;" class="btn btn-success btn-circle send_notification_btn"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="Send Push Notification"><i class="fa fa-bell" aria-hidden="true"></i></a> ';
                    }
                    
                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="'.$row->category_image_url.'" class="img" height="65em" width="65em" /> ';
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
                ->addColumn('hidden_input', function ($row) {
                    return '<input type="hidden" name="category_id[]" value="' . $row->id . '">';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'status', 'hidden_input'])
                ->setRowAttr([
                    'class' => 'ui-sortable-handle',
                    'data-id' => function($row) {
                        return $row->id;
                    }
                ])
                ->toJson();
        }
        return view('admin.category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_category'), 403);

        $outlets = Outlet::where('status', 'active')->get();

        $data['outlets'] = $outlets;

        return view('admin.category.create', $data);
    }

    /**
     * @param StoreCategory $request
     * @return array
     * @throws \Exception
     */
    public function store(StoreCategory $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_category'), 403);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = $request->slug;
        if ($request->hasFile('image')) {
            $category->image = Files::upload($request->image,'category');
        }

        if($request->filled('is_loyalty_program'))
        {
            $category->is_loyalty_program = 1;
        }
        else
        {
            $category->is_loyalty_program = 2;
        }

        $category->save();

        // category outlet start

        if($request->filled('outlet_id'))
        {
            $new_outlet_id = $this->store_category_outlet($request, $category->id);
        }

        // category outlet end

        return Reply::redirect($request->redirect_url, __('messages.createdSuccessfully'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_category'), 403);

        $outlets = Outlet::where('status', 'active')->get();
        $selectedOutlets = DB::table('category_outlets')->where('category_id', $category->id)->pluck('outlet_id')->toArray();

        return view('admin.category.edit', compact('category', 'outlets', 'selectedOutlets'));
    }

    /**
     * @param StoreCategory $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function update(StoreCategory $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_category'), 403);

        $category = Category::find($id);
        $category->name = $request->name;
        $category->status = $request->status;
        $category->slug = $request->slug;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            Files::deleteFile($category->image, 'category');
            $category->image = Files::upload($request->image,'category');
        }

        if($request->filled('is_loyalty_program'))
        {
            $category->is_loyalty_program = 1;
        }
        else
        {
            $category->is_loyalty_program = 2;
        }
        
        $category->save();

        //update business servicess status for the category
        BusinessService::where('category_id', $id)->update(['status' => $request->status]);

        // category outlet start

        if($request->filled('outlet_id'))
        {
            DB::table('category_outlets')->where('category_id', $category->id)->delete();

            $new_outlet_id = $this->store_category_outlet($request, $category->id);
        }

        // category outlet end

        return Reply::redirect(route('admin.categories.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_category'), 403);

        // Category::destroy($id);

        $Category = Category::findOrFail($id);

        if($Category)
        {
            // Delete old image if exists
            Files::deleteFile($Category->image, 'category');
            DB::table('category_outlets')->where('category_id', $id)->delete();
        }

        $Category->delete();

        return Reply::success(__('messages.recordDeleted'));
    }


    public static function store_category_outlet($request, $category_id)
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

        $category_id_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $category_id_outlets[] = [
                'category_id' => $category_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('category_outlets')->insert($category_id_outlets);

        return $new_outlet_id;
    }

    public function get_outlets_by_single_categoryId(Request $request)
    {
        $category_outlets_id = DB::table('category_outlets')->where('category_id', $request->category_id)->pluck('outlet_id')->toArray();
    
        $outlets = Outlet::whereIn('id', $category_outlets_id)->where('status', 'active')->get();
        
        $data['outlets'] = $outlets;

        return response()->json($data);
    }

    public function sort_update(Request $request)
    {
        // return $request->all();

        $category_id = $request->category_id;

        $j = count($category_id);

        for($i=0; $i<count($category_id); $i++)
        {
            // $arr[] = [
            //     'id' => $category_id[$i],
            //     'order_level' => $j
            // ];

            $category = Category::find($category_id[$i]);
            $category->order_level = $j;
            $category->save();

            $j--;
        }

        // return $arr;

        return back()->with('success', __('messages.updatedSuccessfully'));
    }


    public function send_notification(Request $request)
    {
        // return $request->all();

        $category_id = $request->category_id;

        $category = Category::find($category_id);

        if($category)
        {
            $customers = User::allCustomers()->get();

            return NotificationController::send_notification($request, $customers, $category_id, 'category_id', 'category');

            // return response()->json(['status' => 'success', 'message' => 'Notification send successfully']);
        }
        else
        {
            return response()->json(['status' => 'failed', 'message' => 'Category not found']);
        }
    }

    // get_categories_by_single_outlet

    public function get_categories_by_single_outlet(Request $request)
    {
        // return $request->all();

        $outlet_id = $request->outlet_id  ?? '';

        $category_id_arr = DB::table('category_outlets')->where('outlet_id', $outlet_id)->pluck('category_id')->toArray();

        $categories_loyalty_program = Category::where('status', 'active')
                                                ->where('is_loyalty_program', 1)
                                                ->whereIn('id', $category_id_arr)
                                                ->orderBy('order_level', 'desc')
                                                ->get();

        $data['categories_loyalty_program'] = $categories_loyalty_program;

        return response()->json($data);
    } 

}
