@extends('layouts.front')

@push('styles')
<style>
    .coupons-base-content .fa-tag{
        font-size: 20px;
        color: #222;
    }
    .coupons-base-content p{
        color: #3289da;
        font-size: 11px;
    }
    .remove-button{
        margin-bottom: 4px;
        margin-left: 3px;
    }
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button
    {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number]
    {
    -moz-appearance: textfield;
    }
</style>
@endpush
@section('content')
    <section class="section">
        <section class="cart-area sp-80">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="all-title">
                            <h3 class="sec-title">
                                @lang('front.headings.bookingDetails')
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-12 mb-30">
                        <div class="shopping-cart-table">
                            <table class="table table-responsive-md">
                                <thead>
                                <tr>
                                    <th>@lang('front.table.headings.serviceName')</th>
                                    <th>@lang('front.table.headings.unitPrice')</th>
                                    <th>@lang('front.table.headings.quantity')</th>
                                    <th>@lang('front.table.headings.subTotal')</th>
                                    @if (!is_null($products))
                                        <th>&nbsp;</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                    {{--{{ dd($products) }}--}}
                                    @if (!is_null($products))
                                        @foreach($products as $key => $product)
                                            <tr id="{{ $key }}">
                                                <td>{{ $product['serviceName'] }}</td>
                                                <td>{{ $settings->currency->currency_symbol.$product['servicePrice'] }}</td>
                                                <td>
                                                    <div class="qty-wrap">
                                                        <div class="qty-elements">
                                                            <a class="decrement_qty" href="javascript:void(0)" onclick="decreaseQuantity(this)">-</a>
                                                        </div>
                                                        <input onkeypress="return isNumberKey(event)" type="number" name="qty" value="{{ $product['serviceQuantity'] }}" title="Quantity"
                                                            class="input-text qty" data-id="{{ $key }}" data-price="{{$product['servicePrice']}}" autocomplete="none" />
                                                        <div class="qty-elements">
                                                            <a class="increment_qty" href="javascript:void(0)" onclick="increaseQuantity(this)">+</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="sub-total">{{ $settings->currency->currency_symbol }}<span>{{ $product['serviceQuantity'] * $product['servicePrice'] }}</span></td>
                                                <td>
                                                    <a title="@lang('front.table.deleteProduct')" href="javascript:;" onclick="deleteProduct(this, '{{ $key }}')" class="delete-btn">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center text-danger">@lang('front.table.emptyMessage')</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <ul class="cart-buttons">
                                            <li>
                                            </li>
                                            <li>
                                                <a href="{{ route('front.index') }}" class="btn btn-custom btn-blue">@lang('front.buttons.continueBooking')</a>
                                                @if (!is_null($products))
                                                    <a href="javascript:;" onclick="deleteProduct(this, 'all')" class="btn btn-custom btn-blue">@lang('front.buttons.clearCart')</a>
                                                @endif
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 mb-30">
                        <div class="cart-block">
                            <div class="final-cart">
                                <h5>@lang('front.summary.cart.heading.cartTotal')</h5>
                                <div class="mx-3">
                                <div class="input-group" id="applyCouponBox"
                                     @if(is_null($couponData))
                                        style="border-radius: 4px;overflow: hidden;" @else style="display: none" @endif >
                                    <input type="text" name="coupon"class="form-control" placeholder="@lang('front.summary.cart.applyCoupon')" id="coupon" style="border: 0;">
                                    <div class="input-group-prepend">
                                         <button id="" onclick="applyCoupon();" type="button" class="btn btn-sm input-group-text" style="font-size:13px; border:0; color:#000">@lang('front.summary.cart.applyCoupon')</button>
                                     </div>
                                </div>
                                </div>
                            {{--</div>--}}
                                <div class=" py-3 border-bottom" id="removeCouponBox"  @if(is_null($couponData)) style="display: none" @endif>
                                    <h6  class="clearfix text-white">@lang('app.coupons')</h6>

                                    <div class="coupons-base-content justify-content-between d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3 ">
                                                <i class="fa fa-tag text-white"></i>
                                            </div>
                                            <div>
                                                <span class="coupons-name mb-0 text-uppercase" id="couponCode" >
                                                    @if(!is_null($couponData))
                                                        {{ $couponData[0]['title'] }}
                                                    @endif
                                                </span>
                                                <p class="mb-0 text-success savetext">
                                                    @lang('app.youSaved') {{ $settings->currency->currency_symbol }}
                                                    <span id="couponCodeAmonut">
                                                        @if(!is_null($couponData))
                                                            {{ $couponData['applyAmount'] }}
                                                        @endif
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" onclick="removeCoupon();" class="btn btn-sm btn-danger remove-button"> @lang('app.remove') </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="cart-value">
                                    <ul>
                                        <li>
                                            <span>
                                                @lang('front.summary.cart.subTotal')
                                            </span>
                                            <span id="sub-total">
                                            </span>
                                        </li>
                                        @if(!is_null($tax))
                                            <li>
                                                <span>
                                                    {{ $tax->tax_name }} ({{ $tax->percent }}%):
                                                </span>
                                                <span id="tax">
                                                </span>
                                            </li>
                                        @endif
                                        @if(!is_null($couponData))
                                            <li id="couponDiscountBox">
                                                <span>
                                                    @lang('app.discount') ({{ $couponData[0]['title'] }}):
                                                </span>
                                                <span id="couponDiscoiunt">
                                                    -{{ $settings->currency->currency_symbol }}{{ $couponData['applyAmount'] }}
                                                </span>
                                            </li>
                                        @endif
                                        <li id="totalAmountBox">
                                            <span>
                                                @lang('front.summary.cart.totalAmount'):
                                            </span>
                                            <span id="total">
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (!is_null($products))
                    <div class="row">
                        <div class="col-12 text-right">
                            <div class="navigation">
                                <a href="{{ route('front.bookingPage') }}" class="btn btn-custom btn-dark"><i class="fa fa-angle-left mr-2"></i>@lang('front.navigation.goBack')</a>
                                <a href="{{ route('front.checkoutPage') }}" class="btn btn-custom btn-dark">
                                    {{ !is_null($bookingDetails) ? __('front.navigation.toCheckout') : __('front.selectBookingTime') }}
                                    <i class="fa fa-angle-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </section>
