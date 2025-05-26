<?php

namespace App\Http\Controllers\Admin;

use App\BusinessService;
use App\Category;
use App\EmployeeGroup;
use App\Outlet;
use Carbon\Carbon;
use App\EmployeeGroupService;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Employee\StoreRequest;
use App\Http\Requests\Employee\UpdateRequest;
use App\Http\Requests\Service\StoreService;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\ChangeRoleRequest;
use App\Role;
use EmployeeGroupsServices;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.employee'));

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
    // public function index()
    // {
    //     abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee'), 403);

    //     if(\request()->ajax()){
    //         $employee = User::otherThanCustomers()->get();
    //         $roles = Role::all();

    //         return \datatables()->of($employee)
    //             ->addColumn('action', function ($row) {
    //                 $action = '<center>';
    //                 if (($this->user->is_admin || $this->user->can('update_employee'))&& $row->id !== $this->user->id) {
    //                     $action.= '<a href="' . route('admin.employee.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
    //                       data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
    //                 }
    //                 if (($this->user->is_admin || $this->user->can('delete_employee'))&& $row->id !== $this->user->id) {
    //                     $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
    //                         data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
    //                 }

    //                 return $action.'</center>';
    //             })
    //             ->addColumn('image', function ($row) {
    //                 return '<img src="'.$row->user_image_url.'" class="img" height="65em" width="65em"/> ';
    //             })
    //             ->editColumn('name', function ($row) {
    //                 return ucfirst($row->name);
    //             })
    //             ->editColumn('group_id', function ($row) {
    //                 return !is_null($row->group_id) ? ucfirst($row->employeeGroup->name) : '--';
    //             })
    //             ->editColumn('assignedServices', function ($row) {
    //                 $service_list = '';
    //                 foreach ($row->services as $key => $service) {
    //                     $service_list .= '<span style="margin:0.3em; padding:0.3em" class="badge badge-primary">'.$service->name.'</span>';
    //                 }
    //                 return $service_list=='' ? '--' : $service_list;
    //             })
    //             ->addColumn('role_name', function ($row) use ($roles){
    //                 if (($row->id === $this->user->id) || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee')) {
    //                     return $row->role->display_name;
    //                 }

    //                 $roleOption = '<select style="width:100%" name="role_id" class="form-control role_id" data-user-id="'.$row->id.'">';

    //                 foreach ($roles as $role){
    //                     $roleOption.= '<option ';

    //                     if($row->role->id == $role->id){
    //                         $roleOption.= ' selected ';
    //                     }

    //                     $roleOption.= 'value="'.$role->id.'">'.ucwords($role->display_name).'</option>';
    //                 }
    //                 $roleOption.= '</select>';

    //                 return $roleOption;
    //             })
    //             ->addIndexColumn()
    //             ->rawColumns(['action', 'image', 'role_name', 'assignedServices'])
    //             ->toJson();
    //     }

    //     return view('admin.employees.index');
    // }

    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee'), 403);

        if(\request()->ajax()){
            $employee = User::otherThanCustomers()->get();
            $roles = Role::all();

            foreach($employee as $item)
            {
                // employee outlet start

                $assigned_outlet_id_arr = DB::table('employee_outlets')->where('user_id', $item->id)->pluck('outlet_id')->toArray();
                $assigned_outlet_name_arr = Outlet::whereIn('id', $assigned_outlet_id_arr)->pluck('outlet_name')->toArray();

                $item->assigned_outlet_name = implode(', ', $assigned_outlet_name_arr);

                // employee outlet end

                // employee pos outlet start

                $assigned_pos_outlet_id_arr = DB::table('employee_pos_outlets')->where('user_id', $item->id)->pluck('outlet_id')->toArray();
                $assigned_pos_outlet_name_arr = Outlet::whereIn('id', $assigned_pos_outlet_id_arr)->pluck('outlet_name')->toArray();

                $item->assigned_pos_outlet_name = implode(', ', $assigned_pos_outlet_name_arr);

                // employee pos outlet end
            }

            return \datatables()->of($employee)
                ->addColumn('action', function ($row) {
                    $action = '<center>';
                    if (($this->user->is_admin || $this->user->can('update_employee'))&& $row->id !== $this->user->id) {
                        $action.= '<a href="' . route('admin.employee.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                          data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }
                    if (($this->user->is_admin || $this->user->can('delete_employee'))&& $row->id !== $this->user->id) {
                        $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action.'</center>';
                })
                ->addColumn('image', function ($row) {
                    return '<img src="'.$row->user_image_url.'" class="img" height="65em" width="65em"/> ';
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
                })
                ->editColumn('assigned_outlet_name', function ($row) {
                    return $row->assigned_outlet_name;
                })
                ->editColumn('assigned_pos_outlet_name', function ($row) {
                    return $row->assigned_pos_outlet_name;
                })
                ->addColumn('role_name', function ($row) use ($roles){
                    if (($row->id === $this->user->id) || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee')) {
                        return $row->role->display_name;
                    }

                    $roleOption = '<select style="width:100%" name="role_id" class="form-control role_id" data-user-id="'.$row->id.'">';

                    foreach ($roles as $role){
                        $roleOption.= '<option ';

                        if($row->role->id == $role->id){
                            $roleOption.= ' selected ';
                        }

                        $roleOption.= 'value="'.$role->id.'">'.ucwords($role->display_name).'</option>';
                    }
                    $roleOption.= '</select>';

                    return $roleOption;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'role_name'])
                ->toJson();
        }

        return view('admin.employees.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_employee'), 403);

        $groups = EmployeeGroup::all();
        $roles = Role::all();
        $business_services = BusinessService::all();
        $outlets = Outlet::where('status', 'active')->get();

        return view('admin.employees.create', compact('groups', 'roles', 'business_services','outlets'));
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_employee'), 403);

        $user = new User();

        $user->name     = $request->name;
        $user->email    = $request->email;

        // if ($request->group_id !== '0')
        // {
        //     $user->group_id = $request->group_id;

        //     $service_array = array();
        //     $services_lists  =EmployeeGroupService::where('employee_groups_id', $request->group_id)->get();
        //     foreach ($services_lists as $key => $services_list) {
        //         $service_array [] = $services_list->business_service_id;
        //     }

        // }

        $user->calling_code = $request->calling_code;
        $user->mobile = $request->mobile;
        $user->image_display_in_app = $request->image_display_in_app;

        if($request->password != ''){
            $user->password = $request->password;
        }

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image,'avatar');
        }

        $user->save();

        // if ($request->group_id !== '0') 
        // { 
        //     $user->services()->sync($service_array); 
        // }


        /* Assign services to users */
        $business_service_id = $request->business_service_id;
        if($business_service_id)
        {
            $assignedSerives   = array();
            foreach ($business_service_id as $key => $service_id)
            {
                $assignedSerives[] = $business_service_id[$key];
            }
            $user->services()->sync($assignedSerives);
        }

        // add default employee role
        $user->attachRole($request->role_id);

        // employee outlet start

        if($request->filled('outlet_id'))
        {
            $this->store_employee_outlet($request, $user->id);
        }

        // employee outlet end

        // employee pos outlet start

        if($request->filled('pos_outlet_id'))
        {
            $this->store_employee_pos_outlet($request, $user->id);
        }

        // employee pos outlet end
        
        return Reply::redirect(route('admin.employee.index'), __('messages.createdSuccessfully'));
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
    public function edit(Request $request, $id)
    {
        $employee = User::where('id', $id)->first();
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee') || $employee->id === $this->user->id || $employee->is_customer || $this->user->id === $employee->id, 403);

        $groups = EmployeeGroup::all();
        $roles = Role::all();

        /* push all previous assigned services to an array */
        $selectedServices = array();
        $assignedServices = User::with(['services'])->find($id);
        foreach ($assignedServices->services as $key => $services)
        {
            array_push($selectedServices, $services->id);
        }
        $businessServices = BusinessService::active()->get();

        $outlets = Outlet::where('status', 'active')->get();
        $selectedOutlets = DB::table('employee_outlets')->where('user_id', $id)->pluck('outlet_id')->toArray();
        $selected_pos_outlets = DB::table('employee_pos_outlets')->where('user_id', $id)->pluck('outlet_id')->toArray();

        return view('admin.employees.edit', compact('employee', 'groups', 'roles', 'selectedServices', 'businessServices','outlets', 'selectedOutlets', 'selected_pos_outlets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_employee'), 403);

        $user = User::findOrFail($id);
        /* save new edited services */
        $services = $request->service_id;
        if($services){
            $assignedSerives = array();
            foreach ($services as $key=>$service){
                $assignedSerives[] = $services[$key];
            }
            $user->services()->sync($assignedSerives);
        }
        else {
            $user->services()->detach();
        }
        $user->name         = $request->name;
        $user->email        = $request->email;
        // $user->group_id     = $request->group_id == 0 ? null : $request->group_id;
        $user->image_display_in_app = $request->image_display_in_app;

        // if ($request->group_id !== '0')
        // {
        //     $user->group_id = $request->group_id;

        //     DB::table('business_service_user')->where(['user_id' => $user->id])->delete();

        //     $service_array = array();
        //     $services_lists  =EmployeeGroupService::where('employee_groups_id', $request->group_id)->get();
        //     foreach ($services_lists as $key => $services_list) {
        //         $service_array [] = $services_list->business_service_id;
        //     }

        // }

        if($request->password != ''){
            $user->password = $request->password;
        }
        if (($request->mobile != $user->mobile || $request->calling_code != $user->calling_code) && $user->mobile_verified == 1) {
            $user->mobile_verified = 0;
        }
        $user->mobile       = $request->mobile;
        $user->calling_code = $request->calling_code;
        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image,'avatar');
        }
        $user->save();

        // if ($request->group_id !== '0') { $user->services()->sync($service_array); }

        $user->syncRoles([$request->role_id]);

        // employee outlet start

        DB::table('employee_outlets')->where('user_id', $user->id)->delete();

        if($request->filled('outlet_id'))
        {
            $this->store_employee_outlet($request, $user->id);
        }

        // employee outlet end

        // employee pos outlet start

        DB::table('employee_pos_outlets')->where('user_id', $user->id)->delete();

        if($request->filled('pos_outlet_id'))
        {    
            $this->store_employee_pos_outlet($request, $user->id);
        }

        // employee pos outlet end

        return Reply::redirect(route('admin.employee.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_employee') || $user->id === $this->user->id, 403);

        $user->delete();
        return Reply::success(__('messages.recordDeleted'));
    }

    public function changeRole(ChangeRoleRequest $request)
    {
        $user = User::findOrFail($request->user_id);

        $user->roles()->sync($request->role_id);

        Artisan::call('cache:clear');

        return Reply::success(__('messages.roleChangedSuccessfully'));
    }

    public static function store_employee_outlet($request, $user_id)
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

        $employee_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $employee_outlets[] = [
                'user_id' => $user_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('employee_outlets')->insert($employee_outlets);

        return $new_outlet_id;
    }

    public static function store_employee_pos_outlet($request, $user_id)
    {
        $pos_outlet_id = $request->pos_outlet_id;

        $new_pos_outlet_id = [];

        if($pos_outlet_id[0] == 0)
        {
            foreach(Outlet::where('status', 'active')->get() as $item)
            {
                $new_pos_outlet_id[] = $item->id;
            }
        }
        else
        {
            $new_pos_outlet_id = $pos_outlet_id;
        }

        $employee_pos_outlets = [];

        foreach($new_pos_outlet_id as $item)
        {
            $employee_pos_outlets[] = [
                'user_id' => $user_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('employee_pos_outlets')->insert($employee_pos_outlets);

        return $new_pos_outlet_id;
    }
}
