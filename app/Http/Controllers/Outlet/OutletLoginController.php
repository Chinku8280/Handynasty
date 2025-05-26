<?php

namespace App\Http\Controllers\Outlet;

use App\User;
use App\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class OutletLoginController extends Controller
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
                    if (DB::table('employee_outlets')->where('outlet_id', $outlet->id)->where('user_id', $user->id)->exists()) 
                    {
                        return redirect()->route('outlet.dashboard', $outlet->outlet_slug);
                        // return redirect()->route('admin.dashboard');
                    }
                }
            }

            $data['outlet'] = $outlet;
            return view('outlet.auth.login', $data);
        } 
        else 
        {
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
                if (DB::table('employee_outlets')->where('outlet_id', $request->outlet_id)->where('user_id', $user->id)->exists()) {
                    auth()->login($user);

                    $outlet = Outlet::find($request->outlet_id);

                    $request->session()->put('outlet_id', $outlet->id);
                    $request->session()->put('outlet_slug', $outlet_slug);
                    $request->session()->put('outlet_name', $outlet->outlet_name);

                    // return redirect()->route('admin.dashboard');
                    return redirect()->route('outlet.dashboard', $outlet->outlet_slug);
                } 
                else {
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

        return redirect()->route('outlet.login', $outlet_slug);
    }
}
