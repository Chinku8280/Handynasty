<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\Helper\Reply;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class ShowDashboard extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.dashboard'));
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function __invoke(Request $request)
    // {
    //     if(\request()->ajax()){

    //         $startDate = Carbon::createFromFormat('Y-m-d', $request->startDate);
    //         $endDate = Carbon::createFromFormat('Y-m-d', $request->endDate);

    //         $totalBooking = Booking::whereDate('date_time', '>=', $startDate)
    //             ->whereDate('date_time', '<=', $endDate);
    //             if(!$this->user->is_admin){
    //                 $totalBooking = ($this->user->is_employee) ? $totalBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $totalBooking->where('user_id', $this->user->id);
    //             }
    //             $totalBooking = $totalBooking->count();

    //         $inProgressBooking = Booking::whereDate('date_time', '>=', $startDate)
    //             ->whereDate('date_time', '<=', $endDate)
    //             ->where('status', 'in progress');
    //             if(!$this->user->is_admin){
    //                 $inProgressBooking = ($this->user->is_employee) ? $inProgressBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $inProgressBooking->where('user_id', $this->user->id);
    //             }
    //         $inProgressBooking = $inProgressBooking->count();

    //         $pendingBooking = Booking::whereDate('date_time', '>=', $startDate)
    //             ->whereDate('date_time', '<=', $endDate)
    //             ->where('status', 'pending');
    //             if(!$this->user->is_admin){
    //                 $pendingBooking = ($this->user->is_employee) ? $pendingBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $pendingBooking->where('user_id', $this->user->id);
    //             }
    //             $pendingBooking = $pendingBooking->count();

    //         $approvedBooking = Booking::whereDate('date_time', '>=', $startDate)
    //             ->whereDate('date_time', '<=', $endDate)
    //             ->where('status', 'approved');
    //             if(!$this->user->is_admin){
    //                 $approvedBooking = ($this->user->is_employee) ? $approvedBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $approvedBooking->where('user_id', $this->user->id);
    //             }
    //             $approvedBooking = $approvedBooking->count();

    //         $completedBooking = Booking::whereDate('date_time', '>=', $startDate)
    //             ->whereDate('date_time', '<=', $endDate)
    //             ->where('status', 'completed');
    //             if(!$this->user->is_admin){
    //                 $completedBooking =  ($this->user->is_employee) ? $completedBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $completedBooking->where('user_id', $this->user->id);
    //             }
    //         $completedBooking = $completedBooking->count();

    //         $canceledBooking = Booking::whereDate('date_time', '>=', $startDate)
    //             ->whereDate('date_time', '<=', $endDate)
    //             ->where('status', 'canceled');
    //             if(!$this->user->is_admin){
    //                 $canceledBooking = ($this->user->is_employee) ? $canceledBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $canceledBooking->where('user_id', $this->user->id);
    //             }
    //         $canceledBooking = $canceledBooking->count();

    //         $offlineBooking = Booking::whereDate('date_time', '>=', $startDate)
    //             ->whereDate('date_time', '<=', $endDate)
    //             ->where('source', 'pos');
    //             if(!$this->user->is_admin){
    //                 $offlineBooking = ($this->user->is_employee) ? $offlineBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $offlineBooking->where('user_id', $this->user->id);
    //             }
    //         $offlineBooking = $offlineBooking->count();

    //         $onlineBooking = Booking::whereDate('date_time', '>=', $startDate)
    //             ->whereDate('date_time', '<=', $endDate)
    //             ->where('source', 'online');
    //             if(!$this->user->is_admin){
    //                 $onlineBooking = ($this->user->is_employee) ? $onlineBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $onlineBooking->where('user_id', $this->user->id);
    //             }
    //         $onlineBooking = $onlineBooking->count();

    //         if($this->user->is_admin){
    //             $totalCustomers = User::allCustomers()
    //                 ->whereDate('created_at', '>=', $startDate)
    //                 ->whereDate('created_at', '<=', $endDate)
    //                 ->count();
    //             $totalEarnings = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate)
    //                 ->where('payment_status', 'completed')
    //                 ->sum('amount_to_pay');
    //         }
    //         else{
    //             $totalCustomers = 0;
    //             $totalEarnings = 0;
    //         }

    //         return Reply::dataOnly(['status' => 'success', 'totalBooking' => $totalBooking, 'pendingBooking' => $pendingBooking, 'approvedBooking' => $approvedBooking, 'inProgressBooking' => $inProgressBooking, 'completedBooking' => $completedBooking, 'canceledBooking' => $canceledBooking, 'offlineBooking' => $offlineBooking, 'onlineBooking' => $onlineBooking, 'totalCustomers' => $totalCustomers, 'totalEarnings' => round($totalEarnings, 2), 'user' => $this->user]);
    //     }

    //     if($this->user->is_admin){
    //         $recentSales = Booking::orderBy('id', 'desc')->take(20)->get();
    //     }
    //     else{
    //         $recentSales = null;
    //     }

    //     $todoItemsView = $this->generateTodoView();

    //     return view('admin.dashboard.index', compact( 'recentSales', 'todoItemsView'));
    // }

    // public function __invoke(Request $request)
    // {
    //     if(Session::has('outlet_slug'))
    //     {
    //         return redirect()->route('outlet.dashboard', Session::get('outlet_slug'));      
    //     }
    //     else
    //     {
    //         if(\request()->ajax()){

    //             $startDate = Carbon::createFromFormat('Y-m-d', $request->startDate);
    //             $endDate = Carbon::createFromFormat('Y-m-d', $request->endDate);

    //             $totalBooking = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate);
    //                 if(!$this->user->is_admin){
    //                     $totalBooking = ($this->user->is_employee) ? $totalBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $totalBooking->where('user_id', $this->user->id);
    //                 }
    //                 $totalBooking = $totalBooking->count();

    //             $inProgressBooking = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate)
    //                 ->where('status', 'in progress');
    //                 if(!$this->user->is_admin){
    //                     $inProgressBooking = ($this->user->is_employee) ? $inProgressBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $inProgressBooking->where('user_id', $this->user->id);
    //                 }
    //             $inProgressBooking = $inProgressBooking->count();

    //             $pendingBooking = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate)
    //                 ->where('status', 'pending');
    //                 if(!$this->user->is_admin){
    //                     $pendingBooking = ($this->user->is_employee) ? $pendingBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $pendingBooking->where('user_id', $this->user->id);
    //                 }
    //                 $pendingBooking = $pendingBooking->count();

    //             $approvedBooking = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate)
    //                 ->where('status', 'approved');
    //                 if(!$this->user->is_admin){
    //                     $approvedBooking = ($this->user->is_employee) ? $approvedBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $approvedBooking->where('user_id', $this->user->id);
    //                 }
    //                 $approvedBooking = $approvedBooking->count();

    //             $completedBooking = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate)
    //                 ->where('status', 'completed');
    //                 if(!$this->user->is_admin){
    //                     $completedBooking =  ($this->user->is_employee) ? $completedBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $completedBooking->where('user_id', $this->user->id);
    //                 }
    //             $completedBooking = $completedBooking->count();

    //             $canceledBooking = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate)
    //                 ->where('status', 'canceled');
    //                 if(!$this->user->is_admin){
    //                     $canceledBooking = ($this->user->is_employee) ? $canceledBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $canceledBooking->where('user_id', $this->user->id);
    //                 }
    //             $canceledBooking = $canceledBooking->count();

    //             $offlineBooking = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate)
    //                 ->where('source', 'pos');
    //                 if(!$this->user->is_admin){
    //                     $offlineBooking = ($this->user->is_employee) ? $offlineBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $offlineBooking->where('user_id', $this->user->id);
    //                 }
    //             $offlineBooking = $offlineBooking->count();

    //             $onlineBooking = Booking::whereDate('date_time', '>=', $startDate)
    //                 ->whereDate('date_time', '<=', $endDate)
    //                 ->where('source', 'online');
    //                 if(!$this->user->is_admin){
    //                     $onlineBooking = ($this->user->is_employee) ? $onlineBooking->whereHas('users', function($query) { return $query->where('user_id', $this->user->id); }) : $onlineBooking->where('user_id', $this->user->id);
    //                 }
    //             $onlineBooking = $onlineBooking->count();

    //             if($this->user->is_admin){
    //                 $totalCustomers = User::allCustomers()
    //                     ->whereDate('created_at', '>=', $startDate)
    //                     ->whereDate('created_at', '<=', $endDate)
    //                     ->count();
    //                 $totalEarnings = Booking::whereDate('date_time', '>=', $startDate)
    //                     ->whereDate('date_time', '<=', $endDate)
    //                     ->where('payment_status', 'completed')
    //                     ->sum('amount_to_pay');
    //             }
    //             else{
    //                 $totalCustomers = 0;
    //                 $totalEarnings = 0;
    //             }

    //             return Reply::dataOnly(['status' => 'success', 'totalBooking' => $totalBooking, 'pendingBooking' => $pendingBooking, 'approvedBooking' => $approvedBooking, 'inProgressBooking' => $inProgressBooking, 'completedBooking' => $completedBooking, 'canceledBooking' => $canceledBooking, 'offlineBooking' => $offlineBooking, 'onlineBooking' => $onlineBooking, 'totalCustomers' => $totalCustomers, 'totalEarnings' => round($totalEarnings, 2), 'user' => $this->user]);
    //         }

    //         if($this->user->is_admin){
    //             $recentSales = Booking::orderBy('id', 'desc')->take(20)->get();
    //         }
    //         else{
    //             $recentSales = null;
    //         }

    //         $todoItemsView = $this->generateTodoView();

    //         return view('admin.dashboard.index', compact( 'recentSales', 'todoItemsView'));
    //     }
    // }

    public function __invoke(Request $request)
    {
        if(Session::has('outlet_slug'))
        {
            return redirect()->route('outlet.dashboard', Session::get('outlet_slug'));      
        }
        else
        {
            $outlet = Outlet::where('status', 'active')->get();

            $user = Auth::user();

            if($user->is_admin){
                $recentSales = Booking::orderBy('id', 'desc')->take(20)->get();
            }
            else{
                $recentSales = null;
            }

            $todoItemsView = $this->generateTodoView();

            return view('admin.dashboard.index', compact('outlet', 'recentSales', 'todoItemsView'));
        }
    }

    public function get_dashboard_data(Request $request)
    {
        if(\request()->ajax())
        {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->startDate);
            $endDate = Carbon::createFromFormat('Y-m-d', $request->endDate);

            $user = Auth::user();

            $totalBooking = 0;

            $inProgressBooking = 0;

            $pendingBooking = 0;

            $approvedBooking = 0;

            $completedBooking = 0;

            $canceledBooking = 0;

            $offlineBooking = 0;

            $onlineBooking = 0;

            if($user->is_admin){
                $totalCustomers = User::allCustomers()
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->count();
                $totalEarnings = Booking::whereDate('date_time', '>=', $startDate)
                    ->whereDate('date_time', '<=', $endDate)
                    ->where('payment_status', 'completed')
                    ->sum('amount_to_pay');
            }
            else{
                $totalCustomers = 0;
                $totalEarnings = 0;
            }

            $outlet = Outlet::where('status', 'active')->get();

            $data = [
                'totalBooking' => $totalBooking, 
                'pendingBooking' => $pendingBooking, 
                'approvedBooking' => $approvedBooking, 
                'inProgressBooking' => $inProgressBooking, 
                'completedBooking' => $completedBooking, 
                'canceledBooking' => $canceledBooking, 
                'offlineBooking' => $offlineBooking, 
                'onlineBooking' => $onlineBooking, 
                'totalCustomers' => $totalCustomers, 
                'totalEarnings' => round($totalEarnings, 2), 
                'user' => $user,
                'outlet' => $outlet
            ];

            return view('admin.dashboard.partials.dashboard-data', $data);
        }
    }
}
