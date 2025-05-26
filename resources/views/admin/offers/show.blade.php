<div class="modal-header">
    <h4 class="modal-title">@lang('menu.offer') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-12">
                <img src="{{ $offer->offer_image_url }}" class="img img-responsive img-thumbnail" width="100%">
            </div>

            <div class="col-md-6">
                <br>
                <h6 class="text-uppercase">@lang('app.title')</h6>
                <p>{{ $offer->title }}</p>
            </div>
            <div class="col-md-6">
                <br>
                <h6 class="text-uppercase">@lang('app.branch')</h6>
                <p>{{ \App\Location::find($offer->branch_id)->name }}</p>
            </div>

            {{-- <div class="col-md-12">
                    <h6 class="text-uppercase">@lang('app.offerItem')</h6>
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
                            @foreach ($offer_items as $offer_item)
                                <tr>
                                    <td>{{$offer_item->businessService->name}}</td>
                                    <td>{{$settings->currency->currency_symbol}}{{$offer_item->unit_price}}</td>
                                    <td>{{$offer_item->quantity}}</td>
                                    <td>{{$settings->currency->currency_symbol}}{{$offer_item->quantity*$offer_item->unit_price}}</td>
                                    <td>{{$settings->currency->currency_symbol}}{{$offer_item->discount_amount}}</td>
                                    <td>{{$settings->currency->currency_symbol}}{{$offer_item->total_amount}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3"></td>
                                <td id="offer-sub-total">{{$settings->currency->currency_symbol}}{{ $offer->original_amount}}</td>
                                <td id="offer-discount-total">{{$settings->currency->currency_symbol}}{{$offer->original_amount-$offer->offer_amount}}</td>
                                <td id="offer-total-price">{{$settings->currency->currency_symbol}}{{ $offer->offer_amount}}</td>
                            </tr>
                        </table>
                    </div>
                </div> --}}

            {{-- <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.discount') @lang('app.type')</h6>
                    <p> {{ $offer->discount_type }} </p>
                </div> --}}


            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.StartTime')</h6>
                <p>{{ $offer->start_date_time }}</p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.endTime')</h6>
                <p>{{ $offer->end_date_time }}</p>
            </div>           

            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.discount')</h6>
                <p> {{ $offer->discount }}%   </p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.max_person')</h6>
                <p> {{ $offer->max_person }} </p>
            </div>     
            
            <div class="col-md-6">
                <h6 class="text-uppercase">Min Age</h6>
                <p> {{ $offer->min_age }} </p>
            </div> 

            <div class="col-md-6">
                <h6 class="text-uppercase">Max Age</h6>
                <p> {{ $offer->max_age }} </p>
            </div> 

            <div class="col-md-6">
                <h6 class="text-uppercase">Gender</h6>
                <p> {{ $offer->gender }} </p>
            </div>

            @if (!is_null($offer->description))
                <div class="col-md-12">
                    <h6 class="text-uppercase">@lang('app.description')</h6>
                    <p>{!! $offer->description !!} </p>
                </div>
            @endif

        </div>
    </div>
</div>
