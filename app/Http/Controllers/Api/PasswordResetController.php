<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    // forget password start

    public function send_otp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email'
            ]
        );

        if($validator->fails())
        {
            // $errors = $validator->errors();
            // return response()->json(['status'=>false, 'message' => 'error', 'error'=>$errors]);

            $error = $validator->errors()->all();

            foreach($error as $item)
            {
                return response()->json(['status' => false, 'message' => $item]);
            }
        }
        else
        {
            $user = User::where('email', $request->email)->first();

            if($user)
            {
                $otp = rand(1000, 9999);

                $user->otp = $otp;
                $user->save();

                // mail send start

                $data['to'] = $user->email;
                $data['subject'] = "Reset Password Mail";
                $data['otp'] = $otp;

                Mail::send('emails.send-otp', $data, function ($message) use ($data) {
                    $message->to($data['to'])
                            ->subject($data['subject']);
                });

                // mail send end

                return response()->json([
                    'status' => true,
                    'message' => "OTP sent successfully."
                ]);
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => "User not found."
                ]);
            }
        }
    }

    public function verify_otp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'otp' => 'required|digits:4'
            ]
        );

        if($validator->fails())
        {
            // $errors = $validator->errors();
            // return response()->json(['status'=>false, 'message' => 'error', 'error'=>$errors]);

            $error = $validator->errors()->all();

            foreach($error as $item)
            {
                return response()->json(['status' => false, 'message' => $item]);
            }
        }
        else
        {
            $user = User::where('email', $request->email)->first();

            if($user)
            {
                if($user->otp == $request->otp)
                {
                    $user->otp = "";
                    $user->save();
    
                    return response()->json([
                        'status' => true,
                        'message' => "OTP verified successfully."
                    ]);
                }
                else
                {
                    return response()->json([
                        'status' => false,
                        'message' => "Invalid OTP."
                    ]);
                }              
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => "User not found."
                ]);
            }
        }
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
            ]
        );

        if($validator->fails())
        {
            // $errors = $validator->errors();
            // return response()->json(['status'=>false, 'message' => 'error', 'error'=>$errors]);

            $error = $validator->errors()->all();

            foreach($error as $item)
            {
                return response()->json(['status' => false, 'message' => $item]);
            }
        }
        else
        {
            $user = User::where('email', $request->email)->first();

            if($user)
            {
                $user->password = $request->password;
                $user->save();  
                
                return response()->json([
                    'status' => true,
                    'message' => "Password updated successfully."
                ]);
            }
            else
            {
                return response()->json([
                    'status' => false,
                    'message' => "User not found."
                ]);
            }
        }
    }

    // forget password end
}
