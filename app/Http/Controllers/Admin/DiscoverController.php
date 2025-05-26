<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Coupon;
use App\CouponUser;
use App\Discover;
use App\Happening;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Controller;

class DiscoverController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.discover'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_discover'), 403);

        if (request()->ajax()) {
            $happening = Discover::get();
    
            return datatables()->of($happening)
                ->addColumn('action', function ($row) {
                    $action = '';
    
                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_discover')) {
                        $action .= '<a href="' . route('admin.discover.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                                data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_discover')) {
                        $action .= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-happening"
                            data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_discover')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    $imagePath = asset('user-uploads/discovers/' . $row->image);
                    return '<img src="' . $imagePath . '" class="img" height="65em" width="65em"/> ';
                })                       
                ->editColumn('title', function ($row) {
                    return ucfirst($row->title);
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="badge badge-success">' . __("app.active") . '</label>';
                    } elseif ($row->status == 'inactive') {
                        return '<label class="badge badge-danger">' . __("app.inactive") . '</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'title', 'status'])
                ->toJson();
        }

        return view('admin.discover.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_discover'), 403);

        return view('admin.discover.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_discover'), 403);

        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable',
        ]);

        $happening = new Discover();
        $happening->title = $request->input('title');
        $happening->description = $request->input('description');
        $happening->status = $request->input('status');
        $happening->off_percentage = $request->input('off_percentage');

        if ($request->hasFile('image')) {
            $imagePath = Files::upload($request->file('image'), 'happenings');
            $happening->image = $imagePath;
        }

        $happening->save();

        return Reply::redirect(route('admin.discover.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_discover'), 403);
        
        $happening = Discover::findOrFail($id);

        return view('admin.discover.show', compact('happening'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_discover'), 403);

        $happening = Discover::findOrFail($id);

        return view('admin.discover.edit', compact('happening'));
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
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_discover'), 403);

        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable',
        ]);
    
        $happening = Discover::findOrFail($id);
        $happening->title = $request->input('title');
        $happening->description = $request->input('description');
        $happening->status = $request->input('status');
        $happening->off_percentage = $request->input('off_percentage');
    
        if ($request->hasFile('image')) {
            Files::deleteFile($happening->image, '/public/user-uploads/happenings/');
    
            $imagePath = Files::upload($request->file('image'), 'happenings');
            $happening->image = $imagePath;
        }
    
        $happening->save();
    
        return Reply::redirect(route('admin.discover.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_discover'), 403);

        $happening = Discover::findOrFail($id);
    
        Files::deleteFile($happening->image, 'happenings');
    
        $happening->delete();
    
        return Reply::success(__('messages.recordDeleted'));
    }
}
