<?php

namespace App\Http\Controllers\Outlet;

use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutletDashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.dashboard'));
    }

    public function index(Request $request, $outlet_slug)
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
                    $user = Auth::user();               

                    if (DB::table('employee_outlets')->where('outlet_id', $outlet->id)->where('user_id', $user_id)->exists()) 
                    {                                           
                        $todoItemsView = $this->generateTodoView();

                        if($user->is_admin){
                            $recentSales = Booking::orderBy('id', 'desc')->take(20)->get();
                        }
                        else{
                            $recentSales = null;
                        }

                        $data['recentSales'] = $recentSales;
                        $data['todoItemsView'] = $todoItemsView;
                        $data['outlet'] = $outlet;

                        return view('outlet.dashboard', $data);
                    }
                }
            }
            
            return redirect()->route('outlet.login', $outlet->outlet_slug);         
        } 
        else 
        {
            abort(404);
        }
    }
}
