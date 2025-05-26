<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function clearCache(Request $request)
    {
        $redirectUrl = route('admin.dashboard');

        Artisan::call('cache:clear');
        return Reply::redirect($redirectUrl, 'Cache cleared successfully');
    }
}
