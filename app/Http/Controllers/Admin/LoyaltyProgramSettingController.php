<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoyaltyProgramSettingController extends Controller
{
    // update

    public function update(Request $request)
    {
        // return $request->all();

        $validator = Validator::make(
            $request->all(),
            [
                'hours_per_stamp' => 'required',
                'loyalty_program_desc' => 'required',
                'expired_days' => 'required',
            ],
            [],
            [
                'hours_per_stamp' => 'Hours Per Stamp',
                'loyalty_program_desc' => 'Description',
                'expired_days' => 'Expired Days',
            ]
        );

        if($validator->fails())
        {
            $error = $validator->errors();

            return response()->json(['status' => "error", 'error'=>$error]);
        }
        else
        {
            DB::table('loyalty_program_settings')->delete();
    
            DB::table('loyalty_program_settings')->insert([
                'hours_per_stamp' => $request->hours_per_stamp,
                'description' => $request->loyalty_program_desc,
                'expired_days' => $request->expired_days,
            ]);

            $j = 1;

            for($i=0; $i<count($request->stamp_value); $i++)
            {
                $data[] = [
                    'stamp_no' => $request->stamp_no[$i],
                    'stamp_text' => $request->stamp_value[$i],
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ];              

                $j++;
            }

            DB::table('loyalty_program_stamp_text_settings')->delete();
            DB::table('loyalty_program_stamp_text_settings')->insert($data);
    
            return response()->json(['status'=>'success', 'message'=>"Data Updated Succesfully"]);
        }
    }
}
