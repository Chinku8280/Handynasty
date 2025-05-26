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
    @foreach ($voucher_items as $voucher_item)
        <tr id="row{{$voucher_item->businessService->id}}">
            <td><input type="hidden" name="voucher_services[]" value="{{$voucher_item->businessService->id}}">{{$voucher_item->businessService->name}}</td>
            <td><input type="hidden" class="voucher-price-{{$voucher_item->businessService->id}}" name="voucher_unit_price[]" value="{{$voucher_item->unit_price}}">{{$settings->currency->currency_symbol}}  {{$voucher_item->unit_price}}</td>
            <td style="width: 15%">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-default quantity-minus" data-service-id="{{$voucher_item->businessService->id}}"><i class="fa fa-minus"></i></button>
                    </div>
                    <input data-service-id="{{$voucher_item->businessService->id}}" type="text" readonly name="voucher_quantity[]" class="form-control voucher-service-{{$voucher_item->businessService->id}}" value="{{$voucher_item->quantity}}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default quantity-plus" data-service-id="{{$voucher_item->businessService->id}}"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </td>
            <td name="voucher-subtotal[]" class="voucher-subtotal-{{$voucher_item->businessService->id}}">{{$settings->currency->currency_symbol}}{{$voucher_item->unit_price*$voucher_item->quantity}}</td>
            <td style="width: 15%">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">{{$settings->currency->currency_symbol}}</span>
                    </div>
                    <input @if ($voucher_item->voucher->discount_type=="percentage") readonly @endif type="number" name="voucher_discount[]" onkeypress="return isNumberKey(event)" class="form-control voucher_discount" value="{{$voucher_item->discount_amount}}">
                </div>
            </td>

            <td name="voucher-total[]" class="voucher-total-{{$voucher_item->businessService->id}}">{{$settings->currency->currency_symbol}}{{$voucher_item->total_amount}}</td>

            <td><a onclick="deleteRow({{$voucher_item->businessService->id}}, '{{$voucher_item->name}}')" href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row" data-toggle="tooltip" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a></td>

        </tr>
        @php $subTotal += $voucher_item->unit_price; @endphp
    @endforeach

    <tr>
        <td colspan="3"></td>
        <td id="voucher-sub-total">{{$settings->currency->currency_symbol}}{{$voucher_item->voucher->original_amount}}</td>
        <td id="voucher-discount-total">{{$settings->currency->currency_symbol}} {{$voucher_item->voucher->original_amount-$voucher_item->voucher->voucher_amount}}</td>
        <td id="voucher-total-price">{{$settings->currency->currency_symbol}}{{$voucher_item->voucher->voucher_amount}}</td>
    </tr>
</table>


