<?php

namespace App\Http\Controllers\Pos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PosAuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('email.loginAccount'));
    }

    public function showLoginForm($outlet_slug)
    {
        // return $outlet_slug;
        
        $outlet = Outlet::where('outlet_slug', $outlet_slug);

        if ($outlet->exists()) 
        {
            $outlet = $outlet->first();

            $user = Auth::user();
            if($user)
            {
                if ($user->is_employee) 
                {
                    if (DB::table('employee_pos_outlets')->where('outlet_id', $outlet->id)->where('user_id', $user->id)->exists()) 
                    {
                        return redirect()->route('pos.dashboard', $outlet->outlet_slug);
                    }
                }
            }
        
            $data['outlet'] = $outlet;
            return view('pos.auth.login', $data);
        } 
        else {
            abort(404);
        }
    }

    public function login(Request $request, $outlet_slug)
    {
        // return $request->all();

        // Validate the incoming request
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt to log the user in
        if (User::where('email', $request->email)->exists()) {
            $user = User::where('email', $request->email)->first();
            if (Hash::check($request->password, $user->password)) {
                if (DB::table('employee_pos_outlets')->where('outlet_id', $request->outlet_id)->where('user_id', $user->id)->exists()) {
                    auth()->login($user);

                    $outlet = Outlet::find($request->outlet_id);

                    // return redirect()->route('admin.dashboard');
                    return redirect()->route('pos.dashboard', $outlet->outlet_slug);
                } else {
                    return redirect()->back()->withErrors(['errors' => 'You are not authorized to access this outlet.']);
                }
            } else {
                return redirect()->back()->withErrors(['errors' => 'You are not authorized to access this outlet.']);
            }
        } else {
            return redirect()->back()->withErrors(['errors' => 'You are not authorized to access this outlet.']);
        }
    }

    public function logout(Request $request, $outlet_slug)
    {
        Auth::logout();

        session()->forget('url.encoded');

        $request->session()->flush();

        return redirect()->route('pos.login', $outlet_slug);
    }
}
