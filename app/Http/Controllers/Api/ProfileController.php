<?php

namespace App\Http\Controllers\Api;

use App\Helper\Files;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function get_profile(Request $request)
    {
        $user_id = Auth::user()->id;

        $user = User::find($user_id);

        if(!empty($user->dob))
        {
            $user->dob = date('d-m-Y', strtotime($user->dob));
        }        

        if($user)
        {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $user
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'data' => $user
            ]);
        }
        
    }

    public function update_profile(Request $request)
    {
        try
        {
            $user_id = Auth::user()->id;

            $validator = Validator::make($request->all(), [
                // 'fname' => 'required|string',
                // 'lname' => 'required|string',
                'full_name' => 'required|string',
                'dob' => 'nullable',
                'mobile' => "required|digits:8|unique:users,mobile,".$user_id,
                "email" => "required|email|unique:users,email,".$user_id,
                'gender' => 'nullable',
                'residential_address' => 'required',
            ]);

            if($validator->fails())
            {
                // $error = $validator->errors();
                // return response()->json(['status'=> false, 'message' => 'error', 'error'=>$error]);

                $errors = $validator->errors()->all();
                foreach($errors as $item)
                {
                    return response()->json(['status'=>false, 'message' => $item]);
                }  
            }
            else
            {
                $user = User::find($user_id);

                if($user)
                {
                    if($request->hasFile('image'))
                    {
                        $profile_image = $request->file('image');
                        $ext = $profile_image->extension();
                        $profile_img_file = rand(10000000000, 9999999999) . date("YmdHis").".".$ext;

                        $profile_image->move(public_path('user-uploads/avatar'), $profile_img_file);

                        $user->image = $profile_img_file;                       
                    }

                    // $user->fname = $request->fname;
                    // $user->lname = $request->lname;
                    // $user->name = $request->fname . " " . $request->lname;
                    $user->name = $request->full_name;
                    $user->email = $request->email;
                    $user->mobile = $request->mobile;

                    if($request->filled('dob'))
                    {
                        $user->dob = date('Y-m-d', strtotime($request->dob));
                    }

                    if($request->filled('gender'))
                    {
                        $user->gender = $request->gender;     
                    }

                    $user->residential_address = $request->residential_address; 
                    
                    $result = $user->save();

                    if($result)
                    {
                        return response()->json([
                            'status' => true,
                            'message' => 'Profile information updated',
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            "status" => false, 
                            "message" => "Profile information is not updated"
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
        catch (Exception $e) 
        {
            return response()->json(['status'=> false, 'error' => $e->getMessage()]);
        }
    }
}
