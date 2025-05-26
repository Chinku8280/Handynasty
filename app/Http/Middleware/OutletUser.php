<?php

namespace App\Http\Middleware;

use App\Outlet;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutletUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // return $next($request);

        $outlet_slug = $request->route('outlet_slug');

        if(Auth::check())
        {          
            $outlet = Outlet::where('outlet_slug', $outlet_slug)->first();

            $user = Auth::user();

            if($user)
            {
                if ($user->is_employee) 
                {
                    $user_id = Auth::user()->id;

                    if (DB::table('employee_outlets')->where('outlet_id', $outlet->id)->where('user_id', $user_id)->exists()) 
                    {
                        return $next($request);
                    }
                }
            }        
        }

        return redirect()->route('outlet.login', $outlet_slug);
    }
}
