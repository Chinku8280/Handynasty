<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreLocation;
use App\Location;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LocationController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.branches'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_branch'), 403);

        if (request()->ajax()) {
            $locations = Location::all();

            return datatables()->of($locations)
                ->addColumn('action', function ($row) {
                    $action = '';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_branch')) {
                        $action .= '<a href="' . route('admin.branches.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                          data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    $action.= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-branch"
                    data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-eye" aria-hidden="true"></i></a> ';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_branch')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }
                    return $action;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.branch.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_branch'), 403);

        return view('admin.branch.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLocation $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_branch'), 403);

        $location = new Location();
        $location->name = $request->name;
        $location->email = $request->email;
        $location->password = Hash::make($request->password);
        $location->postalCode = $request->postalCode;
        $location->address = $request->address;
        $location->mobile = $request->mobile;
        $location->openingTime = $request->openingTime;
        $location->closingTime = $request->closingTime;
        if ($request->hasFile('image')) {
            $location->image = Files::upload($request->image, 'branch');
        }

        $location->save();
        $baseURL = $request->getSchemeAndHttpHost();
        $url = $baseURL . '/' . Str::slug($location->name) . '/login';

        $location->url = $url;
        $location->save();

        return Reply::redirect($request->redirect_url, __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $branch = Location::findOrFail($id);

        return view('admin.branch.show', compact('branch'));
    }



    /**
     * Show the form for editing the specified resource.
     * 
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $location = Location::find($id);
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_branch'), 403);

        $this->days = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];

        return view('admin.branch.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(StoreLocation $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_branch'), 403);

        $location = Location::find($id);
        $location->name = $request->name;
        $location->email = $request->email;
        $location->password = Hash::make($request->password);
        $location->postalCode = $request->postalCode;
        $location->address = $request->address;
        $location->mobile = $request->mobile;
        $location->openingTime = $request->openingTime;
        $location->closingTime = $request->closingTime;
        if ($request->hasFile('image')) {
            $location->image = Files::upload($request->image, 'branch');
        }

        $location->save();
        $baseURL = $request->getSchemeAndHttpHost();
        $url = $baseURL . '/' . Str::slug($location->name) . '/login';

        $location->url = $url;
        $location->save();

        $location->save();

        return Reply::redirect(route('admin.branches.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_branch'), 403);

        Location::destroy($id);

        return Reply::success(__('messages.recordDeleted'));
    }
}
