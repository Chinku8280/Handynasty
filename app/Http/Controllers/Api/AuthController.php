<?php

namespace App\Http\Controllers\Api;

use App\Mail\forgotMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\PrivacyPolicy;
use App\Role;
use App\TermsAndCondition;
use App\WhoWeArePage;
use App\ServiceBanner;
use App\Voucher;
use App\VoucherRedeem;
use App\VoucherUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends ResponseController
{

    public function register(Request $request)
    {
        // return $request->all();

        $validator = Validator::make($request->all(), [
            // 'fname' => 'required|string',
            // 'lname' => 'required|string',
            'full_name' => 'required|string',
            'dob' => 'nullable',
            'mobile' => 'required|digits:8|unique:users',
            'referral_code' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|same:password',
            'gender' => 'nullable',
        ]);

        if($validator->fails())
        {
            // return $this->sendError($validator->errors());

            $errors = $validator->errors()->all();
            foreach($errors as $item)
            {
                return response()->json(['status'=>false, 'message' => $item]);
            }   
        }

        // $input = $request->all();
        // // $input['password'] = Hash::make($input['password']);   

        // $user = User::create($input);

        $user = new User;
        // $user->fname = $request->fname;
        // $user->lname = $request->lname;
        // $user->name = $request->fname . " " . $request->lname;
        $user->name = $request->full_name;

        if($request->filled('dob'))
        {
            $user->dob = date('Y-m-d', strtotime($request->dob));
        }

        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->password = $request->password;   
        
        if($request->filled('gender'))
        {
            $user->gender = $request->gender;     
        }
        
        if($request->filled('referral_code'))
        {
            $user->referral_code = $request->referral_code;     
        }
        $user->status = 'active';
        $user->save();

        if($user)
        {
            // add customer role
            $userRole = Role::where('name', 'customer')->withoutGlobalScopes()->first()->id;

            $user->roles()->attach($userRole);

            $success['status'] = true;
            $success['token'] =  $user->createToken('token')->accessToken;
            $success['message'] = "Create Successfully";
            return $this->sendResponse($success);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Sorry! Registration is not successfull.'
            ]);
        }
        
    }
  

     /**
     * Login a user and generate access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    
    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|string|email',
    //         'password' => 'required'
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 422);
    //     }
    
    //     // Attempt to authenticate the user
    //     $credentials = $request->only('email', 'password');
    
    //     if (Auth::attempt($credentials)) 
    //     {
    //         // Get the authenticated user after successful authentication
    //         $user = Auth::user();

    //         // // Generate access token
    //         $token = $user->createToken('api_token')->accessToken;

    //         return response()->json([
    //             'status' => true, 
    //             'message' => 'Login successful', 
    //             'access_token' => $token,
    //             'user' => $user
    //         ]);
    //     }
    //     else
    //     {
    //         // Authentication failed
    //         return response()->json([
    //             'data'=> [
    //                 'status' => false, 
    //                 'error' => 'Invalid Creadential',
    //             ]
    //         ]);
    //     }
    // }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_mobile' => 'required',
            'password' => 'required'
        ]);     
    
        if($validator->fails())
        {
            $errors = $validator->errors()->all();
            foreach($errors as $item)
            {
                return response()->json(['status'=>false, 'message' => $item]);
            }   
        }
        else
        {       
            $email_or_mobile = $request->input('email_or_mobile');
            $password = $request->input('password');       

            // Check if login input is email or mobile number
            // $user = User::where('email', $email_or_mobile)->orWhere('mobile', $email_or_mobile)->first();

            $user = User::where(function($query) use ($email_or_mobile) {
                                $query->where('email', $email_or_mobile)
                                    ->orWhere('mobile', $email_or_mobile);
                            })
                            ->where('status', 'active')
                            ->first();

            if($user)
            {
                if (Hash::check($password, $user->password)) 
                {
                    // Generate access token
                    $token = $user->createToken('api_token')->accessToken;

                    $user->login_count += 1;
                    $user->save();

                    // if($user->login_count >= 1)
                    // {
                    //     $this->received_welcome_voucher($user->id);
                    // }

                    return response()->json([
                        'status' => true, 
                        'message' => 'Login successful', 
                        'access_token' => $token,
                        'user' => $user
                    ]);
                }
                else
                {
                    return response()->json(['status'=>false, 'message' => 'Invalid credentials']);
                }
            }
            else
            {
                return response()->json(['status'=>false, 'message' => 'Invalid credentials']);
            }
        }
    }
    
    public function logout(Request $request)
    {
        Auth::user()->tokens()->delete();

        // $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function my_loyality_points()
    {
        $user = Auth::user();
    
        if (!$user) {
            return $this->sendError('User not found', 404);
        }
    
        $loyaltyPoints = $user->loyalty_points;
    
        return response()->json([
            'status' => true,
            'message' => 'success',
            'loyalty_points' => $loyaltyPoints
        ]);
    }

    public function updatePassword(Request $request)
    {
        // $validator = $request->validate([
        //     'old_password' => 'required',
        //     'new_password' => 'required|confirmed',
        //     'new_password_confirmation' => 'required|same:new_password'
        // ]);

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'All feilds are required',
                'errors' => $validator->errors()
            ], 200);
        }
        $user = auth()->user();
    
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false, 
                'message' => 'Old Password Does not match!'
            ],200);
        }
    
        if (Hash::check($request->new_password, $user->password)) 
        {
            return response()->json([
                'status' => false, 
                'message' => 'New Password should be different from the Old Password!'
            ],200);
        }
    
        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Your Password has been Changed Successfully.'
        ],200);
    }

    public static function received_welcome_voucher($user_id)
    {
        $user = User::find($user_id);

        // $user_dob = date('Y-m-d', strtotime($user->dob));
        $user_dob = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
        $user_age = Carbon::parse($user_dob)->age;
        $user_gender = $user->gender;

        $welcome_vouchers = Voucher::where('status', 'active')
                                    ->whereDate('start_date_time', '<=', date('Y-m-d'))
                                    ->whereDate('end_date_time', '>=', date('Y-m-d'))
                                    ->where('is_welcome', 1)
                                    ->get();

        foreach($welcome_vouchers as $voucher)
        {
            // check voucher uses limit active or not start

            if($voucher->uses_limit == null || $voucher->uses_limit == "")
            {
                $redeemed_status = 'active';
            }
            else if($voucher->uses_limit == 0)
            {
                $redeemed_status = 'deactive';
            }
            else
            {
                if(($voucher->used_time != $voucher->uses_limit))
                {
                    $redeemed_status = 'active';
                }
                else
                {
                    $redeemed_status = 'deactive';
                }
            }

            // check coupon uses limit active or not end

            if($redeemed_status == "active")
            {              
                if(strtotime(date('Y-m-d')) >= strtotime($voucher->start_date_time) && strtotime(date('Y-m-d')) <= strtotime($voucher->end_date_time))
                {
                    if($voucher->max_age >= $user_age && $voucher->min_age <= $user_age)
                    {
                        if(DB::table('voucher_gender')->where('voucher_id', $voucher->id)->where('gender', $user_gender)->exists())
                        {                                                     
                            if(!VoucherRedeem::where('user_id', $user_id)->where('voucher_id', $voucher->id)->exists())
                            {                               
                                $voucher_redeem = new VoucherRedeem;

                                $voucher_redeem->voucher_id = $voucher->id;
                                $voucher_redeem->user_id = $user_id;

                                $result = $voucher_redeem->save();

                                if($result)
                                {
                                    $voucher->used_time += 1;
                                    $voucher->save();                         
                                }                                                            
                            }                                                   
                        }                         
                    }                 
                }                      
            }
        }

        return true;
    }

    // get_consumer_player_id

    public function get_consumer_player_id(Request $request)
    {
        $user_id = Auth::user()->id;

        if($request->filled('player_id'))
        {
            $player_id = $request->player_id;

            $user = User::find($user_id);

            $user->player_id = $player_id;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Player Id is saved'
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Player Id is required'
            ]);
        }
    }

    public function delete_customer(Request $request)
    {
        $user_id = Auth::user()->id;

        $user = User::find($user_id);

        if($user)
        {
            $user->status = "inactive";
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Customer deleted successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    }
    
}
