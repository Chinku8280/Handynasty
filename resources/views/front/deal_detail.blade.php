@extends('layouts.front')

@push('styles')
    <style>
        .img{
            height: 25em;
        }
    </style>
@endpush


@section('content')
    <section class="section">
        <section class="service-detail sp-80">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{ $deal->title }}</h4>
                    </div>
                    <div class="col-md-8 col-sm-12 col-xs-12">
                        <img class="img" src="{{ $deal->deal_image_url }}" alt="{{ $deal->slug }}" width="100%" height="300px">

                        <div class="row col-md-12" style="margin-top: 2em">
                            <h6 class="text-uppercase">@lang('app.description')</h6>
                            <div class="row col-12 text-justify">
                            {!! $deal->description !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 mb-60">
                        <div class="detail-info mb-5">
                            <ul>
                                <li>
                                    <span>
                                        @lang('app.location')
                                    </span>
                                    <span>
                                        {{ $deal->location->name }}
                                    </span>
                                </li>
                                <li>
                                    <span>
                                        @lang('app.deal') @lang('app.type')
                                    </span>
                                    <span>
                                        {{ $deal->deal_type=='' ? __('app.offer') : __('app.combo') }}
                                    </span>
                                </li>
                                <li>
                                    <span>
                                        @lang('app.price')
                                    </span>
                                    <span>
                                        {{$settings->currency->currency_symbol}}{{ $deal->deal_amount }}
                                    </span>
                                </li>
                                <li>
                                    <span>
                                        @lang('app.StartTime')
                                    </span>
                                    <span>
                                        {{ $deal->start_date_time }}
                                    </span>
                                </li>
                                <li>
                                    <span>
                                        @lang('app.endTime')
                                    </span>
                                    <span>
                                        {{ $deal->end_date_time }}
                                    </span>
                                </li>
                                <li>
                                    <span>
                                        @lang('app.appliedBeweenTime')
                                    </span>
                                    <span>
                                        {{ $deal->open_time }} - {{ $deal->close_time }}
                                    </span>
                                </li>
                                <li>
                                    <span>
                                        @lang('messages.howManyTimeCustomerCanUse')
                                    </span>
                                    <span>
                                        {{ $deal->max_order_per_customer }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                         <ul class="add-qty">
                            <li>
                                <span class="text-capitalize mb-2 d-block">@lang('app.add') @lang('app.quantity')</span>
                                <div class="qty-wrap">
                                    <div class="qty-elements">
                                        <a class="decrement_qty" href="javascript:void(0)" onclick="decreaseQuantity(this)">-</a>
                                    </div>
                                    @php
                                        // $product = current($reqProduct);
                                    @endphp
                                    <input name="qty" size="4" title="Quantity" class="input-text qty" autocomplete="none" value="1" readonly />
                                    <div class="qty-elements">
                                        <a class="increment_qty" href="javascript:void(0)" onclick="increaseQuantity(this)">+</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="add">
                                    <div class="row">
                                        <div class="col">
                                            <a href="javascript:;" class="btn btn-custom grab-deal" data-deal-price="{{ $deal->deal_amount }}"
                                            data-deal-id="{{ $deal->id }}"
                                            data-deal-name="{{ $deal->title }}"
                                            aria-expanded="false">@lang('app.grab')</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-right">
                        <div class="navigation mt-4">
                            <a href="{{ route('front.index') }}" class="btn btn-custom btn-dark">
                                <i class="fa fa-angle-left mr-2"></i>@lang('front.navigation.goBack')
                            </a>


                            <a href="javascript:;" class="btn btn-custom btn-dark grab-deal"        data-deal-price="{{ $deal->deal_amount }}"
                                data-deal-id="{{ $deal->id }}"
                                data-deal-name="{{ $deal->title }}"
                                aria-expanded="false">
                                @lang('app.grab') @lang('app.deal')
                                <i class="fa fa-angle-right ml-1"></i>
                            </a>



                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

@endsection

@push('footer-script')
    <script>
        function increaseQuantity(ele) {
            var input = $(ele).parent().siblings('input');
            var currentValue = input.val();

            if(currentValue<parseInt({{$deal->max_order_per_customer}})){
                input.val(parseInt(currentValue) + 1);
            }
            else if('{{$deal->max_order_per_customer}}'=='Infinite'){
                input.val(parseInt(currentValue) + 1);
            }
            else{
                return false;
            }
        }

        function decreaseQuantity(ele) {
            var input = $(ele).parent().siblings('input');
            var currentValue = input.val();
            if (currentValue > 1) {
                input.val(parseInt(currentValue) - 1);
            }
        }

        $('input.qty').on('blur', function () {
            if ($(this).val() == '' || $(this).val() == 0) {
                $(this).val(1);
            }
        });


        $('body').on('click', '.grab-deal', function () {
            let dealId = $(this).data('deal-id');
            let dealPrice = $(this).data('deal-price');
            let dealName = $(this).data('deal-name');
            let dealQuantity = $('.qty').val();

            var data = {dealId, dealPrice, dealName, dealQuantity, '_token': $("meta[name='csrf-token']").attr('content')};

            $.easyAjax({
                url: '{{ route('front.grabDeal') }}',
                type: 'POST',
                data: data,
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = '{{ route('front.checkoutPage') }}'
                    }
                }
            })
        });

    </script>
@endpush
