<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UniversalSearch;
use App\User;

class SearchController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('front.search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $key = $request->search_key;

        if(trim($key) == ''){
            return redirect()->back();
        }

        return redirect(route('admin.search.show', $key));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($key) {

        $searchResults = UniversalSearch::where('title', 'like', '%'.$key.'%')->get();

        $userResults = User::where('name', 'like', '%'.$key.'%')->orWhere('email', 'like', '%'.$key.'%')->get();

        $searchKey = $key;

        session()->put('searchKey', $searchKey);

        return view('admin.search.show', compact('searchResults', 'userResults', 'searchKey'));
    }
}
