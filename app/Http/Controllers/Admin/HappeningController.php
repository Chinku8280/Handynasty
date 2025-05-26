<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Happening;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Outlet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HappeningController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.happening'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_happening'), 403);

        if (request()->ajax()) {
            $happenings = Happening::get();

            return datatables()->of($happenings)
                ->addColumn('action', function ($row) {
                    $action = '';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_happening')) {
                        $action .= '<a href="' . route('admin.happening.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                                data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_happening')) {
                        $action .= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-happening"
                            data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_happening')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    $imagePath = asset('user-uploads/happenings/' . $row->image);
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
                ->editColumn('start_date_time', function ($row) {  
                    if(!empty($row->start_date_time))    
                    {
                        return Carbon::parse($row->start_date_time)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format);
                    } 
                    else
                    {
                        return '';
                    }                         
                })
                ->editColumn('end_date_time', function ($row) {
                    if(!empty($row->end_date_time))    
                    {
                        return Carbon::parse($row->end_date_time)->translatedFormat($this->settings->date_format.' '.$this->settings->time_format);
                    } 
                    else
                    {
                        return '';
                    }                 
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'title', 'status'])
                ->toJson();
        }

        return view('admin.happening.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_happening'), 403);

        $outlets = Outlet::where('status', 'active')->get();

        $data = [
            'outlets' => $outlets,
        ];

        return view('admin.happening.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        
        $request->validate([
            'title' => 'required',
            'start_date_time' => 'nullable',
            'end_date_time' => 'nullable',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'gender' => 'required',
            'outlet_id' => 'required',
        ]);

        // $start_date_time = Carbon::createFromFormat('Y-m-d H:i a', $request->start_date_time)->format('Y-m-d H:i:s');
        // $end_date_time = Carbon::createFromFormat('Y-m-d H:i a', $request->end_date_time)->format('Y-m-d H:i:s');

        if($request->filled('start_date_time'))
        {
            $start_date_time = date('Y-m-d H:i:s', strtotime($request->start_date_time));
        }
        else
        {
            $start_date_time = null;
        }

        if($request->filled('end_date_time'))
        {
            $end_date_time = date('Y-m-d H:i:s', strtotime($request->end_date_time));
        }
        else
        {
            $end_date_time = null;
        }

        $happening = new Happening();
        $happening->title = $request->title;
        $happening->start_date_time = $start_date_time;
        $happening->end_date_time = $end_date_time;
        $happening->description = $request->description;
        $happening->status = $request->status;
        $happening->min_age = $request->min_age;
        $happening->max_age = $request->max_age;

        if ($request->hasFile('image')) {
            $imagePath = Files::upload($request->file('image'), 'happenings');
            $happening->image = $imagePath;
        }

        $happening->save();

        // happening gender start

        if($request->filled('gender'))
        {
            $new_gender = $this->store_happening_gender($request, $happening->id);

            $happening->gender = implode(',', $new_gender);
            $happening->save();
        }

        // happening gender end

        // happening outlet start

        if($request->filled('outlet_id'))
        {
            $new_outlet_id = $this->store_happening_outlet($request, $happening->id);

            $happening->outlet_id = implode(',', $new_outlet_id);
            $happening->save();
        }

        // happening outlet end

        return Reply::redirect(route('admin.happening.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_happening'), 403);

        $happening = Happening::findOrFail($id);

        if($happening)
        {
            $happening_outlets = DB::table('happening_outlets')->where('happening_id', $id)->pluck('outlet_id')->toArray();
            $outlet_name_arr = Outlet::whereIn('id', $happening_outlets)->pluck('outlet_name')->toArray();
            
            $happening->outlet_name = implode(', ', $outlet_name_arr);
        }

        return view('admin.happening.show', compact('happening'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_happening'), 403);

        $happening = Happening::findOrFail($id);

        $happening->selected_Gender = explode(",", $happening->gender);
        $happening->selected_outlets = explode(",", $happening->outlet_id);

        $outlets = Outlet::where('status', 'active')->get();

        return view('admin.happening.edit', compact('happening', 'outlets'));
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
        // return $request->all();

        $request->validate([
            'title' => 'required',
            'start_date_time' => 'nullable',
            'end_date_time' => 'nullable',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'gender' => 'required',
            'outlet_id' => 'required',
        ]);

        // $start_date_time = Carbon::createFromFormat('Y-m-d H:i a', $request->start_date_time)->format('Y-m-d H:i:s');
        // $end_date_time = Carbon::createFromFormat('Y-m-d H:i a', $request->end_date_time)->format('Y-m-d H:i:s');

        if($request->filled('start_date_time'))
        {
            $start_date_time = date('Y-m-d H:i:s', strtotime($request->start_date_time));
        }
        else
        {
            $start_date_time = null;
        }

        if($request->filled('end_date_time'))
        {
            $end_date_time = date('Y-m-d H:i:s', strtotime($request->end_date_time));
        }
        else
        {
            $end_date_time = null;
        }

        $happening = Happening::findOrFail($id);

        $happening->title = $request->title;
        $happening->start_date_time = $start_date_time;
        $happening->end_date_time = $end_date_time;
        $happening->description = $request->description;
        $happening->status = $request->status;
        $happening->min_age = $request->min_age;
        $happening->max_age = $request->max_age;

        if ($request->hasFile('image')) {
            if ($happening->image) {
                Files::deleteFile($happening->image, 'happenings');
            }

            $imagePath = Files::upload($request->file('image'), 'happenings');
            $happening->image = $imagePath;
        }

        $happening->save();

        // happening gender start

        if($request->filled('gender'))
        {
            DB::table('happening_gender')->where('happening_id', $happening->id)->delete();

            $new_gender = $this->store_happening_gender($request, $happening->id);

            $happening->gender = implode(',', $new_gender);
            $happening->save();
        }

        // happening gender end

        // happening outlet start

        if($request->filled('outlet_id'))
        {
            DB::table('happening_outlets')->where('happening_id', $happening->id)->delete();

            $new_outlet_id = $this->store_happening_outlet($request, $happening->id);

            $happening->outlet_id = implode(',', $new_outlet_id);
            $happening->save();
        }

        // happening outlet end

        return Reply::redirect(route('admin.happening.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_happening'), 403);

        $happening = Happening::findOrFail($id);

        if($happening)
        {
            DB::table('happening_gender')->where('happening_id', $id)->delete();
            DB::table('happening_outlets')->where('happening_id', $id)->delete();

            Files::deleteFile($happening->image, 'happenings');
    
            $happening->delete();
        
            return Reply::success(__('messages.recordDeleted'));
        }
        else
        {
            return Reply::error(__('messages.noRecordFound'));
        }
    }

    public static function store_happening_gender($request, $happening_id)
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

        $happening_gender = [];

        foreach($new_gender as $item)
        {
            $happening_gender[] = [
                'happening_id' => $happening_id,
                'gender' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('happening_gender')->insert($happening_gender);

        return $new_gender;
    }

    public static function store_happening_outlet($request, $happening_id)
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

        $happening_outlets = [];

        foreach($new_outlet_id as $item)
        {
            $happening_outlets[] = [
                'happening_id' => $happening_id,
                'outlet_id' => $item,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('happening_outlets')->insert($happening_outlets);

        return $new_outlet_id;
    }
}