@endsection

@push('footer-script')
<script src="{{ asset('assets/js/cookie.js') }}"></script>
    <script>

        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
            return true;
        }

        $(function () {
            var couponCode = '';
            calculateTotal();
        });

        var cartUpdate;
        var couponAmount = 0;
        var couponApplied = false;
        var products = {!! json_encode($products) !!};

        @if(!is_null($couponData) && $couponData['applyAmount'])
            couponAmount = '{{ $couponData['applyAmount'] }}';
            couponCode = '{{ $couponData[0]['title'] }}';
            couponApplied = true;
        @endif

        function calculateTotal()
        {
            let cartTotal = tax = totalAmount = 0.00;

            $('.sub-total>span').each(function () {
                cartTotal += parseFloat($(this).text());
            });

            $('#sub-total').text('{{ $settings->currency->currency_symbol }}'+cartTotal.toFixed(2));

            // calculate and display tax
            @if(!is_null($tax))
                let taxPercent = parseFloat('{{ $tax->percent }}');
                tax = (taxPercent * cartTotal)/100;

                $('#tax').text('{{ $settings->currency->currency_symbol }}'+tax.toFixed(2));
            @endif

            totalAmount = cartTotal + tax;

            if(couponAmount)
            {
                if(totalAmount>=couponAmount)
                {
                    totalAmount = totalAmount - couponAmount;
                }
                else
                {
                    totalAmount = 0;
                }
            }

            $('#total').text('{{ $settings->currency->currency_symbol }}'+totalAmount.toFixed(2));
        }

        function increaseQuantity(ele)
        {
            var input = $(ele).parent().siblings('input');
            var currentValue = input.val();
            if(currentValue>0)
            {
                input.val(parseInt(currentValue) + 1);
                input.trigger('keyup');
            }
        }

        function decreaseQuantity(ele) {
            var input = $(ele).parent().siblings('input');
            var currentValue = input.val();

            if (currentValue > 1) {
                input.val(parseInt(currentValue) - 1);
                input.trigger('keyup');
            }
        }

        function deleteProduct(ele, key) {
            var url = '{{ route('front.deleteProduct', ':id') }}';
            url = url.replace(':id', key);

            $.easyAjax({
                url: url,
                type: 'POST',
                data: {_token: $("meta[name='csrf-token']").attr('content')},
                redirect: false,
                success: function (response) {
                    if (response.status == 'success') {
                        if (response.action == "redirect") {
                            var message = "";
                            if (typeof response.message != "undefined") {
                                message += response.message;
                            }

                            $.showToastr(message, "success", {
                                positionClass: "toast-top-right"
                            });

                            setTimeout(function () {
                                window.location.href = response.url;
                            }, 1000);

                        }
                        else {
                            updateCoupon ();
                            $(ele).parents(`tr#${key}`).remove();
                            calculateTotal();
                            $('.cart-badge').text(response.productsCount);
                            products = response.products;
                        }
                    }
                }
            })
        }


        function updateCart() {
            let data = {};
            $('input.qty').each(function ()
            {
                const serviceId = $(this).data('id');
                products[serviceId].serviceQuantity = parseInt($(this).val());
            });
            data.products = products;
            data._token = '{{ csrf_token() }}';
            if($('input.qty').val()>=0 && $('input.qty').val()!='')
            {
                $.easyAjax({
                    url: '{{ route('front.updateCart') }}',
                    type: 'POST',
                    data: data,
                    container: '.section',
                    success:function(response){
                        updateCoupon ();
                    }
                })
            }

        }
        function removeCoupon () {
            $.easyAjax({
                url: '{{ route('front.remove-coupon') }}',
                type: 'GET',
                success: function (response) {
                    couponApplied = false;
                    $('#coupon').val('');
                    $('#coupon_amount').val(0);
                    couponAmount = 0;
                    calculateTotal();
                    $('#couponDiscountBox').remove();
                    $('#removeCouponBox').hide();
                    $('#applyCouponBox').show();

                }
            })


        }

        function applyCoupon ()
        {
            let cartTotal = tax = totalAmount = 0.00;

            $('.sub-total>span').each(function () {
                cartTotal += parseFloat($(this).text());
            });

            $('#sub-total').text('{{ $settings->currency->currency_symbol }}'+cartTotal.toFixed(2));

            // calculate and display tax
            @if(!is_null($tax))
                let taxPercent = parseFloat('{{ $tax->percent }}');
                tax = (taxPercent * cartTotal)/100;
                $('#tax').text('{{ $settings->currency->currency_symbol }}'+tax.toFixed(2));
            @endif

            totalAmount = cartTotal + tax;

           var couponVal = $('#coupon').val();
           if((couponVal === undefined || couponVal === "" || couponVal === null)){
               return $.showToastr("@lang('errors.coupon.required')", 'error');
           }else{
               var currencySymbol = '{{ $settings->currency->currency_symbol }}';
               $.easyAjax({
                   url: '{{ route('front.apply-coupon') }}',
                   type: 'GET',
                   data: {'coupon':couponVal},
                   success: function (response) {
                       if(response.status != 'fail'){
                           couponApplied = true;
                           couponCode = couponVal;
                           couponAmount = response.amount;
                           if(couponAmount>totalAmount)
                           {
                                couponAmount = totalAmount;
                           }
                            calculateTotal();
                           $('#couponDiscountBox').remove();
                           var discountElement = '<li id="couponDiscountBox">'+
                               '<span>'+
                               "@lang('app.discount') ("+response.couponData.title+'):'+
                               '</span>'+
                               '<span id="discountCoupon">-'+currencySymbol +couponAmount+
                               '</span>'+
                               '</li>';
                           $(discountElement).insertBefore( "#totalAmountBox" );

                           $('#applyCouponBox').hide();
                           $('#removeCouponBox').show();

                           $('#couponCodeAmonut').html(couponAmount);
                           $('#couponCode').html(response.couponData.title);
                       }
                       else{
                           removeCoupon ();
                       }

                   }
               })
           }

        }

        function updateCoupon () {

            let cartTotal = tax = totalAmount = 0.00;

            $('.sub-total>span').each(function () {
                cartTotal += parseFloat($(this).text());
            });

            $('#sub-total').text('{{ $settings->currency->currency_symbol }}'+cartTotal.toFixed(2));

            // calculate and display tax
            @if(!is_null($tax))
                let taxPercent = parseFloat('{{ $tax->percent }}');
                tax = (taxPercent * cartTotal)/100;
                $('#tax').text('{{ $settings->currency->currency_symbol }}'+tax.toFixed(2));
            @endif

            totalAmount = cartTotal + tax;

            if (couponApplied){
                var currencySymbol = '{{ $settings->currency->currency_symbol }}';
                $.easyAjax({
                    url: '{{ route('front.update-coupon') }}',
                    type: 'GET',
                    data: {'coupon': couponCode},
                    success: function (response) {
                        if (response.status != 'fail') {
                            couponAmount = response.amount;
                            if(couponAmount>totalAmount)
                            {
                                couponAmount = totalAmount;
                            }
                            calculateTotal();
                            $('#couponDiscountBox').remove();
                            var discountElement = '<li id="couponDiscountBox">' +
                                '<span>' +
                                "@lang('app.discount') (" + response.couponData.title + '):' +
                                '</span>' +
                                '<span id="discountCoupon">-' + currencySymbol + couponAmount +
                                '</span>' +
                                '</li>';
                            $(discountElement).insertBefore("#totalAmountBox");

                            $('#applyCouponBox').hide();
                            $('#removeCouponBox').show();

                            $('#couponCodeAmonut').html(couponAmount);
                            $('#couponCode').html(response.couponData.title);
                        }
                        else {
                            removeCoupon();
                        }

                    }
                })

            }
        }

        $('input.qty').on('keyup', function () {
            const id = $(this).data('id');
            const price = $(this).data('price');
            const quantity = $(this).val();
            let subTotal = 0;

            if (quantity<0)
            {
                $(this).val(0);
            }

            clearTimeout(cartUpdate);

            if (quantity == '' || quantity == 0) {
                subTotal = price * 1;
            }
            else {
                subTotal = price * quantity;
            }

            $(`tr#${id}`).find('.sub-total>span').text(subTotal.toFixed(2));
            calculateTotal();

            cartUpdate = setTimeout(() => {
                updateCart();
            }, 500);
        });

        $('input.qty').on('blur', function () {
            if ($(this).val() == '' || $(this).val() == 0) {
                $(this).val(1);
            }
        })
    </script>
@endpush
