<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\Outlet;
use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OutletController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.outlet'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_outlet'), 403);

        if (request()->ajax()) 
        {
            if(Session::has('outlet_slug') || Session::has('outlet_id'))
            {
                $outlets = Outlet::where('id', Session::get('outlet_id'))->get();
            }
            else
            {
                $outlets = Outlet::get();
            }     

            return datatables()->of($outlets)
                ->addColumn('action', function ($row) {
                    $action = '';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_outlet')) {
                        $action .= '<a href="' . route('admin.outlet.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                            data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_outlet')) {
                        $action .= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-outlet"
                        data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_outlet')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    $imagePath = asset('user-uploads/outlet_images/' . $row->image);
                    return '<img src="' . $imagePath . '" class="img" height="65em" width="65em"/> ';
                })
                ->editColumn('outlet_name', function ($row) {
                    return ucfirst($row->outlet_name);
                })
                ->editColumn('login_url', function ($row) {
                    $loginUrl = Str::limit($row->login_url, 30, '...'); // Limit to 30 characters
                    return '<a href="' . $row->login_url . '" target="_blank" title="' . $row->login_url . '">' . $loginUrl . '</a>';
                })
                // ->editColumn('pos_url', function ($row) {
                //     $posUrl = Str::limit($row->pos_url, 30, '...'); // Limit to 30 characters
                //     return '<a href="' . $row->pos_url . '" target="_blank" title="' . $row->pos_url . '">' . $posUrl . '</a>';
                // })
                ->editColumn('kiosk_url', function ($row) {
                    $kioskUrl = Str::limit($row->kiosk_url, 30, '...'); // Limit to 30 characters
                    return '<a href="' . $row->kiosk_url . '" target="_blank" title="' . $row->kiosk_url . '">' . $kioskUrl . '</a>';
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="badge badge-success">' . __("app.active") . '</label>';
                    } elseif ($row->status == 'inactive') {
                        return '<label class="badge badge-danger">' . __("app.inactive") . '</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'outlet_name', 'login_url', 'kiosk_url', 'status'])
                ->toJson();
        }

        return view('admin.outlet.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_outlet'), 403);

        return view('admin.outlet.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_outlet'), 403);

        // Validate the incoming request
        $request->validate([
            'outlet_name' => 'required|string|unique:outlets,outlet_name',
            'outlet_description' => 'required',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable',  // Image is now required
            'address' => 'nullable|string',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'phone' => 'nullable|digits:8',
            'whatsapp_no' => 'nullable|digits:8',
            'open_time' => 'nullable|string',
            'close_time' => 'nullable|string',
        ]);        

        // Format outlet_name for URL-safe characters (optional, more robust sanitization)
        $outlet_slug = strtolower(str_replace(' ', '_', $request->outlet_name));

        if (!Outlet::where('outlet_slug', $outlet_slug)->exists()) {
            // Create a new Outlet instance
            $outlet = new Outlet;

            // Populate outlet data
            $outlet->outlet_name = $request->outlet_name;
            $outlet->outlet_description = $request->outlet_description;
            $outlet->outlet_slug = $outlet_slug;
            $outlet->latitude = $request->latitude;
            $outlet->longitude = $request->longitude;
            $outlet->status = $request->status;
            $outlet->address = $request->address;
            $outlet->phone = $request->phone;
            $outlet->whatsapp_no = $request->whatsapp_no;
            $outlet->open_time = $request->open_time;
            $outlet->close_time = $request->close_time;

            if ($request->hasFile('image')) 
            {
                // Upload the image file
                $outlet->image = Files::upload($request->file('image'), 'outlet_images');
            }

            // Generate the URLs based on outlet_name
            $outlet->login_url = asset('/outlet/' . $outlet_slug . '/login');
            $outlet->pos_url = asset('/outlet/pos/' . $outlet_slug . '/login');
            $outlet->kiosk_url = asset('/outlet/kiosk/' . $outlet_slug . '/login');

            // Save the outlet data to the database
            $outlet->save();

            return Reply::redirect(route('admin.outlet.index'), __('messages.createdSuccessfully'));
        } else {
            return Reply::error("The outlet name has already been taken.");
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_outlet'), 403);
        
        if(Session::has('outlet_slug') || Session::has('outlet_id'))
        {
            if(Session::get('outlet_id') != $id)
            {
                abort(404);
            }
        }
        
        $outlet = Outlet::findOrFail($id);
        $outlet_services_id = DB::table('business_services_outlets')->where('outlet_id', $id)->pluck('business_service_id')->toArray();
        $outlet_services = BusinessService::whereIn('id', $outlet_services_id)->pluck('name')->toArray();
        $outlet_services_name = implode(', ', $outlet_services);

        $outlet->outlet_services_name = $outlet_services_name;

        // return $outlet;

        return view('admin.outlet.show', compact('outlet'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_outlet'), 403);

        if(Session::has('outlet_slug') || Session::has('outlet_id'))
        {
            if(Session::get('outlet_id') != $id)
            {
                abort(404);
            }
        }
  
        $outlet = Outlet::findOrFail($id);

        return view('admin.outlet.edit', compact('outlet'));
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
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_outlet'), 403);

        $request->validate([
            'outlet_name' => 'required|string|unique:outlets,outlet_name,' . $id,
            'outlet_description' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable',  // Image is now required
            'address' => 'nullable|string',
            'phone' => 'nullable|digits:8',
            'whatsapp_no' => 'nullable|digits:8',
            'open_time' => 'nullable|string',
            'close_time' => 'nullable|string',
        ]);

        // Find the outlet by ID
        $outlet = Outlet::findOrFail($id);

        // Format the outlet_name for consistency
        $outlet_slug = strtolower(str_replace(' ', '_', $request->outlet_name));
        if (!Outlet::where('outlet_slug', $outlet_slug)->where('id','!=',$id)->exists()) {

            // Update outlet details
            $outlet->outlet_name = $request->outlet_name;
            $outlet->outlet_description = $request->outlet_description;
            $outlet->outlet_slug = $outlet_slug;
            $outlet->latitude = $request->latitude;
            $outlet->longitude = $request->longitude;
            $outlet->status = $request->status;
            $outlet->address = $request->address;
            $outlet->phone = $request->phone;
            $outlet->whatsapp_no = $request->whatsapp_no;
            $outlet->open_time = $request->open_time;
            $outlet->close_time = $request->close_time;

            // If there's a new image, upload and delete the old one
            if ($request->hasFile('image')) {
                // Delete old image if exists
                Files::deleteFile($outlet->image, 'outlet_images');
                // Upload new image
                $outlet->image = Files::upload($request->file('image'), 'outlet_images');
            }

            // Generate the URLs based on the outlet_name
            $outlet->login_url = asset('/outlet/' . $outlet_slug . '/login');
            $outlet->pos_url = asset('/outlet/pos/' . $outlet_slug . '/login');
            $outlet->kiosk_url = asset('/outlet/kiosk/' . $outlet_slug . '/login');

            // Save the updated outlet
            $outlet->save();

            return Reply::redirect(route('admin.outlet.index'), __('messages.updatedSuccessfully'));
        } else {
            return Reply::error("The outlet name has already been taken.");
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_outlet'), 403);

        if(Session::has('outlet_slug') || Session::has('outlet_id'))
        {
            if(Session::get('outlet_id') != $id)
            {
                abort(404);
            }
        }

        $outlet = Outlet::findOrFail($id);

        Files::deleteFile($outlet->image, 'outlet_images');

        $outlet->delete();

        return Reply::success(__('messages.recordDeleted'));
    }
}
