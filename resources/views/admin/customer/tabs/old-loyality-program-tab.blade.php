<div class="tab-pane" id="pc-8" role="tabpanel">
    <div class="d-flex justify-content-between">
        <div></div>
        <button class="btn btn-sm mb-3" id="loyalty_program_add_btn" style="background-color: #541726;color: #fff;">New</button>
    </div>

    <table id="loyalty_program_history_table" class="table  table-bordered example table dt-responsive nowrap w-100">
        <thead class="table-light">
            <tr>
                <th>Sr No.</th>
                <th>Services</th>
                <th>Date</th>
                <th>Time</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($LoyaltyProgramHistory as $key => $item)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $item->services_name }}</td>
                    <td>{{ date('j F Y', strtotime($item->date)) }}</td>
                    <td>{{ date('g:i A', strtotime($item->time)) }}</td>
                    <td>{{ $item->hours }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- loyalty program modal --}}
<div class="modal fade bs-modal-lg in" id="loyalty_program_onload_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-lg-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Loyalty Program</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>        
            </div>
            <div class="modal-body">
                <div class="l_points d-flex align-items-center">
                    Loyalty Coins:- <span>{{ $customer->loyalty_points ?? 0 }}</span>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" id="loyalty_program_modal_form">

                                    <div class="form-group">
                                        <label for="">Select Date</label>

                                        <input type="date" class="form-control" name="date" id="loyalty_program_modal_date" value="{{date('Y-m-d')}}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="">Select Time</label>

                                        <input type="time" class="form-control" name="time" id="loyalty_program_modal_time" value="{{date('H:i')}}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="">Select Outlet</label>

                                        <select name="outlet_id" id="loyalty_program_modal_outlet_id" class="form-control form-control-lg select2" style="width: 100%" required>
                                            @foreach($outlet as $item)
                                                <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="">Select Category</label>

                                        <select name="category_id[]" id="loyalty_program_modal_category_id" class="form-control form-control-lg select2" multiple="multiple" style="width: 100%" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="">Select Services</label>

                                        <select name="service_id[]" id="loyalty_program_modal_service_id" class="form-control form-control-lg select2" multiple="multiple" style="width: 100%" required>

                                        </select>
                                    </div>

                                    <h4 id="loyalty_program_modal_total_hours"></h4>

                                    <div class="form-group" style="text-align: right;">
                                        <button type="button" class="btn btn-success" id="loyalty_program_modal_apply_btn">Apply</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title text-center" style="color: #380814;">Your Progess Tracker</div>
                            </div>
                            <div class="card-body">
                                <div class="coin_box_group" id="coin_box_group">  

                                    {{-- <a href="#" style="background-color: #380814;">                                                                         
                                    </a>

                                    <a href="#" style="background: linear-gradient(90deg, #380814 50%, white 50%);">                                                                         
                                    </a> --}}

                                    @for ($i=0; $i < 20; $i++)                                         
                                        @if($i == 9 || $i == 19)     
                                            @if($i == 9)
                                                @php                                                                                                       
                                                    $title_html = "Free one 1 hour voucher reward";
                                                @endphp                                              
                                            @elseif($i == 19)
                                                @php
                                                    $title_html = "Free two 1 hour voucher reward";
                                                @endphp                                           
                                            @else
                                                @php
                                                    $title_html = "";
                                                @endphp  
                                            @endif  
                                            
                                            <a href="#" data-id="">
                                                <div class="coin_box reward_class" title="{{$title_html}}"><span class='span_reward_class'>+Reward</span></div>    
                                            </a>
                                        @else
                                            @php
                                                $title_html = "";
                                            @endphp

                                            <a href="#" data-id="">
                                                <div class="coin_box" title="{{$title_html}}"><span class='span_reward_class'></span></div>    
                                            </a>
                                        @endif                               
                                    @endfor                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title text-center" style="color: #380814;">Loyalty Program History</div>
                            </div>
                            <div class="card-body">
                                <table id="loyalty_program_modal_history_table" class="table table-bordered example table dt-responsive nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Services</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Total Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>
            </div> --}}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>