<?php

namespace App\Http\Controllers\Admin;

use App\Location;
use App\BusinessService;
use App\offer;
use App\offerItem;
use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\offer\StoreRequest;
use App\Http\Requests\offer\UpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.offers'));
    }


    public function index()
    {

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_offer'), 403);

        if (request()->ajax()) {
            $offers = offer::get();

            return datatables()->of($offers)
                ->addColumn('action', function ($row) {
                    $action = '';

                    if ($this->user->can('update_offer')) {
                        $action .= '<a href="' . route('admin.offers.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                        data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    $action .= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-offer"
                    data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a> ';

                    if ($this->user->can('delete_offer')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                        data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action;
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . $row->offer_image_url . '" class="img" height="65em" width="65em"/> ';
                })
                ->editColumn('title', function ($row) {
                    return ucfirst($row->title);
                })
                ->editColumn('start_date_time', function ($row) {
                    return Carbon::parse($row->start_date_time)->translatedFormat($this->settings->date_format . ' ' . $this->settings->time_format);
                })
                ->editColumn('end_date_time', function ($row) {
                    return Carbon::parse($row->end_date_time)->translatedFormat($this->settings->date_format . ' ' . $this->settings->time_format);
                })
                ->editColumn('discount', function ($row) {
                    return $row->discount . '%';
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="badge badge-success">' . __("app.active") . '</label>';
                    } elseif ($row->status == 'inactive') {
                        return '<label class="badge badge-danger">' . __("app.inactive") . '</label>';
                    }
                })

                ->addIndexColumn()
                ->rawColumns(['action', 'image', 'title', 'start_date_time', 'end_date_time', 'discount', 'status'])
                ->toJson();
        }
        return view('admin.offers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_offer'), 403);
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $branches  = Location::groupBy('name')->get();
        return view('admin.offers.create', compact('days', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
      //   dd($request->all());
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_offer'), 403);     


        $offer = new offer();
        $offer->title                   = $request->title;
        $offer->start_date_time         = $request->start_date_time;
        $offer->end_date_time           = $request->end_date_time;
        $offer->branch_id               = $request->branch_id;
        $offer->max_person              = $request->max_person;
        $offer->status                  = $request->status;
        $cleanDescription               = strip_tags($request->input('description'));
        $cleanDescription = strip_tags($request->input('description'));
        $offer->description             = $cleanDescription;
        $offer->discount              = $request->discount;
        $offer->gender = $request->gender;
        $offer->min_age = $request->min_age;
        $offer->max_age = $request->max_age;


        if ($request->hasFile('feature_image')) {
            $offer->image = Files::upload($request->feature_image, 'offer');
        }

        $offer->save();

        return Reply::redirect(route('admin.offers.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $offer = Offer::with('branch')->findOrFail($id); 
        
        $branch = $offer->branch;
        return view('admin.offers.show', compact('offer', 'branch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_offer'), 403);      
        $offer = Offer::findOrFail($id); 
        $branches  = Location::groupBy('name')->get();
        return view('admin.offers.edit', compact('offer','branches'));
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
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_offer'), 403);  

        $offer = Offer::findOrFail($id);
        $offer->title                   = $request->title;
        $offer->start_date_time         = $request->start_date_time;
        $offer->end_date_time           = $request->end_date_time;
        $offer->branch_id               = $request->branch_id;
        $offer->max_person              = $request->max_person;
        $offer->status                  = $request->status;
        $cleanDescription               = strip_tags($request->input('description'));       
        $offer->description             = $cleanDescription;
        $offer->discount              = $request->discount;
        $offer->gender = $request->gender;
        $offer->min_age = $request->min_age;
        $offer->max_age = $request->max_age;
    
        if ($request->hasFile('feature_image')) {
            $offer->image = Files::upload($request->feature_image, 'offer');
        }

        $offer->save();

        return Reply::redirect(route('admin.offers.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_coupon'), 403);

        $coupon = Offer::findOrFail($id);
        $coupon->delete();
        return Reply::success(__('messages.recordDeleted'));
    }
} /* end of class */
