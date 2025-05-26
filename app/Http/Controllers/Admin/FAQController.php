<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\FAQ;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\Controller;

class FAQController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.faq'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_faqs'), 403);

        if (request()->ajax()) {
            $faqs = FAQ::get();

            return datatables()->of($faqs)
                ->addColumn('action', function ($row) {
                    $action = '';

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_faqs')) {
                        $action .= '<a href="' . route('admin.faq.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                                data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_faqs')) {
                        $action .= ' <a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-info btn-circle view-faq"
                                data-toggle="tooltip" data-original-title="' . __('app.view') . '"><i class="fa fa-eye" aria-hidden="true"></i></a> ';
                    }

                    if ($this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_faqs')) {
                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-row"
                                data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }

                    return $action;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'visible') {
                        return '<label class="badge badge-success">' . __("Visible") . '</label>';
                    } elseif ($row->status == 'hidden') {
                        return '<label class="badge badge-danger">' . __("Hidden") . '</label>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->toJson();
        }

        return view('admin.faq.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_faqs'), 403);

        return view('admin.faq.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_faqs'), 403);

        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'status' => 'required|in:hidden,visible',
        ]);

        $faq = FAQ::create([
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
            'status' => $request->input('status'),
        ]);

        return Reply::redirect(route('admin.faq.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_faqs'), 403);

        $faq = FAQ::findOrFail($id);

        return view('admin.faq.show', compact('faq'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_faqs'), 403);

        $faq = FAQ::findOrFail($id);

        return view('admin.faq.edit', compact('faq'));
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
        // dd($request->all());

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('update_faqs'), 403);
        
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'status' => 'required|in:hidden,visible',
        ]);
    
        $faq = FAQ::findOrFail($id);
        $faq->update([
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
            'status' => $request->input('status'),
        ]);
    
        return Reply::redirect(route('admin.faq.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_faqs'), 403);

        $faq = FAQ::findOrFail($id);
        $faq->delete();

        return Reply::success(__('messages.recordDeleted'));
    }
}
