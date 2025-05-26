<style>
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
<form action="" id="update-form" class="ajax-form">
    @method('PUT')
    @csrf
<div class="row mt-2 mb-3">
    <div class="col-md-4 border-right"> <strong>@lang('app.name')</strong> <br>
        <p class="text-muted"><i class="icon-user"></i> {{ ucwords($booking->user->name) }}</p>
    </div>
    <div class="col-md-4 border-right"> <strong>@lang('app.email')</strong> <br>
        <p class="text-muted"><i class="icon-email"></i> {{ $booking->user->email ?? '--' }}</p>
    </div>
    <div class="col-md-4"> <strong>@lang('app.mobile')</strong> <br>
        <p class="text-muted"><i class="icon-mobile"></i> {{ $booking->user->mobile ? $booking->user->formatted_mobile : '--' }}</p>
    </div>
</div>
<hr>


@if ($booking->deal_id!='')
    <div class="row">
        <div class="col-md-12 border-right"> <strong>@lang('app.deal') @lang('app.name')</strong> <br>
            <a data-toggle="tooltip" data-original-title="@lang('app.view') @lang('app.deal')" href="{{ route('admin.deals.index') }}">{{$booking->deal->title}}</a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6 border-right"> <strong>@lang('app.deal') @lang('app.location')</strong> <br>
            <p> {{$booking->deal->location->name}} </p>
        </div>
        <div class="col-md-6 border-right"> <strong>@lang('app.deal') @lang('app.quantity')</strong> <br>
            <p> {{$booking->deal_quantity}} </p>
        </div>
    </div>
    <hr>
@endif





<div class="row">
    <div class="col-sm-4"> <strong>@lang('app.booking') @lang('app.date')</strong> <br>
        <div class="form-group">
            <input type="text" class="form-control datepicker" name="boking_date" value="
            @if ($booking->date_time!='') {{ $booking->date_time->format($settings->date_format) }} @endif ">
            <input type="hidden" name="booking_date" id="booking_date" value="{{ $booking->date_time->format('Y-m-d') }}">
        </div>
    </div>
    <div class="col-sm-4"> <strong>@lang('app.booking') @lang('app.time') </strong> <br>
        <div class="form-group">
            <div class="input-group date">
                <input type="text" class="form-control" name="booking_time" id="booking_time" value="@if ($booking->date_time!='') {{ $booking->date_time->translatedFormat($settings->time_format) }} @endif ">
                <span class="input-group-append input-group-addon">
                    <button type="button" class="btn btn-default">
                        <span class="fa fa-clock-o"></span>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-4"> <strong>@lang('app.booking') @lang('app.status')</strong> <br>
        <div class="form-group">
            <select name="status" id="booking-status" class="form-control">
                <option value="completed" @if($booking->status == 'completed') selected @endif>@lang('app.completed')</option>
                <option value="pending" @if($booking->status == 'pending') selected @endif>@lang('app.pending')</option>
                <option value="approved" @if($booking->status == 'approved') selected @endif>@lang('app.approved')</option>
                <option value="in progress" @if($booking->status == 'in progress') selected @endif>@lang('app.in progress')</option>
                <option value="canceled" @if($booking->status == 'canceled') selected @endif>@lang('app.canceled')</option>
            </select>
        </div>
    </div>
</div>
    <hr>
<div class="row">
    <div class="col-sm-12"> <strong>@lang('menu.employee')</strong> <br>
        <div class="form-group">
            <select name="employee_id[]" id="employee_id" class="form-control" multiple="multiple" style="width: 100%">
                <option value=""> @lang('app.selectEmployee') </option>
                @foreach($employees as $employee)
                    <option
                            @if(in_array($employee->id, $selected_booking_user)) selected @endif
                    value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<hr>

