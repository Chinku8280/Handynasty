<?php

namespace App\Http\Controllers\BranchAuth;

use Illuminate\Http\Request;
use App\Location;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Controller;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers, AppBoot;

    protected $redirectTo = '/account/dashboard';

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('email.loginAccount'));
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request)
    {
        $fullUrl = $request->url();
    
        $location = Location::where('url', '=', $fullUrl)->first();
    
        if (!$location) {
            return abort(404);
        }
    
        return view('auth.branchlogin');
    }

    public function login(Request $request)
    {
        $currentUrl = $request->input('current_url');
        $location = Location::where('url', $currentUrl)->first();

        if ($location && Auth::guard('branch')->loginUsingId($location->id)) 
        {
            // dd($location->name);
            
            session(['url.intended' => route('branch.dashboard', ['branchName' => $location->name])]);
            
            return redirect()->route('branch.dashboard', ['branchName' => $location->name]);
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }
    

    // Testing

    // public function login(Request $request)
    // {
    //     $currentUrl = $request->input('current_url');
            
    //     $location = Location::where('url', $currentUrl)->first();
    
    //     if ($location) {
    //         // Debugging to compare hashed passwords
    //         dd('Hashed Password (DB):', $location->password, 'Plain Text Password (Input):', $request->input('password'));
    //     }
    
    //     if ($location && Hash::check($request->input('password'), $location->password) && $request->input('email') === $location->email) 
    //     {
    //         // Manual login
    //         Auth::guard('branch')->loginUsingId($location->id);
    //         return redirect()->route('branch.dashboard');
    //     }
    
    //     return back()->withErrors(['email' => 'Invalid credentials']);
    // }
    
    
}
