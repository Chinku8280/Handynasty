<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\Package;
use App\PackageUser;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Package\StoreRequest;
use App\Http\Requests\Package\UpdateRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Location;

class PackageController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.packages'));
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
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_package'), 403);
        return view('admin.packages.index');
    }

    public function data()
    {
        $package = Package::all();

        return \datatables()->of($package)
            ->addColumn('action', function ($row) {
                $action = '';
               
                if($this->user->can('update_package')) {
                    $action.= '<a href="' . route('admin.packages.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                    data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                }
                
                $action.= '<a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-package"
                data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-eye" aria-hidden="true"></i></a> ';

                if($this->user->can('delete_package')) {
                    $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                    data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }

                return $action;
            })

            ->editColumn('title', function ($row) {
                return '<span class="badge badge-warning">'.strtoupper($row->title).'</span>';
            })          
           
            ->editColumn('amount', function ($row) {
                if($row->amount && is_null($row->percent)){
                    return $row->amount;
                }             
            })
            ->editColumn('coin', function ($row) {             
                    return $row->coin;                         
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
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_package'), 403);  
       
        return view('admin.packages.create');
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_package'), 403);   

        $package = new Package();
       

        $package->title                   = strtolower($request->title); 
        $package->amount                  = $request->amount;
        $package->coin                    = $request->coin;       
        $package->description             =  $request->description;
        $package->status                  =  $request->status; 
        $package->save();
        return Reply::redirect(route('admin.packages.index'), __('messages.createdSuccessfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->package = Package::findOrFail($id);       

        return view('admin.packages.show', $this->data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
       
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_package'), 403);     

        $this->package = Package::with('customers')->findOrFail($id);      
             
        return view('admin.packages.edit',$this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
     {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_package'), 403);

        $package = Package::findOrFail($id);

        $package->title                   = strtolower($request->title);
        $package->amount                  = $request->amount;
        $package->coin                    = $request->coin;       
        $package->description             =  $request->description;
        $package->status                  =  $request->status;       

        $package->save();

        return Reply::redirect(route('admin.packages.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_package'), 403);

        $package = Package::findOrFail($id);
        $package->delete();

        return Reply::success(__('messages.recordDeleted'));
    }

    // public function assignPackage(Request $request)
    // {        
    //     // assign package 

    //     $PackageUser = new PackageUser();
    //     $PackageUser->package_id             = $request->packageId;
    //     $PackageUser->user_id                = $request->userId;     
    //     $PackageUser->status                = 1;  

    //     $PackageUser->save();
       
    //     return response()->json(['message' => 'Package assigned successfully']);
    // }

    public function assignPackage(Request $request)
    {
        $existingAssignment = PackageUser::where('package_id', $request->packageId)
                                         ->where('user_id', $request->userId)
                                         ->first();
    
        if ($existingAssignment) {
            $existingAssignment->status = 1;
            $existingAssignment->save();
    
            return Reply::redirect(route('admin.customers.show', $request->userId), __('messages.packageAlreadyAssigned'));
        }
    
        $newAssignment = new PackageUser();
        $newAssignment->package_id = $request->packageId;
        $newAssignment->user_id = $request->userId;     
        $newAssignment->status = 1;  
    
        $newAssignment->save();
    
        return response()->json([
            'message' => 'Package assigned successfully',
            'packageStatus' => $newAssignment->status,
            'assignedAt' => $newAssignment->created_at->format('j F Y g:i A'),
            'title' => $newAssignment->package->title,
            'amount' => $newAssignment->package->amount,
            'coin' => $newAssignment->package->coin,
        ]);
    }
    
}
