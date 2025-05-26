<div class="modal-header">
    <h4 class="modal-title">@lang('menu.deal') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
                <div class="col-12">
                    <img src="{{$deal->deal_image_url}}" class="img img-responsive img-thumbnail" width="100%">
                </div>

                <div class="col-md-12">
                    <br>
                    <h6 class="text-uppercase">@lang('app.title')</h6>
                    <p>{{ $deal->title }}</p>
                </div>

                <div class="col-md-12">
                    <h6 class="text-uppercase">@lang('app.dealItem')</h6>
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
                            @foreach ($deal_items as $deal_item)
                                <tr>
                                    <td>{{$deal_item->businessService->name}}</td>
                                    <td>{{$settings->currency->currency_symbol}}{{$deal_item->unit_price}}</td>
                                    <td>{{$deal_item->quantity}}</td>
                                    <td>{{$settings->currency->currency_symbol}}{{$deal_item->quantity*$deal_item->unit_price}}</td>
                                    <td>{{$settings->currency->currency_symbol}}{{$deal_item->discount_amount}}</td>
                                    <td>{{$settings->currency->currency_symbol}}{{$deal_item->total_amount}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3"></td>
                                <td id="deal-sub-total">{{$settings->currency->currency_symbol}}{{ $deal->original_amount}}</td>
                                <td id="deal-discount-total">{{$settings->currency->currency_symbol}}{{$deal->original_amount-$deal->deal_amount}}</td>
                                <td id="deal-total-price">{{$settings->currency->currency_symbol}}{{ $deal->deal_amount}}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.discount') @lang('app.type')</h6>
                    <p> {{ $deal->discount_type }} </p>
                </div>

                @if ($deal->discount_type=='percentage')
                    <div class="col-md-6">
                        <h6 class="text-uppercase">@lang('app.percentage')</h6>
                        <p> {{ $deal->percentage }}% </p>
                    </div>
                @else
                    <div class="col-md-6">
                        <h6 class="text-uppercase">@lang('app.amount')</h6>
                        <p> {{ $deal->original_amount-$deal->deal_amount }} </p>
                    </div>
                @endif


                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.StartTime')</h6>
                    <p>{{ $deal->start_date_time }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.endTime')</h6>
                    <p>{{ $deal->end_date_time }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.appliedBeweenTime')</h6>
                    <p>{{ $deal->open_time }} - {{ $deal->close_time }} </p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.usesTime')</h6>
                    <p>
                        @if($deal->uses_limit > 0)
                        {{ $deal->uses_limit }}
                        @else
                            @lang('app.infinite')
                        @endif
                    </p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.dealUsedTime')</h6>
                    <p>
                        @if($deal->used_time !='')
                        {{ $deal->used_time }}
                        @else
                            0
                        @endif
                    </p>
                </div>

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

                @if(!is_null($deal->description))
                    <div class="col-md-12">
                        <h6 class="text-uppercase">@lang('app.description')</h6>
                        <p>{!! $deal->description !!} </p>
                    </div>
                @endif

            </div>
    </div>
</div>
