<?php

namespace App\Http\Controllers\Api;

use App\HealthQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HealthQuestionController extends Controller
{
    public function display_form()
    {
        $user_id = Auth()->user()->id;

        $health_qstn = HealthQuestion::where('customer_id', $user_id)->first();
        
        if ($health_qstn) 
        {
            $createdDate = Carbon::parse($health_qstn->created_date); // Convert to Carbon
            if ($createdDate->addMonths(6) < now()) 
            {
                $display_flag = true;
            }
            else
            {
                $display_flag = false;
            }           
        }
        else
        {
            $display_flag = true;
        }

        $data = [
            'display_flag' => $display_flag
        ];

        return response()->json([
            'status' => true,
            'message' => 'Display Form',
            'data' => $data
        ]);
    }

    // public function store(Request $request)
    // {
    //     // return $request->all();    

    //     $validator = Validator::make($request->all(), [
    //         'temperature_reading' => 'required',
    //         'respiratory_symptoms_cough' => 'required',
    //         'respiratory_symptoms_runny_nose' => 'required',
    //         'respiratory_symptoms_sore_throat' => 'required',
    //         'respiratory_symptoms_shortness_breath' => 'required',
    //         'visited_covid_areas' => 'required',
    //         'under_quarantine_order' => 'required',
    //         'surgery' => 'required',
    //         'any_form_of_medication' => 'required',
    //         'any_drug_allergies' => 'required',
    //         'asthma_problems' => 'required',
    //         'high_blood_pressure' => 'required',
    //         'low_blood_pressure' => 'required',
    //         'diabetes' => 'required',
    //         'depression' => 'required',
    //         'skin_allergies' => 'required',
    //         'injuries_pain' => 'required',
    //         'mobility_limitations' => 'required',
    //         'heart_problems' => 'required',
    //         'using_pacemaker' => 'required',
    //         'pregnant' => 'required',
    //         'irregular_periods' => 'required',     
    //         'created_date' => 'required|date',
    //         'customer_name' => 'required',
    //         'customer_signature' => 'required'
    //     ]);     

    //     if($validator->fails())
    //     {
    //         $errors = $validator->errors()->all();

    //         foreach($errors as $item)
    //         {
    //             return response()->json(['status'=>false, 'message' => $item]);
    //         }   
    //     }
    //     else
    //     {
    //         $user_id = Auth()->user()->id;

    //         if(HealthQuestion::where('customer_id', $user_id)->exists())
    //         {
    //             $health_qstn = HealthQuestion::where('customer_id', $user_id)->first();
    //             $old_signature = $health_qstn ? $health_qstn->customer_signature : '';
    //         }
    //         else
    //         {
    //             $health_qstn = new HealthQuestion();
    //         }          
    
    //         $health_qstn->customer_id = $user_id;
    //         $health_qstn->temperature_reading = $request->temperature_reading;
    //         $health_qstn->respiratory_symptoms_cough = $request->respiratory_symptoms_cough;
    //         $health_qstn->respiratory_symptoms_runny_nose = $request->respiratory_symptoms_runny_nose;
    //         $health_qstn->respiratory_symptoms_sore_throat = $request->respiratory_symptoms_sore_throat;
    //         $health_qstn->respiratory_symptoms_shortness_breath = $request->respiratory_symptoms_shortness_breath;
    //         $health_qstn->visited_covid_areas = $request->visited_covid_areas;
    //         $health_qstn->under_quarantine_order = $request->under_quarantine_order;
    //         $health_qstn->surgery = $request->surgery;
    //         $health_qstn->surgery_details = $request->surgery_details;
    //         $health_qstn->any_form_of_medication = $request->any_form_of_medication;
    //         $health_qstn->any_form_of_medication_details = $request->any_form_of_medication_details;
    //         $health_qstn->any_drug_allergies = $request->any_drug_allergies;
    //         $health_qstn->any_drug_allergies_details = $request->any_drug_allergies_details;
    //         $health_qstn->asthma_problems = $request->asthma_problems;
    //         $health_qstn->asthma_problems_details = $request->asthma_problems_details;
    //         $health_qstn->high_blood_pressure = $request->high_blood_pressure;
    //         $health_qstn->high_blood_pressure_details = $request->high_blood_pressure_details;
    //         $health_qstn->low_blood_pressure = $request->low_blood_pressure;
    //         $health_qstn->low_blood_pressure_details = $request->low_blood_pressure_details;
    //         $health_qstn->diabetes = $request->diabetes;
    //         $health_qstn->diabetes_details = $request->diabetes_details;
    //         $health_qstn->depression = $request->depression;
    //         $health_qstn->depression_details = $request->depression_details;
    //         $health_qstn->skin_allergies = $request->skin_allergies;
    //         $health_qstn->skin_allergies_details = $request->skin_allergies_details;
    //         $health_qstn->injuries_pain = $request->injuries_pain;
    //         $health_qstn->injuries_pain_details = $request->injuries_pain_details;
    //         $health_qstn->mobility_limitations = $request->mobility_limitations;
    //         $health_qstn->mobility_limitations_details = $request->mobility_limitations_details;
    //         $health_qstn->heart_problems = $request->heart_problems;
    //         $health_qstn->heart_problems_details = $request->heart_problems_details;
    //         $health_qstn->using_pacemaker = $request->using_pacemaker;
    //         $health_qstn->using_pacemaker_details = $request->using_pacemaker_details;           
    //         $health_qstn->pregnant = $request->pregnant;
    //         $health_qstn->pregnant_details = $request->pregnant_details;
    //         $health_qstn->irregular_periods = $request->irregular_periods;
    //         $health_qstn->irregular_periods_details = $request->irregular_periods_details;

    //         $health_qstn->customer_name = $request->customer_name;
    //         $health_qstn->created_date = date('Y-m-d', strtotime($request->created_date));

    //         // customer signature start

    //         if($request->hasFile('customer_signature'))
    //         {
    //             // Delete old signature if it exists
    //             if (isset($old_signature) && file_exists(public_path('/user-uploads/customer-signature/' . $old_signature))) {
    //                 unlink(public_path('/user-uploads/customer-signature/' . $old_signature));
    //             }              

    //             // Save new signature
    //             $customer_signature = $request->file('customer_signature');

    //             $ext = $customer_signature->extension();
    //             $customer_signature_file = rand(100000000, 99999999999).date("YmdHis").$user_id.".".$ext;

    //             $customer_signature->move(public_path('/user-uploads/customer-signature'), $customer_signature_file);
    //         }
    //         else
    //         {
    //             $customer_signature_file = $old_signature ?? "";
    //         }

    //         // customer signature end

    //         $health_qstn->customer_signature = $customer_signature_file;
            
    //         $result = $health_qstn->save();

    //         if($result)
    //         {
    //             return response()->json(['status'=>true, 'message'=>'Data stored successfully']);
    //         }
    //         else
    //         {
    //             return response()->json(['status'=>false, 'message'=>'Failed to data stored']);
    //         }
    //     }
    // }

    public function store(Request $request)
    {
        // return $request->all();    

        $validator = Validator::make($request->all(), [
            
            'surgery' => 'required',
            'any_form_of_medication' => 'required',
            'any_drug_allergies' => 'required',
            'asthma_problems' => 'required',
            'high_blood_pressure' => 'required',
            'low_blood_pressure' => 'required',
            'diabetes' => 'required',
            'depression' => 'required',
            'skin_allergies' => 'required',
            'injuries_pain' => 'required',
            'mobility_limitations' => 'required',
            'heart_problems' => 'required',
            'using_pacemaker' => 'required',
            'pregnant' => 'required',
            'irregular_periods' => 'required',     
            'created_date' => 'required|date',
            'customer_name' => 'required',
            'customer_nric_fin_no' => 'required',        
            'customer_hp_no' => 'required',        
            'customer_signature' => 'required'
        ]);     

        // Conditionally required if main value is "yes"
        $conditions = [
            'surgery' => 'surgery_details',
            'any_form_of_medication' => 'any_form_of_medication_details',
            'any_drug_allergies' => 'any_drug_allergies_details',
            'asthma_problems' => 'asthma_problems_details',
            'high_blood_pressure' => 'high_blood_pressure_details',
            'low_blood_pressure' => 'low_blood_pressure_details',
            'diabetes' => 'diabetes_details',
            'depression' => 'depression_details',
            'skin_allergies' => 'skin_allergies_details',
            'injuries_pain' => 'injuries_pain_details',
            'mobility_limitations' => 'mobility_limitations_details',
            'heart_problems' => 'heart_problems_details',
            'using_pacemaker' => 'using_pacemaker_details',
            'pregnant' => 'pregnant_details',
            'irregular_periods' => 'irregular_periods_details',
        ];

        // Loop to apply `sometimes` rule for each detail field
        foreach ($conditions as $main => $detail) {
            $validator->sometimes($detail, 'required', function ($input) use ($main) {
                return isset($input->$main) && strtolower($input->$main) === 'yes';
            });
        }

        // Conditionally require addons_field if any answer is "yes"
        $validator->sometimes('addons_field', 'required', function ($input) use ($conditions) {
            return collect(array_keys($conditions))
                ->some(fn($field) => isset($input->$field) && strtolower($input->$field) === 'yes');
        });

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
            $user_id = Auth()->user()->id;

            if(HealthQuestion::where('customer_id', $user_id)->exists())
            {
                $health_qstn = HealthQuestion::where('customer_id', $user_id)->first();
                $old_signature = $health_qstn ? $health_qstn->customer_signature : '';
            }
            else
            {
                $health_qstn = new HealthQuestion();
            }          
    
            $health_qstn->customer_id = $user_id;
            $health_qstn->surgery = $request->surgery;
            $health_qstn->surgery_details = $request->surgery_details;
            $health_qstn->any_form_of_medication = $request->any_form_of_medication;
            $health_qstn->any_form_of_medication_details = $request->any_form_of_medication_details;
            $health_qstn->any_drug_allergies = $request->any_drug_allergies;
            $health_qstn->any_drug_allergies_details = $request->any_drug_allergies_details;
            $health_qstn->asthma_problems = $request->asthma_problems;
            $health_qstn->asthma_problems_details = $request->asthma_problems_details;
            $health_qstn->high_blood_pressure = $request->high_blood_pressure;
            $health_qstn->high_blood_pressure_details = $request->high_blood_pressure_details;
            $health_qstn->low_blood_pressure = $request->low_blood_pressure;
            $health_qstn->low_blood_pressure_details = $request->low_blood_pressure_details;
            $health_qstn->diabetes = $request->diabetes;
            $health_qstn->diabetes_details = $request->diabetes_details;
            $health_qstn->depression = $request->depression;
            $health_qstn->depression_details = $request->depression_details;
            $health_qstn->skin_allergies = $request->skin_allergies;
            $health_qstn->skin_allergies_details = $request->skin_allergies_details;
            $health_qstn->injuries_pain = $request->injuries_pain;
            $health_qstn->injuries_pain_details = $request->injuries_pain_details;
            $health_qstn->mobility_limitations = $request->mobility_limitations;
            $health_qstn->mobility_limitations_details = $request->mobility_limitations_details;
            $health_qstn->heart_problems = $request->heart_problems;
            $health_qstn->heart_problems_details = $request->heart_problems_details;
            $health_qstn->using_pacemaker = $request->using_pacemaker;
            $health_qstn->using_pacemaker_details = $request->using_pacemaker_details;           
            $health_qstn->pregnant = $request->pregnant;
            $health_qstn->pregnant_details = $request->pregnant_details;
            $health_qstn->irregular_periods = $request->irregular_periods;
            $health_qstn->irregular_periods_details = $request->irregular_periods_details;
            $health_qstn->addons_field = $request->addons_field;

            $health_qstn->customer_name = $request->customer_name ?? Auth()->user()->name;
            $health_qstn->customer_nric_fin_no = $request->customer_nric_fin_no;
            $health_qstn->customer_hp_no = $request->customer_hp_no ?? Auth()->user()->mobile;           
            $health_qstn->created_date = date('Y-m-d', strtotime($request->created_date));

            // customer signature start

            if($request->hasFile('customer_signature'))
            {
                // Delete old signature if it exists
                // if (isset($old_signature) && file_exists(public_path('/user-uploads/customer-signature/' . $old_signature))) {
                //     unlink(public_path('/user-uploads/customer-signature/' . $old_signature));
                // }              

                // Save new signature
                $customer_signature = $request->file('customer_signature');

                $ext = $customer_signature->extension();
                $customer_signature_file = rand(100000000, 99999999999).date("YmdHis").$user_id.".".$ext;

                $customer_signature->move(public_path('/user-uploads/customer-signature'), $customer_signature_file);
            }
            else
            {
                $customer_signature_file = $old_signature ?? "";
            }

            // customer signature end

            $health_qstn->customer_signature = $customer_signature_file;
            
            $result = $health_qstn->save();

            if($result)
            {
                if(!empty($customer_signature_file))
                {
                    DB::table('health_question_customer_signatures')->insert([
                        'customer_id' => $user_id,
                        'customer_signature' => $customer_signature_file,
                        'created_date' => date('Y-m-d', strtotime($request->created_date)),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                return response()->json(['status'=>true, 'message'=>'Data stored successfully']);
            }
            else
            {
                return response()->json(['status'=>false, 'message'=>'Failed to data stored']);
            }
        }
    }

    // public function get_data()
    // {
    //     $user_id = Auth()->user()->id;

    //     $health_qstn = HealthQuestion::where('customer_id', $user_id)->first();

    //     if($health_qstn)
    //     {
    //         $health_qstn->created_date_format = date('d-m-Y');

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Health and Questionnarie Data',
    //             'data' => $health_qstn
    //         ]);
    //     }
    //     else
    //     {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data not found',
    //             'data' => $health_qstn
    //         ]);
    //     }
    // }

    // public function get_data()
    // {
    //     $user_id = Auth()->user()->id;

    //     $health_qstn = HealthQuestion::where('customer_id', $user_id)->get();

    //     if (!$health_qstn->isEmpty())
    //     {
    //         foreach($health_qstn as $item)
    //         {
    //             $item->created_date_format = date('d-m-Y');
    //         }
            
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Health and Questionnarie Data',
    //             'data' => $health_qstn
    //         ]);
    //     }
    //     else
    //     {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Data not found',
    //             'data' => $health_qstn
    //         ]);
    //     }
    // }

    public function get_data()
    {
        $user_id = Auth()->user()->id;

        $health_qstn = HealthQuestion::where('customer_id', $user_id)->get();

        if (!$health_qstn->isEmpty())
        {
            foreach($health_qstn as $item)
            {
                $item->created_date_format = $item->created_date ? date('d-m-Y', strtotime($item->created_date)) : '';

                $item->health_question_customer_signatures = DB::table('health_question_customer_signatures')
                                                                    ->select('created_date', 'customer_signature')
                                                                    ->where('customer_id', $user_id)
                                                                    ->orderBy('created_date', 'desc')
                                                                    ->limit(6)
                                                                    ->get();

                foreach($item->health_question_customer_signatures as $item_sign)
                {
                    $item_sign->created_date_format = $item_sign->created_date ? date('d-m-Y', strtotime($item_sign->created_date)) : '';
                    
                    if (is_null($item_sign->customer_signature) || empty($item_sign->customer_signature)) 
                    {
                        $item_sign->customer_signature_image_url = "";
                    }
                    else
                    {
                        $item_sign->customer_signature_image_url = asset('/user-uploads/customer-signature/' . $item_sign->customer_signature);
                    }
                }                                                       
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Health and Questionnarie Data',
                'data' => $health_qstn
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $health_qstn
            ]);
        }
    }
}
