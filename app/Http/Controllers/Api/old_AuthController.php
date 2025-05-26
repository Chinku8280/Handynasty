<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use App\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends ResponseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string',
            'lname' => 'required|string',
            'dob' => 'required|date',
            'mobile' => 'required|numeric',
            'referral_code' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $userRole = Role::where('name', 'customer')->withoutGlobalScopes()->first()->id;
        $user->roles()->attach($userRole);
        if ($user) {
            $success['token'] =  $user->createToken('token')->accessToken;
            return $this->sendResponse($success, 'User register successfully.');
        } else {
            $error = "Sorry! Registration is not successfull.";
            return $this->sendError($error, 401);
        }
    }


    /**
     * Login a user and generate access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */


    // ...


    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return $this->sendError('Invalid Credentials');
        }

        $user = auth()->user();
        $accessToken = $user->createToken('authToken')->accessToken;

        return $this->sendResponse(['user' => $user, 'access_token' => $accessToken], 'Login successful');
    }


    //getuser
    public function getUser(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $success['user'] = $user;
            return $this->sendResponse($success, 'User retrieved successfully.');
        } else {
            $error = "User not found. Please check your authorization token.";
            return $this->sendError($error, 401); // 401 indicates Unauthorized status
        }
    }


    //logout
    public function logout(Request $request)
{
    $user = $request->user();

    if ($user) {
        // Check if the user has an active token before revoking
        if ($user->token()) {
            $user->token()->revoke();
            $success = 'User Logout successfully.';
            return $this->sendResponse(null, $success);
        } else {
            $errorMessages = 'User does not have an active token.';
            return $this->sendError($errorMessages);
        }
    } else {
        $errorMessages = 'User not found.';
        return $this->sendError($errorMessages);
    }
}

}
