@php
    if(isset($health_qstn))
    {
        if($health_qstn->update_flag == false)
        {
            $readonly = 'readonly';
            $disabled = "disabled";
            $button_show = false;
            $display_flag = false;
        }
        else 
        {
            $readonly = '';
            $disabled = "";
            $button_show = true;
            $display_flag = true;
        }
    }
    else 
    {
        $readonly = '';
        $disabled = "";
        $button_show = true;
        $display_flag = true;
    }
@endphp

<div class="tab-pane" id="pc-9" role="tabpanel">
    <div class="tab-content p-3 text-muted">
        <div class="tab-pane active" role="tabpanel">

            <h4 class="mb-3 text-center">Health Questionnaire</h4>

            <form action="" method="POST" id="health_qstn_form" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <small>Please answer Yes or No. If yes, please provide more details.</small>
                    </div>

                    <div>
                        <button type="button" class="btn btn-danger" id="no_to_all_btn" {{$disabled ?? ''}}>No to all</button>
                    </div>
                </div>
         
                <table class="table table-bordered w-100 mt-4">
                    <thead>
                        <tr>
                            <th>Medical History</th>
                            <th>Yes</th>
                            <th>No</th>
                            <th>Unsure</th>
                            <th style="width: 40%;">If 'Yes', please provide details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                Have you undergone any form of surgery in the past 6 months?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="surgery" id="surgery_yes" value="yes" {{ optional($health_qstn)->surgery == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="surgery_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="surgery" id="surgery_no" value="no" {{ optional($health_qstn)->surgery == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="surgery_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="surgery" id="surgery_unsure" value="unsure" {{ optional($health_qstn)->surgery == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="surgery_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="surgery_details" id="surgery_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->surgery_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Are you on any form of medication?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="any_form_of_medication" id="any_form_of_medication_yes" value="yes" {{ optional($health_qstn)->any_form_of_medication == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="any_form_of_medication_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="any_form_of_medication" id="any_form_of_medication_no" value="no" {{ optional($health_qstn)->any_form_of_medication == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="any_form_of_medication_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="any_form_of_medication" id="any_form_of_medication_unsure" value="unsure" {{ optional($health_qstn)->any_form_of_medication == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="any_form_of_medication_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="any_form_of_medication_details" id="any_form_of_medication_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->any_form_of_medication_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have any drug allergies?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="any_drug_allergies" id="any_drug_allergies_yes" value="yes" {{ optional($health_qstn)->any_drug_allergies == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="any_drug_allergies_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="any_drug_allergies" id="any_drug_allergies_no" value="no" {{ optional($health_qstn)->any_drug_allergies == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="any_drug_allergies_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="any_drug_allergies" id="any_drug_allergies_unsure" value="unsure" {{ optional($health_qstn)->any_drug_allergies == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="any_drug_allergies_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="any_drug_allergies_details" id="any_drug_allergies_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->any_drug_allergies_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have asthma or any respiratory problems?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="asthma_problems" id="asthma_problems_yes" value="yes" {{ optional($health_qstn)->asthma_problems == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="asthma_problems_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="asthma_problems" id="asthma_problems_no" value="no" {{ optional($health_qstn)->asthma_problems == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="asthma_problems_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="asthma_problems" id="asthma_problems_unsure" value="unsure" {{ optional($health_qstn)->asthma_problems == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="asthma_problems_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="asthma_problems_details" id="asthma_problems_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->asthma_problems_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have high blood pressure?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="high_blood_pressure" id="high_blood_pressure_yes" value="yes" {{ optional($health_qstn)->high_blood_pressure == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="high_blood_pressure_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="high_blood_pressure" id="high_blood_pressure_no" value="no" {{ optional($health_qstn)->high_blood_pressure == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="high_blood_pressure_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="high_blood_pressure" id="high_blood_pressure_unsure" value="unsure" {{ optional($health_qstn)->high_blood_pressure == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="high_blood_pressure_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="high_blood_pressure_details" id="high_blood_pressure_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->high_blood_pressure_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have low blood pressure?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="low_blood_pressure" id="low_blood_pressure_yes" value="yes" {{ optional($health_qstn)->low_blood_pressure == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="low_blood_pressure_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="low_blood_pressure" id="low_blood_pressure_no" value="no" {{ optional($health_qstn)->low_blood_pressure == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="low_blood_pressure_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="low_blood_pressure" id="low_blood_pressure_unsure" value="unsure" {{ optional($health_qstn)->low_blood_pressure == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="low_blood_pressure_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="low_blood_pressure_details" id="low_blood_pressure_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->low_blood_pressure_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have diabetes?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="diabetes" id="diabetes_yes" value="yes" {{ optional($health_qstn)->diabetes == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="diabetes_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="diabetes" id="diabetes_no" value="no" {{ optional($health_qstn)->diabetes == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="diabetes_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="diabetes" id="diabetes_unsure" value="unsure" {{ optional($health_qstn)->diabetes == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="diabetes_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="diabetes_details" id="diabetes_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->diabetes_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you suffer from Depression of Anxiety?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="depression" id="depression_yes" value="yes" {{ optional($health_qstn)->depression == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="depression_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="depression" id="depression_no" value="no" {{ optional($health_qstn)->depression == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="depression_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="depression" id="depression_unsure" value="unsure" {{ optional($health_qstn)->depression == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="depression_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="depression_details" id="depression_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->depression_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have Eczema or any form of skin allergies?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="skin_allergies" id="skin_allergies_yes" value="yes" {{ optional($health_qstn)->skin_allergies == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skin_allergies_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="skin_allergies" id="skin_allergies_no" value="no" {{ optional($health_qstn)->skin_allergies == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skin_allergies_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="skin_allergies" id="skin_allergies_unsure" value="unsure" {{ optional($health_qstn)->skin_allergies == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skin_allergies_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="skin_allergies_details" id="skin_allergies_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->skin_allergies_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have any injuries or experience pain in your joints, neck, arms, legs and torso?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="injuries_pain" id="injuries_pain_yes" value="yes" {{ optional($health_qstn)->injuries_pain == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="injuries_pain_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="injuries_pain" id="injuries_pain_no" value="no" {{ optional($health_qstn)->injuries_pain == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="injuries_pain_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="injuries_pain" id="injuries_pain_unsure" value="unsure" {{ optional($health_qstn)->injuries_pain == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="injuries_pain_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="injuries_pain_details" id="injuries_pain_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->injuries_pain_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have any mobility limitations?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="mobility_limitations" id="mobility_limitations_yes" value="yes" {{ optional($health_qstn)->mobility_limitations == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mobility_limitations_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="mobility_limitations" id="mobility_limitations_no" value="no" {{ optional($health_qstn)->mobility_limitations == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mobility_limitations_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="mobility_limitations" id="mobility_limitations_unsure" value="unsure" {{ optional($health_qstn)->mobility_limitations == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mobility_limitations_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="mobility_limitations_details" id="mobility_limitations_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->mobility_limitations_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have any heart problems?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="heart_problems" id="heart_problems_yes" value="yes" {{ optional($health_qstn)->heart_problems == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="heart_problems_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="heart_problems" id="heart_problems_no" value="no" {{ optional($health_qstn)->heart_problems == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="heart_problems_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="heart_problems" id="heart_problems_unsure" value="unsure" {{ optional($health_qstn)->heart_problems == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="heart_problems_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="heart_problems_details" id="heart_problems_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->heart_problems_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Are you currently using a pacemaker?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="using_pacemaker" id="using_pacemaker_yes" value="yes" {{ optional($health_qstn)->using_pacemaker == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="using_pacemaker_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="using_pacemaker" id="using_pacemaker_no" value="no" {{ optional($health_qstn)->using_pacemaker == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="using_pacemaker_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="using_pacemaker" id="using_pacemaker_unsure" value="unsure" {{ optional($health_qstn)->using_pacemaker == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="using_pacemaker_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="using_pacemaker_details" id="using_pacemaker_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->using_pacemaker_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>For Women Only</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>    
                        </tr>

                        <tr>
                            <td>
                                Are you pregnant?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="pregnant" id="pregnant_yes" value="yes" {{ optional($health_qstn)->pregnant == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregnant_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="pregnant" id="pregnant_no" value="no" {{ optional($health_qstn)->pregnant == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregnant_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="pregnant" id="pregnant_unsure" value="unsure" {{ optional($health_qstn)->pregnant == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pregnant_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="pregnant_details" id="pregnant_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->pregnant_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Do you have irregular periods?
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="irregular_periods" id="irregular_periods_yes" value="yes" {{ optional($health_qstn)->irregular_periods == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="irregular_periods_yes">Yes</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="irregular_periods" id="irregular_periods_no" value="no" {{ optional($health_qstn)->irregular_periods == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="irregular_periods_no">No</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="irregular_periods" id="irregular_periods_unsure" value="unsure" {{ optional($health_qstn)->irregular_periods == 'unsure' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="irregular_periods_unsure">Unsure</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea name="irregular_periods_details" id="irregular_periods_details" class="form-control" {{$readonly ?? ''}}>{{$health_qstn->irregular_periods_details ?? ''}}</textarea>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-group"> 
                    <label for="">If any of the answers above is yes, please specific:</label>
                    <input type="text" name="addons_field" class="form-control" value="{{ $health_qstn->addons_field ?? '' }}" {{$readonly ?? ''}}>
                </div>

                <div>
                    <h4>Declaration</h4>
                    <p>I acknowledge that the above information given by me is complete and accurate to the best of my knowledge and that no fact that is likely to influence the safety of the treatment(s) that I have signed up for have been withheld.</p>
                </div>

                <table class="table table-bordered w-100" style="margin-bottom: 0;">
                    <tbody>
                        <tr>
                            <td rowspan="2" style="width: 50%;">
                                <div class="form-group">
                                    <label for="">Signature of Customer</label>

                                    @if ($display_flag == false)
                                        <div>
                                            <img src="{{$health_qstn->customer_signature_image_url ?? ''}}" alt="" style="width: 20%;">
                                        </div>
                                    @else
                                        <input type="file" name="customer_signature" class="form-control">
                                        
                                        @if (isset($health_qstn->customer_signature))
                                            <div class="mt-2">
                                                <img src="{{$health_qstn->customer_signature_image_url ?? ''}}" alt="" style="width: 20%;">
                                            </div>
                                        @endif                                 
                                    @endif                                  
                                </div>
                            </td>
                            <td style="width: 50%;">
                                <div class="form-group">
                                    <label for="">Name of Customer</label>
                                    <input type="text" class="form-control" name="customer_name" value="{{ $health_qstn->customer_name ?? $customer->name }}" {{$readonly ?? ''}} required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="">NRIC / FIN No.</label>
                                    <input type="text" class="form-control" name="customer_nric_fin_no" value="{{ $health_qstn->customer_nric_fin_no ?? '' }}" {{$readonly ?? ''}} required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="">Date</label>
                                    <input type="date" class="form-control" name="created_date" {{$readonly ?? ''}} value="{{ $health_qstn->created_date ?? '' }}" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="">H/P No.</label>
                                    <input type="text" class="form-control" name="customer_hp_no" value="{{ $health_qstn->customer_hp_no ?? $customer->mobile }}" {{$readonly ?? ''}} required>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    
                <div>
                    <small><strong>Confidentiality Note:</strong> The information provided in the health questionnaire is for the sole purpose of carrying out safe and effective treatment(s) and will be kept strictly confidential.</small>
                </div>
                
                <div class="mt-4">
                    <h4>For every subsequent visit within 6 months</h4>
                    <p>I declared that my health condition had remained unchanged, as stated in this health questionnaire.</p>
                </div>

                <table class="table table-bordered w-100 multi_date_signature_table">
                    <tbody>
                        <tr>
                            <th style="width: 10%;">Date</th>  
                            <td>{{ isset($health_qstn->health_question_customer_signatures[0]->created_date) ? date('d-m-Y', strtotime($health_qstn->health_question_customer_signatures[0]->created_date)) : '' }}</td>      
                            <td>{{ isset($health_qstn->health_question_customer_signatures[1]->created_date) ? date('d-m-Y', strtotime($health_qstn->health_question_customer_signatures[1]->created_date)) : '' }}</td>      
                            <td>{{ isset($health_qstn->health_question_customer_signatures[2]->created_date) ? date('d-m-Y', strtotime($health_qstn->health_question_customer_signatures[2]->created_date)) : '' }}</td>      
                            <td>{{ isset($health_qstn->health_question_customer_signatures[3]->created_date) ? date('d-m-Y', strtotime($health_qstn->health_question_customer_signatures[3]->created_date)) : '' }}</td>      
                            <td>{{ isset($health_qstn->health_question_customer_signatures[4]->created_date) ? date('d-m-Y', strtotime($health_qstn->health_question_customer_signatures[4]->created_date)) : '' }}</td>      
                            <td>{{ isset($health_qstn->health_question_customer_signatures[5]->created_date) ? date('d-m-Y', strtotime($health_qstn->health_question_customer_signatures[5]->created_date)) : '' }}</td>                                       
                        </tr>
                        <tr>
                            <th>Signature</th>
                            <td>
                                <img src="{{$health_qstn->health_question_customer_signatures[0]->customer_signature_image_url ?? ''}}" alt="">
                            </td>
                            <td>
                                <img src="{{$health_qstn->health_question_customer_signatures[1]->customer_signature_image_url ?? ''}}" alt="">
                            </td>
                            <td>
                                <img src="{{$health_qstn->health_question_customer_signatures[2]->customer_signature_image_url ?? ''}}" alt="">
                            </td>
                            <td>
                                <img src="{{$health_qstn->health_question_customer_signatures[3]->customer_signature_image_url ?? ''}}" alt="">
                            </td>
                            <td>
                                <img src="{{$health_qstn->health_question_customer_signatures[4]->customer_signature_image_url ?? ''}}" alt="">
                            </td>
                            <td>
                                <img src="{{$health_qstn->health_question_customer_signatures[5]->customer_signature_image_url ?? ''}}" alt="">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary" {{$disabled ?? ''}}>Submit</button>
                </div>
                
            </form>
        </div>
    </div>
</div>