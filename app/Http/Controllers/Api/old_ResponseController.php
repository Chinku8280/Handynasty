<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;
use App\User;

class ResponseController extends Controller
{
    public function sendResponse($result = null, $message = null)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
    
        return response()->json($response, 200);
    }


    public function sendError($errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $errorMessages,
        ];
    
        return response()->json($response, $code);

    }
}
