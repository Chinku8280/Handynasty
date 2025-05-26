<div class="tab-pane" id="pc-6" role="tabpanel">

    <ul class="nav nav-pills nav-tabs-rounded nav-justified" role="tablist">
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link active" data-toggle="tab" href="#available_voucher_tab"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="fas fa-home"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-money-bill me-2"></i>Available
                    vouchers</span>
            </a>
        </li>
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link" data-toggle="tab" href="#used_voucher_tab"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="far fa-user"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-list-alt me-2"> </i> Used
                    vouchers</span>
            </a>
        </li>
    </ul>
    <div class="tab-content p-3 text-muted">
        {{-- available voucher --}}
        <div class="tab-pane active" id="available_voucher_tab" role="tabpanel">
            <table id="available_voucher_table"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Voucher Name</th>
                        <th>Discount</th>
                        <th>Start On</th>
                        <th>Expire On</th>
                        <th>Validity</th>
                        <th>Valid Till</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- voucher --}}
                    @foreach ($filtered_available_vouchers as $voucher)
                        <tr>
                            <td>{{ $voucher->title }}</td>
                            <td>
                                @if ($voucher->discount_type == "percent")
                                    {{ $voucher->discount }}%
                                @else
                                    ${{ $voucher->discount }}
                                @endif                                
                            </td>
                            <td>{{ $voucher->formattedStartDate }}</td>
                            <td>{{ $voucher->formattedEndDate }}</td>
                            <td>{{ $voucher->validity }} {{ $voucher->validity_type }}</td>
                            <td>{{ $voucher->validUntil }}</td>
                            <td class="description-cell" title="{{ strip_tags($voucher->description) }}">{{ strip_tags($voucher->description) }}</td>
                            <td>
                                <span class="badge bg-success">Unused</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary voucher_use_btn" data-voucher_id="{{$voucher->id}}" data-voucher_redeem_id="{{$voucher->voucher_redeem_id}}">Use</button>
                            </td>
                        </tr>
                    @endforeach

                    {{-- loyalty shop product --}}
                    @foreach ($filtered_available_loyalty_shop_product as $item)
                        <tr>
                            <td>{{ $item->title }}</td>   
                            <td></td>                        
                            <td>{{ $item->formattedStartDate }}</td>
                            <td>{{ $item->formattedEndDate }}</td>
                            <td>{{ $item->validity }} {{ $item->validity_type }}</td>
                            <td>{{ $item->validUntil }}</td>
                            <td class="description-cell" title="{{ strip_tags($item->description) }}">{{ strip_tags($item->description) }}</td>
                            <td>
                                <span class="badge bg-success">Unused</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary loyalty_shop_use_btn" data-loyalty_shop_id="{{$item->id}}" data-loyalty_shop_redeem_id="{{$item->loyalty_shop_redeem_id}}">Use</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- used voucher --}}
        <div class="tab-pane" id="used_voucher_tab" role="tabpanel">
            <table id="used_voucher_table"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Voucher Name</th>
                        <th>Discount</th>                        
                        <th>Start On</th>
                        <th>Expire On</th>
                        <th>Validity</th>
                        <th>Description</th>
                        <th>Used On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- voucher --}}
                    @foreach ($usedVoucher as $voucher)
                        <tr>
                            <td>{{ $voucher->title }}</td>
                            <td>
                                @if ($voucher->discount_type == "percent")
                                    {{ $voucher->discount }}%
                                @else
                                    ${{ $voucher->discount }}
                                @endif                                
                            </td>                           
                            <td>{{ $voucher->formattedStartDate }}</td>
                            <td>{{ $voucher->formattedEndDate }}</td>
                            <td>{{ $voucher->validity }} {{ $voucher->validity_type }}</td>
                            <td class="description-cell" title="{{ strip_tags($voucher->description) }}">{{ strip_tags($voucher->description) }}</td>
                            <td>{{$voucher->used_on_date_format}}</td>
                            <td><span class="badge bg-danger">Used</span></td>
                        </tr>
                    @endforeach

                    {{-- loyalty shop product --}}
                    @foreach ($used_loyalty_shop_product as $item)
                        <tr>
                            <td>{{ $item->title }}</td>             
                            <td></td>                                        
                            <td>{{ $item->formattedStartDate }}</td>
                            <td>{{ $item->formattedEndDate }}</td>
                            <td>{{ $item->validity }} {{ $item->validity_type }}</td>
                            <td class="description-cell" title="{{ strip_tags($item->description) }}">{{ strip_tags($item->description) }}</td>
                            <td>{{$item->used_on_date_format}}</td>
                            <td><span class="badge bg-danger">Used</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>