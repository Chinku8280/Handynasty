<div class="modal-header">
    <h4 class="modal-title">@lang('menu.voucher') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-12">
                <img src="{{$voucher->voucher_image_url}}" class="img img-responsive img-thumbnail" width="100%">
            </div>

            <div class="col-md-12">
                <br>
                <h6 class="text-uppercase">@lang('app.title')</h6>
                <p>{{ $voucher->title }}</p>
            </div>

            {{-- <div class="col-md-12">
                <h6 class="text-uppercase">@lang('app.voucherItem')</h6>
                <div class="table table-responsive" id="result_div">
                    <table class="table table-bordered table-condensed" width="100%">
                        <tr>
                            <th>@lang('app.service')</th>
                            <th>@lang('app.unitPrice')</th>
                            <th>@lang('app.quantity')</th>
                            <th>@lang('app.subTotal')</th>
                            <th>@lang('app.discount')</th>
                            <th>@lang('app.total')</th>
                        </tr>
                        @foreach ($voucher_items as $voucher_item)
                            <tr>
                                <td>{{$voucher_item->businessService->name}}</td>
                                <td>{{$settings->currency->currency_symbol}}{{$voucher_item->unit_price}}</td>
                                <td>{{$voucher_item->quantity}}</td>
                                <td>{{$settings->currency->currency_symbol}}{{$voucher_item->quantity*$voucher_item->unit_price}}</td>
                                <td>{{$settings->currency->currency_symbol}}{{$voucher_item->discount_amount}}</td>
                                <td>{{$settings->currency->currency_symbol}}{{$voucher_item->total_amount}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3"></td>
                            <td id="voucher-sub-total">{{$settings->currency->currency_symbol}}{{ $voucher->original_amount}}</td>
                            <td id="voucher-discount-total">{{$settings->currency->currency_symbol}}{{$voucher->original_amount-$voucher->voucher_amount}}</td>
                            <td id="voucher-total-price">{{$settings->currency->currency_symbol}}{{ $voucher->voucher_amount}}</td>
                        </tr>
                    </table>
                </div>
            </div> --}}

            {{-- <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.discount') @lang('app.type')</h6>
                <p> {{ $voucher->discount_type }} </p>
            </div> --}}

            <div class="col-md-6">
                <h6 class="text-uppercase">Minimum Spending Amount</h6>
                <p> ${{ $voucher->minimum_purchase_amount }}</p>
            </div>

            @if ($voucher->discount_type=='percent')
                <div class="col-md-6">
                    <h6 class="text-uppercase">Discount @lang('app.percentage')</h6>
                    <p> {{ $voucher->discount }}% up to ${{ $voucher->max_discount }}</p>
                </div>
            @else
                <div class="col-md-6">
                    <h6 class="text-uppercase">Discount @lang('app.amount')</h6>
                    <p> ${{ $voucher->discount }} </p>
                </div>
            @endif


            
            @if (!empty($voucher->start_date_time))
                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.StartTime')</h6>
                    <p>{{ date('d-m-Y h:i A', strtotime($voucher->start_date_time)) }}</p>
                </div>
            @endif               
            
            
            @if (!empty($voucher->end_date_time))
                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.endTime')</h6>
                    <p>{{ date('d-m-Y h:i A', strtotime($voucher->end_date_time)) }}</p>
                </div>
            @endif                   
            

            {{-- <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.appliedBeweenTime')</h6>
                <p>{{ $voucher->open_time }} - {{ $voucher->close_time }} </p>
            </div> --}}

            <div class="col-md-6">
                <h6 class="text-uppercase">Customer @lang('app.usesTime')</h6>
                <p>{{ $voucher->max_order_per_customer }}</p>
            </div>

            {{-- <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.voucherUsedTime')</h6>
                <p>
                    @if($voucher->used_time !='')
                    {{ $voucher->used_time }}
                    @else
                        0
                    @endif
                </p>
            </div> --}}

            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.dayForApply')</h6>
                <p>
                    @if(sizeof($days) == 7)
                        @lang('app.allDays')
                    @else
                        @forelse($days as $day)
                            <span style="margin-left: 20px"> @lang('app.'. strtolower($day)) </span>
                        @empty
                        @endforelse
                    @endif
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.loyalty_point')</h6>
                <p>
                    @if($voucher->loyalty_point !='')
                        {{ $voucher->loyalty_point }}
                    @else
                        0
                    @endif
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">Age</h6>
                <p>{{ $voucher->min_age }} - {{$voucher->max_age}}</p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">Gender</h6>
                <p>{{ $voucher->gender }}</p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">Validity</h6>
                <p>{{ $voucher->validity }} {{$voucher->validity_type}}</p>
            </div>

            @if(!is_null($voucher->description))
                <div class="col-md-12">
                    <h6 class="text-uppercase">@lang('app.description')</h6>
                    <p>{!! $voucher->description !!} </p>
                </div>
            @endif

        </div>
    </div>
</div>
