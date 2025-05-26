<br>
<table class="table table-bordered table-condensed" width="100%" id="voucher_table">
    <thead class="thead-dark">
        <tr>
            <th>@lang('app.service')</th>
            <th>@lang('app.unitPrice')</th>
            <th>@lang('app.quantity')</th>
            <th>@lang('app.subTotal')</th>
            <th>@lang('app.discount')</th>
            <th>@lang('app.total')</th>
            <th>#</th>
        </tr>
    </thead>

    @php $subTotal=0; $discount=0; $total=0; @endphp
    @foreach ($voucher_list as $voucher)
        <tr id="row{{$voucher->id}}">
            <td><input type="hidden" name="voucher_services[]" value="{{$voucher->id}}">{{$voucher->name}}</td>
            <td><input type="hidden" class="voucher-price-{{$voucher->id}}" name="voucher_unit_price[]" value="{{$voucher->price}}">{{$settings->currency->currency_symbol}}  {{$voucher->price}}</td>
            <td style="width: 15%">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-default quantity-minus" data-service-id="{{$voucher->id}}"><i class="fa fa-minus"></i></button>
                    </div>
                        <input data-service-id="{{$voucher->id}}" type="text" readonly name="voucher_quantity[]" class="form-control voucher-service-{{$voucher->id}}" value="1">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default quantity-plus" data-service-id="{{$voucher->id}}"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </td>
            <td name="voucher-subtotal[]" class="voucher-subtotal-{{$voucher->id}}">{{$settings->currency->currency_symbol}}{{$voucher->price}}</td>
            <td style="width: 15%">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">{{$settings->currency->currency_symbol}}</span>
                    </div>
                    <input type="number" name="voucher_discount[]" onkeypress="return isNumberKey(event)" class="form-control voucher_discount" value="0">
                </div>
            </td>
        <td name="voucher-total[]" class="voucher-total-{{$voucher->id}}">{{$settings->currency->currency_symbol}}{{$voucher->price}}</td>
            <td><a onclick="deleteRow({{$voucher->id}}, '{{$voucher->name}}')" href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row" data-toggle="tooltip" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a></td>
        </tr>
        @php $subTotal += $voucher->price; @endphp
    @endforeach

    <tr>
        <td colspan="3"></td>
        <td id="voucher-sub-total">{{$settings->currency->currency_symbol}}{{$subTotal}}</td>
        <td id="voucher-discount-total">{{$settings->currency->currency_symbol}} {{$discount}}</td>
        <td id="voucher-total-price">{{$settings->currency->currency_symbol}}{{$subTotal}}</td>
    </tr>

</table>

<script>
    $('#discount_amount').val('{{$subTotal}}');
    $('#original_amt').val('{{$subTotal}}');
</script>

