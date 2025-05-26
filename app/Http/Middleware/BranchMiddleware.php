<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Location;
use Closure;
use Illuminate\Support\Facades\Auth;

class BranchMiddleware extends Controller
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
        
         $branchUrl = $request->segment(1); // Assuming the branch URL is the first URL segment
         $branch = Location::where('name', $branchUrl)->first();
      
         if (!$branch) {
 
             return abort(404);
         }
         if ($request->is("{$branchUrl}/login")) {
 
             redirect("/login");
         }
 
         else{
             return redirect("{$branchUrl}/login");
         }
     }
}
