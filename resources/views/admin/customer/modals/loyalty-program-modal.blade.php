{{-- loyalty program modal --}}
<div class="modal fade bs-modal-lg in" id="loyalty_program_onload_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-lg-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Loyalty Program</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>        
            </div>
            <div class="modal-body">
                <div class="row mb-3">                                  
                    <div class="col-lg-12">
                        <div class="l_points">
                            Loyalty Coins:- <span id="loyalty_program_modal_loyalty_point">{{ $customer->loyalty_points ?? 0 }}</span>
                        </div>
                    </div>

                    {{-- <div class="col-lg-12" id="loyalty_point_group">
                        <div class="card">
                            <div class="card-body">  
                                <form id="loyalty_point_form" method="post">
                                    @csrf

                                    <input type="hidden" name="customer_id" value="{{$customer->id}}">

                                    <div class="form-group mb-3">
                                        <label for="loyalty_points">Loyalty Points:</label>
                                        <input type="number" class="form-control" name="loyalty_points" placeholder="Enter loyalty points" min="0" required>
                                    </div>
                
                                    <div class="form-group" style="text-align: end;">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}
                </div>
                
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" id="loyalty_program_modal_form">

                                    @csrf

                                    <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                    <input type="hidden" class="form-control" name="hidden_loyalty_points" id="hidden_loyalty_points">

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
                                            <option value="">Select Outlet</option>
                                            @foreach($outlet as $item)
                                                {{-- <option value="{{ $item->id }}">{{ $item->outlet_name }}</option> --}}

                                                @if (Session::has('outlet_id'))
                                                    @if (Session::get('outlet_id') == $item->id)
                                                        <option value="{{ $item->id }}" selected>{{ $item->outlet_name }}</option>
                                                    {{-- @else
                                                        <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>                                             --}}
                                                    @endif
                                                @else
                                                    <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>
                                                @endif      
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="">Select Category</label>

                                        <select name="category_id" id="loyalty_program_modal_category_id" class="form-control form-control-lg select2" style="width: 100%" required>
                                            {{-- <option value="">Select</option>
                                            @foreach($categories_loyalty_program as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="">Select Services</label>

                                        <select name="service_id" id="loyalty_program_modal_service_id" class="form-control form-control-lg select2" style="width: 100%" required>

                                        </select>
                                    </div>

                                    <div class="table-reponsive">
                                        <table id="loyalty_program_modal_sevices_table" class="table table-bordered example table dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <td hidden>Service Id</td>
                                                    <td style="width: 35%">Service</td>
                                                    <td style="width: 25%">Qty</td>
                                                    <td style="width: 20%">Hours</td>
                                                    <td style="width: 20%">Action</td>
                                                </tr>
                                            </thead>
                                            <tbody id="loyalty_program_modal_sevices_table_tbody">
                                                
                                            </tbody>
                                        </table>
                                    </div>

                                    <h4 id="loyalty_program_modal_total_hours"></h4>

                                    <div class="form-group" style="text-align: right;">
                                        <button type="submit" class="btn btn-success" id="loyalty_program_modal_apply_btn">Apply</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12" id="loyalty_point_group">
                        <div class="card">
                            <div class="card-body">  
                                <form id="loyalty_point_form" method="post">
                                    @csrf

                                    <input type="hidden" name="customer_id" value="{{$customer->id}}">

                                    <div class="form-group mb-3">
                                        <label for="loyalty_points">Loyalty Coins:</label>
                                        <input type="number" class="form-control" name="loyalty_points" id="loyalty_points" placeholder="Enter Loyalty Coins" min="0" required>
                                    </div>
                
                                    {{-- <div class="form-group" style="text-align: end;">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                    </div> --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title text-center" style="color: #380814;">Your Progress Tracker</div>
                            </div>
                            <div class="card-body">
                                {{-- <div class="coin_box_group" id="coin_box_group">  
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
                                        
                                        <!-- Extra free stamps -->
                                        @if($i == 9)                                               
                                            <a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>
                                        @elseif($i == 19)
                                            <a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>
                                            <a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>
                                        @endif
                                    @endfor         
                                </div> --}}

                                {{-- <div class="coin_box_group" id="coin_box_group">  
                                    <div class="first_coin_box_group">
                                        @for ($i=0; $i < 10; $i++)                                         
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
                                            
                                            <!-- Extra free stamps -->
                                            @if($i == 9)                                               
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                            @elseif($i == 19)
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                            @endif
                                        @endfor  
                                    </div>

                                    <div class="second_coin_box_group" style="margin-top: 20px;">
                                        @for ($i=10; $i < 20; $i++)                                         
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
                                            
                                            <!-- Extra free stamps -->
                                            @if($i == 9)                                               
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                            @elseif($i == 19)
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                            @endif
                                        @endfor  
                                    </div>                                     
                                </div> --}}

                                <div class="coin_box_group" id="coin_box_group">  
                                    {{-- <a href="#">
                                        <div class="coin_box" style="background-color: #380814;">
                                            <span class='span_reward_class'></span>
                                        </div>
                                    </a>
                                    <a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                        <div class="coin_box" style="background: linear-gradient(90deg, #380814 50%, white 50%);">
                                            <span class='span_reward_class'></span>
                                        </div>
                                    </a> --}}
                                    <div class="first_coin_box_group">
                                        @for ($i=0; $i < 10; $i++)                                         
                                                                                          
                                            <a href="#" data-id="">
                                                <div class="coin_box"><span class='span_reward_class'></span></div>    
                                            </a>
                                            
                                            <!-- Extra free stamps -->
                                            @if($i == 9)                                               
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                            @elseif($i == 19)
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                            @endif
                                        @endfor  
                                    </div>

                                    <div class="second_coin_box_group" style="margin-top: 20px;">
                                        @for ($i=10; $i < 20; $i++)                                         
                                            
                                            <a href="#" data-id="">
                                                <div class="coin_box"><span class='span_reward_class'></span></div>    
                                            </a>                                           
                                            
                                            <!-- Extra free stamps -->
                                            @if($i == 9)                                               
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                            @elseif($i == 19)
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                                <a href="#" data-id="">
                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                        <span class="span_extra_reward_class">
                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                        </span>
                                                    </div>
                                                </a>
                                            @endif
                                        @endfor  
                                    </div>                                     
                                </div>  
                            </div>

                            <div class="card-footer mt-3" id="loyalty_program_hours_group" style="display: none; border-top: 1px solid rgba(0, 0, 0, 0.125);;">
                                <h4>Total Hours Accumulated <span id="loyalty_program_modal_db_total_hours"></span></h4>
                                <br>
                                <h4><span id="loyalty_program_modal_db_balance_hours"></span> Hrs left for your next free massage</h4>
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
                                <div class="table-responsive">
                                    <table id="loyalty_program_modal_history_table" class="table table-bordered example table dt-responsive nowrap w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>Category</th>
                                                <th>Services</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Total Hours</th>
                                                <th>Given By</th>
                                                <th>Action</th>
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
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>
            </div> --}}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>