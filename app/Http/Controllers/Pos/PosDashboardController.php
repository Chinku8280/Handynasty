<?php

namespace App\Http\Controllers\Pos;

use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosDashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.dashboard'));
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
                        $todoItemsView = $this->generateTodoView();
                
                        $data['todoItemsView'] = $todoItemsView;
                        $data['outlet'] = $outlet;
                        
                        return view('pos.dashboard', $data);
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
