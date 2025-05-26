<?php

namespace App\Http\Controllers\Pos;

use App\BusinessService;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Location;
use App\Outlet;
use App\TaxSetting;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.pos'));
    }

    public function index($outlet_slug)
    {
        $outlet = Outlet::where('outlet_slug', $outlet_slug);

        if ($outlet->exists()) 
        {
            $outlet = $outlet->first();

            $user = Auth::user();

            if($user)
            {
                if ($user->is_employee) 
                {
                    $user_id = Auth::user()->id;

                    if (DB::table('employee_pos_outlets')->where('outlet_id', $outlet->id)->where('user_id', $user_id)->exists()) 
                    {
                        $services = BusinessService::active()->get();
                        $categories = Category::active()
                            ->with(['services' => function ($query) {
                                $query->active();
                            }])->get();
                        $locations = Location::all();
                        $tax = TaxSetting::active()->first();
                        $employees = User::OtherThanCustomers()->get();


                        $data['outlet'] = $outlet;
                        $data['services'] = $services;
                        $data['categories'] = $categories;
                        $data['locations'] = $locations;
                        $data['tax'] = $tax;
                        $data['employees'] = $employees;
                        
                        return view('pos.pos.pos', $data);
                    }
                }
            }

            return redirect()->route('pos.login', $outlet->outlet_slug);
        } 
        else 
        {
            abort(404);
        }
    }
}
