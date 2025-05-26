<?php

namespace App\Http\Controllers\Auth;

use App\Booking;
use App\Http\Controllers\Controller;
use App\Location;
use Carbon\Carbon;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, AppBoot;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('email.loginAccount'));
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        // if (!$this->isLegal()) {
        //     return redirect('verify-purchase');
        // }

        if (!session()->has('errors')) {
            session()->put('url.encoded', url()->previous());
        }

        return view('auth.login');
    }


    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
       
        if ($user->is_admin || $user->is_employee) {
            return redirect()->route('admin.dashboard');
        }

     
        if ($user->is_branch) {
            return redirect()->route('admin.dashboard');
        }

        if(!$user->is_admin && !$user->is_employee && Cookie::get('bookingDetails')!==null && Cookie::get('products')!==null && $this->checkUserBooking($user->id)>$this->settings->booking_per_day){
            return redirect(route('front.index'))->withCookie(Cookie::forget('bookingDetails'))->withCookie(Cookie::forget('products'))->withCookie(Cookie::forget('couponData'));
        }
        return redirect(session()->get('url.encoded'));
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        session()->forget('url.encoded');

        return redirect(url()->previous());
    }

    protected function checkUserBooking($user_id)
    {
        $bookingDetails = json_decode(request()->cookie('bookingDetails'), true);
        $bookingDate = Carbon::createFromFormat('Y-m-d', $bookingDetails['bookingDate']);
        return Booking::whereDate('created_at', $bookingDate)->where('user_id', $user_id)->get()->count();
    }






}
