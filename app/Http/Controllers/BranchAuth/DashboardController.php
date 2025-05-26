<?php

namespace App\Http\Controllers\BranchAuth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index($branchName)
    {
        dd("Controller hit for branch: $branchName");

        return view('branch.dashboard');
        
    }
}
