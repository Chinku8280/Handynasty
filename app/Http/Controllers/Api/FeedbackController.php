<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\Feedback;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends ResponseController
{
    // public function send_feedback(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'first_name' => 'required|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'email' => 'required|email',
    //         'country' => 'nullable',
    //         'message' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError($validator->errors());       
    //     }

    //     $validatedData = $validator->validated();

    //     $feedback = new Feedback($validatedData);

    //     $feedback->save();

    //     if ($feedback) {
    //         $success['status'] = true;
    //         $success['message'] = "Feedback submitted successfully";
    //         return $this->sendResponse($success);
    //     } else {
    //         $error = "Sorry! Feedback is not submitted.";
    //         return $this->sendError($error, 401); 
    //     }
    // }

    public function send_feedback(Request $request)
    {
        $validator = Validator::make(
            $request->all(), 
            [
                'fullname' => 'required|string|max:255',
                'email' => 'required|email',
                'country' => 'required',
                'message' => 'required',
            ]
        );

        if ($validator->fails()) 
        {
            // $errors = $validator->errors();
            // return response()->json(['status'=>false, 'message' => 'error', 'error'=>$errors]);

            $errors = $validator->errors()->all();
            foreach($errors as $item)
            {
                return response()->json(['status'=>false, 'message' => $item]);
            }      
        }
        else
        {
            $user_id = Auth()->user()->id;

            $feedback = new Feedback();
            $feedback->customer_id = $user_id;
            $feedback->fullname = $request->fullname;
            $feedback->email = $request->email;
            $feedback->country = $request->country;
            $feedback->message = $request->message;

            $result = $feedback->save();
    
            if ($result) 
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Feedback submitted successfully'
                ]);
            } 
            else 
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Feedback is not submitted successfully'
                ]);
            }
        }
    }
}