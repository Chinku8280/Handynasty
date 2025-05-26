@extends('layouts.master')

@push('head-css')
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
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #999;
    }
    .select2-dropdown .select2-search__field:focus, .select2-search--inline .select2-search__field:focus {
        border: 0px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        margin: 0 13px;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #cfd1da;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        cursor: pointer;
        float: right;
        font-weight: bold;
        margin-top: 8px;
        margin-right: 15px;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-7">
            <form id="filter-form" class="ajax-form" method="GET">
                <div class="card card-light">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">@lang('app.category') @lang('app.filter') </label>
                                    <div class="col-sm-8">
                                        <select id="category-filter" name="category_id" class="form-control">
                                            <option value="0">--</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">@lang('app.location') @lang('app.filter')</label>
                                    <div class="col-sm-8">
                                        <select id="location-filter" name="location_id" class="form-control">
                                            <option value="0">--</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" id="pos-services">

                        @foreach($categories as $category)
                        <div class="row">
                            @if($category->services->count() > 0)
                            <div class="col-md-12 mt-2">
                                <h5>{{ ucfirst($category->name) }}</h5>
                            </div>
                            @endif
                            @foreach($category->services as $service)
                            <div class="col-md-6 col-lg-3">
                                <div class="card">
                                    <img height="100em" class="card-img-top" src="{{ $service->service_image_url }}">
                                    <div class="card-body p-2">
                                        <p class="font-weight-normal">{{ ucwords($service->name) }}</p>
                                         {!! ($service->discount > 0) ? "<s class='h6 text-danger'>".$settings->currency->currency_symbol.$service->price."</s> ".$settings->currency->currency_symbol.$service->discounted_price : $settings->currency->currency_symbol.$service->price !!}
                                    </div>
                                    <div class="card-footer p-1">
                                        <a href="javascript:;"
                                           data-service-price="{{ $service->discounted_price }}"
                                           data-service-id="{{ $service->id }}"
                                           data-service-name="{{ ucwords($service->name) }}"
                                           class="btn btn-block btn-dark add-to-cart"><i class="fa fa-plus"></i> @lang('app.add')</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </form>
        </div>
        <div class="col-md-5">
            <form id="pos-form" class="ajax-form" method="POST" autocomplete="off">
                @csrf
                <div class="card card-dark">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-10">
                                <label for="">@lang('app.date')</label>
                                <div class="input-group form-group">

                                    <input type="text" class="form-control" name="date" id="datepicker" value="">
                                    <span class="input-group-append input-group-addon">
                                        <button type="button" class="btn btn-info"><span class="fa fa-calendar-o"></span></button>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-10">
                                <label for="">@lang('app.time')</label>
                                <div class="input-group form-group">

                                    <input type="text" class="form-control" name="time" id="timepicker" value="">
                                    <span class="input-group-append input-group-addon">
                                        <button type="button" class="btn btn-info"><span class="fa fa-clock-o"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="">@lang('modules.booking.searchNote')</label>
                                    <select id="user_id" name="user_id" class="form-control select2"></select>
                                    <div id="user-error" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mt-2">&nbsp;</div>
                                <button class="btn btn-success btn-rounded" id="select-customer" type="button"><i
                                            class="fa fa-plus"></i> @lang('app.add')</button>
                            </div>

                            <div class="col-md-10" id="employee_list">
                                <div class="form-group">
                                    <label for="">@lang('modules.booking.assignEmployee')</label>
                                    <select id="employee" name="employee[]" class="form-control select2" multiple="multiple" style="width: 100%">
                                        @foreach($employees as $employee)
                                            <option
                                            value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                    <div id="employee-error" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-2 mb-2 p-2" id="pos-customer-details"></div>

                        </div>

                        <div class="row">
                            <table class="table table-condensed" id="cart-table">
                                <thead>
                                    <tr>
                                        <th width="30%">@lang('app.service')</th>
                                        <th width="20%">@lang('app.price')</th>
                                        <th style="width: 120px">@lang('app.quantity')</th>
                                        <th class="text-right">@lang('app.subTotal')</th>
                                        <th><i class="fa fa-gear"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="no-service">
                                        <td colspan="5" class="text-center text-danger">@lang("messages.selectService")</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <!-- /.card-body -->

                </div>
                <!-- /.card -->

                <div class="card">
                    <div class="card-body">
                        <div class="row pos-calculations">
                            <div class="col-md-6 border-bottom">
                                @lang('app.subTotal')
                            </div>
                            <div class="col-md-6 border-bottom" id="cart-sub-total">
                                {{ $settings->currency->currency_symbol }}0
                            </div>
                            <div class="col-md-6 border-bottom">
                                <h6>@lang('app.discount') (%)</h6>
                            </div>
                            <div class="col-md-6 border-bottom">
                                <input onchange="checkValue(this.value)" onkeypress="return isNumberKey(event)" type="number" id="cart-discount" name="cart_discount" class="form-control" step=".01" min="0" max="100" value="0">
                            </div>

                            @if(!is_null($tax))
                                <input type="hidden" id="cart-tax" name="cart_tax" value="{{ $tax->percent }}">
                                <div class="col-md-6 border-bottom">
                                    <h6>{{ $tax->tax_name.' ('.$tax->percent.'%)' }}</h6>
                                </div>
                                <div class="col-md-6 border-bottom">
                                    <h5 id="cart-tax-amount">{{ $settings->currency->currency_symbol }}0</h5>
                                </div>
                            @else
                                <input type="hidden" id="cart-tax" name="cart_tax" value="0">
                            @endif
                            <div class="col-md-12 border-bottom" id="applyCouponBox">
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <h6>@lang('app.applyCoupon')</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="coupon" name="coupon" class="form-control" style="width: 80%; display: inline">
                                        <button type="button" id="applyCoupon" style="margin-bottom: 4px;margin-left: 3px;" class="btn btn-success "><i class="fa fa-check"></i> </button>
                                    </div>
                                </div>

                            </div>


                            <div class="col-md-12 py-3 border-bottom" id="removeCouponBox" style="display:none">
                                <h5>@lang('app.coupons')</h5>

                                <div class="coupons-base-content justify-content-between d-flex align-items-center">
                                   <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fa fa-tag"></i>
                                        </div>
                                        <div>
                                            <h5 class="coupons-name mb-0" id="couponCode"> </h5>
                                            <p class="mb-0 text-success">
                                                @lang('app.youSaved') {{ $settings->currency->currency_symbol }}<span id="couponCodeAmonut"> </span>
                                            </p>
                                        </div>
                                   </div>
                                    <div>
                                        <button type="button" onclick="removeCoupon();" class="btn btn-success btn-outline-danger remove-button"> @lang('app.remove') </button>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6" id="totalAmountBox">
                                <h4>@lang('app.total')</h4>
                            </div>
                            <div class="col-md-6">
                                <h4 id="cart-total">{{ $settings->currency->currency_symbol }}0</h4>
                                <input type="hidden" id="cart-total-input">
                                <input type="hidden" id="coupon_id" name="coupon_id" >
                                <input type="hidden" id="coupon_amount" name="coupon_amount" >
                            </div>

                            <div class="col-md-6 mt-2">
                                <button type="button" id="empty-cart" class="btn btn-danger p-3 btn-lg btn-block">@lang('modules.booking.emptyCart')</button>
                                <div id="cart-item-error" class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <button type="button" id="do-payment" class="btn btn-success p-3 btn-lg btn-block">@lang('app.pay')</button>
                                <div id="cart-item-error" class="invalid-feedback"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    {{--pay Modal--}}
    <div class="modal fade bs-modal-md in" id="payment-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">@lang('app.pay')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 h5">@lang('app.total'):</div>
                                            <div class="col-md-8 h5" id="payment-modal-total">0</div>
                                        </div>
                                    </div>
                                    <div class="form-group">

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" checked type="radio" name="payment_gateway" id="pay-cash" value="cash">
                                            <label class="form-check-label" for="pay-cash">@lang('modules.booking.payViaCash')</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_gateway" id="pay-card" value="card">
                                            <label class="form-check-label" for="pay-card">@lang('modules.booking.payViaCard')</label>
                                        </div>

                                    </div>


                                    <div id="cash-mode">
                                        <div class="form-group">
                                            <label for="">@lang('modules.booking.cashGivenByCustomer')</label>
                                            <input oninput="limitDecimalPlaces(event)" onkeypress="return isNumberKey(event)" type="number" min="0" step=".01" class="form-control form-control-lg" id="cash-given">
                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="">@lang('modules.booking.cashRemaining')</label>
                                                <div class="col-md-12 h5" id="cash-remaining">-</div>
                                            </div>


                                            <div class="form-group col-md-6">
                                                <label for="">@lang('modules.booking.cashToReturn')</label>
                                                <div class="col-md-12 h5" id="cash-return">-</div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>
                    <button type="button" id="submit-cart" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.submit')</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--pay Modal Ends--}}

@endsection

@push('footer-js')
    <script src="{{ asset('assets/plugins/select2/dist/js/i18n/'.$settings->locale.'.js') }}"></script>
    <script>
        var currentTime = moment().format("{{ $time_picker_format }}'");
        var globalCartTotal = 0;
        var couponAmount = 0;
        var couponCodeValue = '';
        var couponApplied = false;

        var moment_Date = moment();
        var pos_date = moment_Date.format('YYYY-MM-DD');
        var pos_time = moment_Date.format("hh:mm a");

        $('#timepicker').val(currentTime);

        $('#timepicker').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
        }).on('dp.change', function(e) {
            $('#pos_time').val(convert(e.date));
        });


        $('#datepicker').datetimepicker({
            format: '{{ $date_picker_format }}',
            locale: '{{ $settings->locale }}',
            defaultDate: moment_Date,
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right"
            }
        }).on('dp.change', function(e) {
            pos_date =  moment(e.date).format('YYYY-MM-DD');
        });

        function removeCoupon () {
            couponApplied = false;
            $('#coupon').val('');
            $('#coupon_id').val('');
            $('#coupon_amount').val(0);
            couponAmount = 0;
            calculateTotal();
            $('.couponDiscountBox').remove();
            $('#removeCouponBox').hide();
            $('#applyCouponBox').show();
        }

        $('#applyCoupon').click(function () {

            let cartTotal = 0;
            let cartSubTotal = 0;
            let cartDiscount = $('#cart-discount').val();
            let cartTax = $('#cart-tax').val();
            let discount = 0;
            let tax = 0;

            $("input[name='cart_prices[]']").each(function( index ) {
                let servicePrice = $(this).val();
                let qty = $("input[name='cart_quantity[]']").eq(index).val();
                cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));
            });

            $("#cart-sub-total").html("{{ $settings->currency->currency_symbol }}"+cartSubTotal.toFixed(2));

            if(parseFloat(cartDiscount) > 0){
                if(cartDiscount > 100) cartDiscount = 100;

                discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);
            }

            cartSubTotal = (cartSubTotal - discount).toFixed(2);

            if(parseFloat(cartTax) > 0){
                tax = ((parseFloat(cartTax)/100)*cartSubTotal);
                $('#cart-tax-amount').html("{{ $settings->currency->currency_symbol }}"+tax.toFixed(2));
            }

            cartTotal = (parseFloat(cartSubTotal) + parseFloat(tax));


            var couponVal = $('#coupon').val();
            couponCodeValue = couponVal;
            var userID    = $('#user_id').val();
            var cart_discount    = $('#cart-discount').val();
            var cartServices = [];
            var titles = $('input[name^=cart_services]').map(function(idx, elem) {
                cartServices.push([$(elem).val(),$("input[name='cart_quantity[]']").eq(idx).val()]);
            }).get();

            if(cartServices === undefined || cartServices === "" || cartServices === null || cartServices.length <= 0){
                return $.showToastr("@lang('errors.coupon.serviceRequired')", 'error');
            }
            if(userID === undefined || userID === "" || userID === null){
                return $.showToastr("@lang('errors.coupon.customerRequired')", 'error');
            }
            if(couponVal === undefined || couponVal === "" || couponVal === null){
                return $.showToastr("@lang('errors.coupon.required')", 'error');
            }else{
                var currencySymbol = '{{ $settings->currency->currency_symbol }}';
                var token = '{{ csrf_token() }}';
                $.easyAjax({
                    url: '{{ route('admin.pos.apply-coupon') }}',
                    type: 'POST',
                    data: {'_token':token,'coupon':couponVal,'user_id':userID, 'cart_services': cartServices, 'cart_discount': cart_discount},
                    success: function (response) {
                        if(response.status == 'success'){
                            couponApplied = true;
                            couponAmount = response.amount;
                            if(couponAmount>cartTotal)
                            {
                                    couponAmount = cartTotal;
                            }
                            calculateTotal();
                            $('.couponDiscountBox').remove();
                            var discountElement =     '<div class="col-md-6 border-bottom couponDiscountBox" id="couponDiscountBox">'+
                                                        '<h6>@lang('app.couponDiscount')('+response.couponData.title+') </h6>'+
                                                        '</div>'+
                                                        '<div class="col-md-6 border-bottom couponDiscountBox">'+
                                                        ' <h5 id="coupon-discount-amount">-'+currencySymbol +couponAmount+'</h5>'+
                                                        '</div>';

                            $(discountElement).insertBefore( "#totalAmountBox" );

                            $('#coupon_id').val(response.couponData.id);
                            $('#coupon_amount').val(couponAmount);

                            $('#applyCouponBox').hide();
                            $('#removeCouponBox').show();

                            $('#couponCodeAmonut').html(couponAmount);
                            $('#couponCode').html(response.couponData.title);
                        }
                    }
                })
            }

        });

        function updateCoupon () {

            let cartTotal = 0;
            let cartSubTotal = 0;
            let cartDiscount = $('#cart-discount').val();
            let cartTax = $('#cart-tax').val();
            let discount = 0;
            let tax = 0;

            $("input[name='cart_prices[]']").each(function( index ) {
                let servicePrice = $(this).val();
                let qty = $("input[name='cart_quantity[]']").eq(index).val();
                cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));
            });

            $("#cart-sub-total").html("{{ $settings->currency->currency_symbol }}"+cartSubTotal.toFixed(2));

            if(parseFloat(cartDiscount) > 0){
                if(cartDiscount > 100) cartDiscount = 100;

                discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);
            }

            cartSubTotal = (cartSubTotal - discount).toFixed(2);

            if(parseFloat(cartTax) > 0){
                tax = ((parseFloat(cartTax)/100)*cartSubTotal);
                $('#cart-tax-amount').html("{{ $settings->currency->currency_symbol }}"+tax.toFixed(2));
            }

            cartTotal = (parseFloat(cartSubTotal) + parseFloat(tax));

            if(couponApplied){

                var userID    = $('#user_id').val();

                var cart_discount   = $('#cart-discount').val();
                var cartServices = [];
                var titles = $('input[name^=cart_services]').map(function(idx, elem) {
                    cartServices.push([$(elem).val(),$("input[name='cart_quantity[]']").eq(idx).val()]);
                }).get();

                if(cartServices === undefined || cartServices === "" || cartServices === null ||
                    cartServices.length <= 0 || userID === undefined || userID === "" || userID === null){
                    removeCoupon ();
                    return false;
                }
                if(couponCodeValue === undefined || couponCodeValue === "" || couponCodeValue === null){
                    removeCoupon ();
                    return false;
                }else{
                    var currencySymbol = '{{ $settings->currency->currency_symbol }}';
                    var token = '{{ csrf_token() }}';
                    $.easyAjax({
                        url: '{{ route('admin.pos.update-coupon') }}',
                        type: 'POST',
                        data: {'_token':token,'coupon':couponCodeValue, 'cart_discount': cart_discount, 'cart_services': cartServices},
                        success: function (response) {
                            if(response.status != 'fail'){

                                couponAmount = response.amount;
                                if(couponAmount>cartTotal)
                                {
                                    couponAmount = cartTotal;
                                }

                                $('.couponDiscountBox').remove();
                                var discountElement =     '<div class="col-md-6 border-bottom couponDiscountBox" id="couponDiscountBox">'+
                                                            '<h6>@lang('app.couponDiscount')('+response.couponData.title+') </h6>'+
                                                            '</div>'+
                                                            '<div class="col-md-6 border-bottom couponDiscountBox">'+
                                                            ' <h5 id="coupon-discount-amount">-'+currencySymbol +couponAmount+'</h5>'+
                                                            '</div>';

                                $(discountElement).insertBefore( "#totalAmountBox" );

                                $('#coupon_id').val(response.couponData.id);
                                $('#coupon_amount').val(couponAmount);

                                $('#applyCouponBox').hide();
                                $('#removeCouponBox').show();

                                $('#couponCodeAmonut').html(couponAmount);
                                $('#couponCode').html(response.couponData.title);
                                calculateTotal();
                            }
                            else{
                                removeCoupon ();
                            }
                        }
                    })
                }
            }
        }

        $("#employee").select2({
            placeholder: "Select Employee",
            allowClear: true
        });

        $('#user_id').select2({
            language: '{{ $settings->locale }}',
            ajax: {
                url: "{{ route('admin.pos.search-customer') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };

                },
                cache: true
            },
            placeholder: "@lang('modules.booking.selectCustomer')",
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        }).on('select2:select', function (e) {
            var userId = $('#user_id').val();
            $('#user-error').text('');
            customerDetails(userId);
        });

        function formatRepo(repo) {
            if (repo.loading) {
                return repo.text;
            }

            var markup = "<div class='row'>" +
                "<div class='col-md-12'><h6>" + repo.full_name + "</h6></div>";

            markup += "<div class='col-md-6'><i class='fa fa-envelope'></i>: " + repo.email + "</div>" +
                "<div class='col-md-6'><i class='fa fa-phone'></i>: " + repo.mobile + "</div>" +
                "</div>";

            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.full_name || repo.text;
        }

        $('#select-customer').click(function () {
            var url = '{{ route('admin.pos.select-customer')}}';

            $.ajaxModal('#application-modal', url);
        });

        var customerDetails = function(userId){
            let url = '{{route('admin.customers.show', ":id")}}';
            url = url.replace(":id", userId);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if(response.status == 'success'){
                        $('#pos-customer-details').html(response.view);
                    }
                }
            })
        };

        function filterServices() {
            $.easyAjax({
                url: '{{ route('admin.pos.filter-services') }}',
                type: 'GET',
                container: '#pos-services',
                data: $('#filter-form').serialize(),
                success: function (response) {
                    $('#pos-services').html(response.view);
                }
            })
        }

        $('#category-filter, #location-filter').change(function () {
            filterServices();
        });

        $("body").on('click', '.add-to-cart', function () {
            let serviceId = $(this).data('service-id');
            let servicePrice = $(this).data('service-price');
            let serviceName = $(this).data('service-name');

            let isAdded = checkExists(serviceId); //check if service already added to cart

            if(isAdded === false){
                let cartRow =  '<tr>\n' +
                    '                                <td><input type="hidden" name="cart_services[]" value="'+serviceId+'">'+serviceName+'</td>\n' +
                    '                                <td><input type="hidden" name="cart_prices[]" class="cart-price-'+serviceId+'" value="'+servicePrice+'">{{ $settings->currency->currency_symbol }}'+servicePrice+'</td>\n' +
                    '                                <td><div class="input-group">\n' +
                    '                  <div class="input-group-prepend">\n' +
                    '                    <button type="button" class="btn btn-default quantity-minus" data-service-id="'+serviceId+'"><i class="fa fa-minus"></i></button>\n' +
                    '                  </div>\n' +
                    '                  <input type="text" readonly name="cart_quantity[]" data-service-id="'+serviceId+'" class="form-control cart-service-'+serviceId+'" value="1">\n' +
                    '                  <div class="input-group-append">\n' +
                    '                    <button type="button" class="btn btn-default quantity-plus" data-service-id="'+serviceId+'"><i class="fa fa-plus"></i></button>\n' +
                    '                  </div>\n' +
                    '                </div></td>\n' +
                    '                                <td class="text-right cart-subtotal-'+serviceId+'">{{ $settings->currency->currency_symbol }}'+servicePrice+'</td>\n' +
                    '                                <td>\n' +
                    '                                    <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row" data-toggle="tooltip"\n' +
                    '                                      data-original-title="@lang('app.delete')"><i class="fa fa-times"\n' +
                    '                                                                                                   aria-hidden="true"></i></a>\n' +
                    '                                </td>\n' +
                    '                            </tr>';

                if ($("#cart-table tbody").has('tr#no-service')) {
                    $("#cart-table tbody tr#no-service").remove();
                }
                $("#cart-table tbody").append(cartRow);
                $('#cart-item-error').text('');
                calculateTotal();
                updateCoupon ();
            }
        });

        $("#cart-table").on('change', "input[name='cart_quantity[]']", function () {
            let serviceId = $(this).data('service-id');
            let qty = $(this).val();

            updateCartQuantity(serviceId, qty);
        });

        $('#cart-table').on('click', '.quantity-minus', function () {
            let serviceId = $(this).data('service-id');

            let qty = $('.cart-service-'+serviceId).val();
            qty = parseInt(qty)-1;

            if(qty < 1){
                qty = 1;
            }
            $('.cart-service-'+serviceId).val(qty);

            updateCartQuantity(serviceId, qty);
        });

        $('#cart-table').on('click', '.quantity-plus', function () {
            let serviceId = $(this).data('service-id');

            let qty = $('.cart-service-'+serviceId).val();
            qty = parseInt(qty)+1;

            $('.cart-service-'+serviceId).val(qty);

            updateCartQuantity(serviceId, qty);
        });

        function checkExists(serviceId) {
            let isAdded = $(".cart-service-"+serviceId).length;
            let qty = $(".cart-service-"+serviceId).val();
            qty = parseInt(qty)+1;

            $(".cart-service-"+serviceId).val(qty);

            if(isAdded > 0){
                return updateCartQuantity(serviceId, qty);
            }
            return false;
        }

        function updateCartQuantity(serviceId, qty) {

            let servicePrice = $('.cart-price-'+serviceId).val();

            let subTotal = (parseFloat(servicePrice) * parseInt(qty));

            $('.cart-subtotal-'+serviceId).html("{{ $settings->currency->currency_symbol }}"+subTotal.toFixed(2));

            calculateTotal();
            updateCoupon ();
        }

        $('#cart-table').on('click', '.delete-cart-row', function () {
            $(this).closest('tr').remove();
            calculateTotal();
            if ($("#cart-table tbody tr").length == 0) {
                $("#cart-table tbody").html(`<tr id="no-service">
                            <td colspan="5" class="text-center text-danger">@lang("messages.selectService")</td>
                        </tr>`);
            }
            updateCoupon ();
        });

        $('#empty-cart').click(function () {
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $("input[name='cart_prices[]']").each(function( index ) {
                            $(this).closest('tr').remove();
                        });
                        calculateTotal();
                        if ($("#cart-table tbody tr").length == 0) {
                            $("#cart-table tbody").html(`<tr id="no-service">
                                        <td colspan="5" class="text-center text-danger">@lang("messages.selectService")</td>
                                    </tr>`);
                        }
                        updateCoupon ();
                    }
                });
        });

        $('#cart-discount').keyup(function () {
            if ($(this).val() > 100) {
                $(this).val(100);
            }
            if(couponApplied)
            {
                updateCoupon ();
            }else{
                calculateTotal();
            }
        });

        $('#cart-tax').change(function () {
            calculateTotal();
            updateCoupon ();
        });

        function calculateTotal() {
            let cartTotal = 0;
            let cartSubTotal = 0;
            let cartDiscount = $('#cart-discount').val();
            let cartTax = $('#cart-tax').val();
            let discount = 0;
            let tax = 0;

            $("input[name='cart_prices[]']").each(function( index ) {
                let servicePrice = $(this).val();
                let qty = $("input[name='cart_quantity[]']").eq(index).val();
                cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));
            });

            $("#cart-sub-total").html("{{ $settings->currency->currency_symbol }}"+cartSubTotal.toFixed(2));

            if(parseFloat(cartDiscount) > 0){
                if(cartDiscount > 100) cartDiscount = 100;

                discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);
            }

            cartSubTotal = (cartSubTotal - discount).toFixed(2);

            if(parseFloat(cartTax) > 0){
                tax = ((parseFloat(cartTax)/100)*cartSubTotal);
                $('#cart-tax-amount').html("{{ $settings->currency->currency_symbol }}"+tax.toFixed(2));
            }

            cartTotal = (parseFloat(cartSubTotal) + parseFloat(tax));

            if(couponAmount > 0)
            {
                if(cartTotal>=couponAmount )
                {
                    cartTotal =  (cartTotal - couponAmount);
                }
                else
                {
                    cartTotal = 0;
                }
            }

            cartTotal = cartTotal.toFixed(2);

            $("#cart-total-input").val(cartTotal);
            $("#cart-total").html("{{ $settings->currency->currency_symbol }}"+cartTotal);
            $("#payment-modal-total").html("{{ $settings->currency->currency_symbol }}"+cartTotal);
            globalCartTotal = cartTotal;
        }
    </script>

    <script>
        $('#do-payment').click(function () {
            let cartItems = $("input[name='cart_prices[]']").length;
            let userId = $("#user_id").val();

            if(userId === null){
                swal('@lang("modules.booking.selectCustomer")');

                $('#user-error').html('@lang("modules.booking.selectCustomer")');
                return false;
            }
            else{
                $('#user-error').html('');
            }

            if(cartItems === 0){
                swal('@lang("modules.booking.addItemsToCart")');
                $('#cart-item-error').html('@lang("modules.booking.addItemsToCart")');
                return false;
            }
            else{
                $('#cart-item-error').html('');
            }
           $('#payment-modal').modal('show');
        });

        $('#payment-modal').on('shown.bs.modal', function () {
            $('#cash-given').val(globalCartTotal);
            $('#cash-return').html("{{ $settings->currency->currency_symbol }}"+'0.00');
            $('#cash-remaining').html("{{ $settings->currency->currency_symbol }}"+'0.00');
            $('#cash-given').select();
        });

        $('#cash-given').focus(function () {
            $(this).select();
        })

        $("input[name='payment_gateway']").click(function () {
            let paymentMode = $(this).val();

            if(paymentMode === 'cash'){
                $('#cash-mode').show();
            }
            else {
                $('#cash-mode').hide();
            }
        });

        function limitDecimalPlaces(e)
        {
            let count = 2; /* digits after decimal */
            if (e.target.value.indexOf('.') == -1) { return; }
            if ((e.target.value.length - e.target.value.indexOf('.')) > count) {
                e.target.value = parseFloat(e.target.value).toFixed(count);
            }
        }

        $('#cash-given').keyup(function () {
            let cashGiven = $(this).val();

            if(cashGiven === ''){
                cashGiven = 0;
            }

            let total = $('#cart-total-input').val();
            let cashReturn = (parseFloat(total) - parseFloat(cashGiven)).toFixed(2);
            let cashRemaining = (parseFloat(total) - parseFloat(cashGiven)).toFixed(2);

            if(cashRemaining < 0 || total>=cashGiven){
                cashRemaining = parseFloat(0).toFixed(2);
            }

            if(cashReturn < 0){
                cashReturn = Math.abs(cashReturn);
            }
            else{
                cashReturn = parseFloat(0).toFixed(2);
            }

            $('#cash-return').html("{{ $settings->currency->currency_symbol }}"+cashReturn);
            $('#cash-remaining').html("{{ $settings->currency->currency_symbol }}"+cashRemaining);

        });

        $('#submit-cart').click(function () {
            let url = '{{route('admin.pos.store')}}';
            $.easyAjax({
                url: url,
                container: '#pos-form',
                type: "POST",
                data: $('#pos-form').serialize()+'&payment_gateway='+$('input[name="payment_gateway"]:checked').val()+'&pos_date='+pos_date+'&pos_time='+pos_time,
                redirect: true
            })
        });

        function checkValue(discount)
        {
            if(discount=='')
            {
                $('#cart-discount').val(0);
            }
        }

        function convert(str) 
        {
            var date = new Date(str);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            hours = ("0" + hours).slice(-2);
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }
    </script>

@endpush