<div class="row">

    @if($booking->deal_id == '')
        <div class="col-md-12 mb-2">
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-plus"></i> @lang('app.add') @lang('app.item')
                </button>
                <div class="dropdown-menu">
                    @foreach($businessServices as $service)
                        <a class="dropdown-item add-item"
                        data-price="{{ $service->discounted_price }}"
                        data-service-id="{{ $service->id }}" href="javascript:;">{{ ucwords($service->name) }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    <div class="col-md-12">
        <table class="table table-condensed" id="cart-table">
            <thead class="bg-secondary">
            <tr>
                <th>@lang('app.item')</th>
                <th>@lang('app.unitPrice')</th>
                <th width="120">@lang('app.quantity')</th>
                <th class="text-right">@lang('app.amount')</th>
                @if ($booking->deal_id=='')
                    <th><i class="icon-settings"></i></th>
                @endif

            </tr>
            </thead>
            <tbody>
            @foreach($booking->items as $key=>$item)
                <tr>
                    <td><input type="hidden" name="cart_services[]" value="{{ $item->business_service_id }}">
                        {{ ucwords($item->businessService->name) }}</td>
                    <td><input type="hidden" name="cart_prices[]" class="cart-price-{{ $item->business_service_id }}" value="{{ number_format((float)$item->unit_price, 2, '.', '') }}">{{ $settings->currency->currency_symbol.number_format((float)$item->unit_price, 2, '.', '') }}</td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="button" class="btn btn-default quantity-minus" data-service-id="{{ $item->business_service_id }}"><i class="fa fa-minus"></i></button>
                            </div>
                            <input type="text" readonly name="cart_quantity[]" data-service-id="{{ $item->business_service_id }}" class="form-control cart-service-{{ $item->business_service_id }}" value="{{ $item->quantity }}">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default quantity-plus" id="btn{{$item->business_service_id}}" data-service-id="{{ $item->business_service_id }}"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </td>


                    @if ($booking->deal_id!='')
                        <td class="text-right cart-subtotal-{{ $item->business_service_id }}">{{ $settings->currency->currency_symbol.number_format((float)($item->unit_price  * $item->quantity), 2, '.', '') }} x {{$booking->deal_quantity}} =  {{ $settings->currency->currency_symbol.number_format((float)($item->unit_price  * $item->quantity * $booking->deal_quantity), 2, '.', '') }}</td>
                    @else
                        <td class="text-right cart-subtotal-{{ $item->business_service_id }}">{{ $settings->currency->currency_symbol.number_format((float)($item->businessService->discounted_price  * $item->quantity), 2, '.', '') }}</td>
                        <td>
                            <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row"><i class="fa fa-times" aria-hidden="true"></i></a>
                        </td>
                    @endif







                </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    <div class="col-md-6 border-top">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed">
                    <tr class="h6">
                        <td class="border-top-0">@lang('modules.booking.paymentMethod')</td>
                        <td class="border-top-0 "><i class="fa fa-money"></i> {{ $booking->payment_gateway }}</td>
                    </tr>
                    <tr class="h6">
                        <td>@lang('modules.booking.paymentStatus')</td>
                        <td><div class="form-group">
                                <select name="payment_status" id="payment-status" class="form-control select2">
                                    <option value="completed" @if($booking->payment_status == 'completed') selected @endif>@lang('app.completed')</option>
                                    <option value="pending" @if($booking->payment_status == 'pending') selected @endif>@lang('app.pending')</option>
                                </select>
                            </div></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 border-top">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed">
                    <tr class="h6">
                        <td class="border-top-0 text-right w-50">@lang('app.subTotal')</td>
                        <td class="border-top-0" id="cart-sub-total">{{ $settings->currency->currency_symbol.number_format((float)$booking->original_amount, 2, '.', '') }}</td>
                    </tr>

                    @if($booking->deal_id == '')
                        <tr class="h6">
                            <td class="text-right">@lang('app.discount')</td>
                            <td><input type="number" id="cart-discount" name="cart_discount" class="form-control" step=".01" min="0" value="{{ $booking->discount_percent }}"></td>
                        </tr>
                    @endif

                    @if($booking->tax_amount > 0)
                    <tr class="h6">
                        <input type="hidden" id="cart-tax" name="cart_tax" value="{{ $booking->tax_percent }}">
                        <td class="text-right">{{ $booking->tax_name.' ('.$booking->tax_percent.'%)' }}</td>
                        <td id="cart-tax-amount">{{ $settings->currency->currency_symbol.number_format((float)$booking->tax_amount, 2, '.', '') }}</td>
                    </tr>
                    @endif
                    @if($booking->coupon_discount > 0)
                        <tr class="h6">
                            <input type="hidden" id="coupon_id" name="coupon_id" value="{{ $booking->coupon_id }}">
                            <input type="hidden" id="coupon_amount" name="coupon_amount" value="{{ $booking->coupon_discount }}">
                            <td class="text-right">@lang('app.couponDiscount') ({{ $booking->coupon->coupon_code}})</td>
                            <td id="couponAmount">{{ $settings->currency->currency_symbol.number_format((float)$booking->coupon_discount, 2, '.', '') }}</td>
                        </tr>
                    @endif
                    <tr class="h5">
                        <td class="text-right">@lang('app.total')</td>
                        <td id="cart-total">{{ $settings->currency->currency_symbol.number_format((float)$booking->amount_to_pay, 2, '.', '') }}
                            <input type="hidden"  id="cart-total-input">
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mt-2">
                <button class="btn btn-outline-danger delete-row" data-row-id="{{ $booking->id }}" type="button"><i class="fa fa-times"></i> @lang('app.delete') @lang('app.booking')</button>
            </div>
            <div class="mt-2">
                <button type="button" id="update-booking" data-booking-id="{{ $booking->id }}" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.update')</button>
                <div id="cart-item-error" class="invalid-feedback"></div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="hidden_booking_time" id="hidden_booking_time" value="{{ $booking->date_time->format($settings->time_format) }}">
</form>
<script src="/js/utils.js"></script>
<script>
    var couponAmount = 0;
    var couponCodeValue = '';
//    var couponApplied = false;

    $("#employee_id").select2({
        placeholder: "Select Employee",
        allowClear: true
    });


    $('.datepicker').datetimepicker({
        format: '{{ $date_picker_format }}',
        locale: '{{ $settings->locale }}',
        allowInputToggle: true,
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down",
            previous: "fa fa-angle-double-left",
            next: "fa fa-angle-double-right"
        }
    }).on('dp.change', function(e) {
        $('#booking_date').val(moment(e.date).format('YYYY-MM-DD'));
    });

    $('#booking_time').datetimepicker({
            format: '{{ $time_picker_format }}', 
            locale: '{{ $settings->locale }}',
            allowInputToggle: true,
            defaultDate: moment(),
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
        }).on('dp.change', function(e) {
            $('#hidden_booking_time').val(convert(e.date));
        });

    $("#cart-table").on('change', "input[name='cart_quantity[]']", function () {
        let serviceId = $(this).data('service-id');
        let qty = $(this).val();

        updateCartQuantity(serviceId, qty);
    });

    $('#cart-table').on('click', '.quantity-minus', function () {
        if('{{$booking->deal_id}}'!=''){
            return false;
        }
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

        if('{{$booking->deal_id}}'!=''){
            return false;
        }

        let serviceId = $(this).data('service-id');

        let qty = $('.cart-service-'+serviceId).val();
        qty = parseInt(qty)+1;

        $('.cart-service-'+serviceId).val(qty);

        updateCartQuantity(serviceId, qty);
    });

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
        updateCoupon ();
    });

    $('#cart-discount').keyup(function () {
        if ($(this).val() == '') {
            $(this).val(0);
        }
        if ($(this).val() > 100) {
            $(this).val(100);
        }
        calculateTotal();
        updateCoupon ();
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
        cartSubTotal = (parseFloat(cartSubTotal) - discount).toFixed(2);

        if(parseFloat(cartTax) > 0){
            tax = ((parseFloat(cartTax)/100)*cartSubTotal);
            $('#cart-tax-amount').html("{{ $settings->currency->currency_symbol }}"+tax.toFixed(2));
        }

        cartTotal = (parseFloat(cartSubTotal) + tax);

        couponAmount = $('#coupon_amount').val();
        if(couponAmount)
        {
            if(cartTotal>couponAmount)
            {
                cartTotal =  (cartTotal - couponAmount);
            }
            else
            {
                cartTotal = 0;
            }
        }

        cartTotal =  parseFloat(cartTotal).toFixed(2);

        $("#cart-total-input").val(cartTotal);

        $("#cart-total").html("{{ $settings->currency->currency_symbol }}"+cartTotal);
        $("#payment-modal-total").html("{{ $settings->currency->currency_symbol }}"+cartTotal);
    }

    $('.add-item').click(function () {
        let serviceId = $(this).data('service-id');
        let serviceName = $(this).html();
        let servicePrice = parseFloat($(this).data('price')).toFixed(2);
        let serviceItems = $('#cart-table tbody tr td:first-child input[type="hidden"]');
        let serviceItemsCount = 0;

        let item = '<tr>\n' +
            '                    <td><input type="hidden" name="cart_services[]" value="'+serviceId+'">\n' +
            '                        '+serviceName+'</td>\n' +
            '                    <td><input type="hidden" name="cart_prices[]" class="cart-price-'+serviceId+'" value="'+servicePrice+'">{{ $settings->currency->currency_symbol }}'+servicePrice+'</td>\n' +
            '                    <td>\n' +
            '                        <div class="input-group">\n' +
            '                            <div class="input-group-prepend">\n' +
            '                                <button type="button" class="btn btn-default quantity-minus" data-service-id="'+serviceId+'"><i class="fa fa-minus"></i></button>\n' +
            '                            </div>\n' +
            '                            <input type="text" readonly name="cart_quantity[]" data-service-id="'+serviceId+'" class="form-control cart-service-'+serviceId+'" value="1">\n' +
            '                            <div class="input-group-append">\n' +
            '                                <button type="button" class="btn btn-default quantity-plus" id="btn'+serviceId+'" data-service-id="'+serviceId+'"><i class="fa fa-plus"></i></button>\n' +
            '                            </div>\n' +
            '                        </div>\n' +
            '                    </td>\n' +
            '                    <td class="text-right cart-subtotal-'+serviceId+'">{{ $settings->currency->currency_symbol }}'+servicePrice+'</td>\n' +
            '                    <td>\n' +
            '                        <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row"><i class="fa fa-times" aria-hidden="true"></i></a>\n' +
            '                    </td>\n' +
            '                </tr>';


        serviceItems.each(function()
        {
            if(this.value==serviceId)
            {
                serviceItemsCount += 1;
            }
        });

        if(serviceItemsCount>0)
        {
            $('#btn'+serviceId).click();
        }
        else
        {
            $('#cart-table tbody').append(item);
        }

        calculateTotal();
        updateCoupon ();

    });

    // Update coupon during change discount
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
        cartSubTotal = (parseFloat(cartSubTotal) - discount).toFixed(2);

        if(parseFloat(cartTax) > 0){
            tax = ((parseFloat(cartTax)/100)*cartSubTotal);
            $('#cart-tax-amount').html("{{ $settings->currency->currency_symbol }}"+tax.toFixed(2));
        }

        cartTotal = (parseFloat(cartSubTotal) + tax).toFixed(2);


        @if($booking->coupon_id)

            cartSubTotal   = 0;
            var cart_discount  = $('#cart-discount').val();
            var cartServices   = [];
            var coupon_id      = {{$booking->coupon_id}};

            $("input[name='cart_prices[]']").each(function( index ) {
                let servicePrice = $(this).val();
                let qty = $("input[name='cart_quantity[]']").eq(index).val();
                cartServices = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));
            });

            if(cartServices === undefined || cartServices === "" || cartServices === null ||
                cartServices.length <= 0){
                return false;
            }

            var currencySymbol = '{{ $settings->currency->currency_symbol }}';
            var token = '{{ csrf_token() }}';

            $.easyAjax({
                url: '{{ route('admin.bookings.update-coupon') }}',
                type: 'POST',
                data: {'_token':token,'coupon_id':coupon_id, 'cart_discount': cart_discount, 'cart_services': cartServices},
                success: function (response) {
                    if(response.status != 'fail'){
                        couponAmount = response.amount;
                        if(couponAmount>cartTotal)
                        {
                            couponAmount = cartTotal;
                        }
                        $('#couponAmount').html(currencySymbol+couponAmount);
                        $('#coupon_amount').val(couponAmount);
                        calculateTotal();
                    }
                }
            });

        @endif
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
