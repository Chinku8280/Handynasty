<h4>Loyalty Program Settings</h4>
<br>
<form class="form-horizontal" id="loyalty_settings_form" method="POST">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="hours_per_stamp" class="control-label">1 Stamp = how many hours</label>

                <input type="number" class="form-control form-control-lg"
                       id="hours_per_stamp" name="hours_per_stamp"
                       value="{{$loyalty_program_settings->hours_per_stamp ?? 1}}" min="1" readonly required>
            </div>  
            <div class="form-group">
                <label for="hours_per_stamp" class="control-label">Description</label>

                <textarea class="form-control form-control-lg" name="loyalty_program_desc" id="loyalty_program_desc" cols="30" rows="5">{{$loyalty_program_settings->description ?? ''}}</textarea>
            </div> 
            
            <div class="form-group">
                <label for="loyalty_points_expired_days" class="control-label">Loyalty Program Expired days</label>

                <input type="number" class="form-control form-control-lg"
                       id="expired_days" name="expired_days"
                       value="{{$loyalty_program_settings->expired_days ?? ''}}" min="0" required>
            </div>     
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label for="">Stamp 1</label>

                <input type="hidden" name="stamp_no[]" value="stamp_1">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[0]->stamp_text)?$loyalty_program_stamp_text_settings[0]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 2</label>

                <input type="hidden" name="stamp_no[]" value="stamp_2">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[1]->stamp_text)?$loyalty_program_stamp_text_settings[1]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 3</label>

                <input type="hidden" name="stamp_no[]" value="stamp_3">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[2]->stamp_text)?$loyalty_program_stamp_text_settings[2]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 4</label>

                <input type="hidden" name="stamp_no[]" value="stamp_4">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[3]->stamp_text)?$loyalty_program_stamp_text_settings[3]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 5</label>

                <input type="hidden" name="stamp_no[]" value="stamp_5">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[4]->stamp_text)?$loyalty_program_stamp_text_settings[4]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 6</label>

                <input type="hidden" name="stamp_no[]" value="stamp_6">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[5]->stamp_text)?$loyalty_program_stamp_text_settings[5]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 7</label>

                <input type="hidden" name="stamp_no[]" value="stamp_7">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[6]->stamp_text)?$loyalty_program_stamp_text_settings[6]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 8</label>

                <input type="hidden" name="stamp_no[]" value="stamp_8">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[7]->stamp_text)?$loyalty_program_stamp_text_settings[7]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 9</label>

                <input type="hidden" name="stamp_no[]" value="stamp_9">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[8]->stamp_text)?$loyalty_program_stamp_text_settings[8]->stamp_text:''}}" required>
            </div>
 
            <div class="form-group">
                <label for="">Stamp 10</label>

                <input type="hidden" name="stamp_no[]" value="stamp_10">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[9]->stamp_text)?$loyalty_program_stamp_text_settings[9]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 11</label>

                <input type="hidden" name="stamp_no[]" value="stamp_11">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[10]->stamp_text)?$loyalty_program_stamp_text_settings[10]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 12</label>

                <input type="hidden" name="stamp_no[]" value="stamp_12">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[11]->stamp_text)?$loyalty_program_stamp_text_settings[11]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 13</label>

                <input type="hidden" name="stamp_no[]" value="stamp_13">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[12]->stamp_text)?$loyalty_program_stamp_text_settings[12]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 14</label>

                <input type="hidden" name="stamp_no[]" value="stamp_14">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[13]->stamp_text)?$loyalty_program_stamp_text_settings[13]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 15</label>

                <input type="hidden" name="stamp_no[]" value="stamp_15">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[14]->stamp_text)?$loyalty_program_stamp_text_settings[14]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 16</label>

                <input type="hidden" name="stamp_no[]" value="stamp_16">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[15]->stamp_text)?$loyalty_program_stamp_text_settings[15]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 17</label>

                <input type="hidden" name="stamp_no[]" value="stamp_17">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[16]->stamp_text)?$loyalty_program_stamp_text_settings[16]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 18</label>

                <input type="hidden" name="stamp_no[]" value="stamp_18">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[17]->stamp_text)?$loyalty_program_stamp_text_settings[17]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 19</label>

                <input type="hidden" name="stamp_no[]" value="stamp_19">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[18]->stamp_text)?$loyalty_program_stamp_text_settings[18]->stamp_text:''}}" required>
            </div>

            <div class="form-group">
                <label for="">Stamp 20</label>

                <input type="hidden" name="stamp_no[]" value="stamp_20">
                <input type="text" class="form-control" name="stamp_value[]" value="{{isset($loyalty_program_stamp_text_settings[19]->stamp_text)?$loyalty_program_stamp_text_settings[19]->stamp_text:''}}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <button id="save_loyalty_settings" type="submit" class="btn btn-success"><i
                        class="fa fa-check"></i> @lang('app.save')</button>
            </div>
        </div>

    </div>

</form>