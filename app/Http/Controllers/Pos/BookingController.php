<?php

namespace App\Http\Controllers\Pos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.bookings'));
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
                        $data['outlet'] = $outlet;
                                        
                        return view('pos.bookings', $data);
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
