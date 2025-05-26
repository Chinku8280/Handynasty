<div class="modal-header">
    <h4 class="modal-title">@lang('menu.voucher') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-12">
                <img src="{{$LoyaltyShop->loyalty_shop_image_url}}" class="img img-responsive img-thumbnail" width="100%">
            </div>

            <div class="col-md-12">
                <br>
                <h6 class="text-uppercase">@lang('app.title')</h6>
                <p>{{ $LoyaltyShop->title }}</p>
            </div>

            @if ($LoyaltyShop->discount_type=='percent')
                <div class="col-md-6">
                    <h6 class="text-uppercase">Discount @lang('app.percentage')</h6>
                    <p> {{ $LoyaltyShop->discount }}% </p>
                </div>
            @else
                <div class="col-md-6">
                    <h6 class="text-uppercase">Discount @lang('app.amount')</h6>
                    <p> ${{ $LoyaltyShop->discount }} </p>
                </div>
            @endif

            @if (!empty($LoyaltyShop->start_date_time))
                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.StartTime')</h6>
                    <p>{{ date('d-m-Y h:i A', strtotime($LoyaltyShop->start_date_time)) }}</p>
                </div>
            @endif

            @if (!empty($LoyaltyShop->end_date_time))
                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.endTime')</h6>
                    <p>{{ date('d-m-Y h:i A', strtotime($LoyaltyShop->end_date_time)) }}</p>
                </div>
            @endif

            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.loyalty_point')</h6>
                <p>
                    @if($LoyaltyShop->loyalty_point !='')
                        {{ $LoyaltyShop->loyalty_point }}
                    @else
                        0
                    @endif
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">Age</h6>
                <p>{{ $LoyaltyShop->min_age }} - {{$LoyaltyShop->max_age}}</p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">Gender</h6>
                <p>{{ $LoyaltyShop->gender }}</p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">Validity</h6>
                <p>{{ $LoyaltyShop->validity }} {{$LoyaltyShop->validity_type}}</p>
            </div>

            @if(!is_null($LoyaltyShop->short_description))
                <div class="col-md-12">
                    <h6 class="text-uppercase">Short @lang('app.description')</h6>
                    <p>{{$LoyaltyShop->short_description}}</p>
                </div>
            @endif

            @if(!is_null($LoyaltyShop->description))
                <div class="col-md-12">
                    <h6 class="text-uppercase">@lang('app.description')</h6>
                    <p>{!! $LoyaltyShop->description !!}</p>
                </div>
            @endif

        </div>
    </div>
</div>
