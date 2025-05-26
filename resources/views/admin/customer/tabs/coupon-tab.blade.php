<div class="tab-pane" id="pc-3" role="tabpanel">
    <ul class="nav nav-pills nav-tabs-rounded nav-justified" role="tablist">
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link active" data-toggle="tab" href="#available_coupon_tab"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="fas fa-home"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-list-alt me-2"></i>Available
                    Coupons</span>
            </a>
        </li>
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link" data-toggle="tab" href="#used_coupon_tab"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="far fa-user"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-hourglass-end me-2"></i>Used Coupons
                </span>
            </a>
        </li>


    </ul>
    <div class="tab-content p-3 text-muted">
        <div class="tab-pane active" id="available_coupon_tab" role="tabpanel">
            <table id="available_coupon_table"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Coupon Code</th>
                        <th>Discount</th>
                        <th>Valid Upto</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($unusedCoupons as $coupon)                      
                        <tr>
                            <td>{{ $coupon->coupon_code }}</td>
                            <td>
                                @if (!empty($coupon->percent))
                                    {{ $coupon->percent }}%
                                @else
                                    {{ $coupon->amount }}
                                @endif
                            </td>
                            <td>{{ $coupon->formattedStartDate }} to
                                {{ $coupon->formattedEndDate }}</td>
                            <td><span class="badge bg-success">Unused</span></td>
                            <td>
                                <button type="button" class="btn btn-primary coupon_use_btn" data-coupon_id="{{$coupon->id}}">Use</button>
                            </td>
                        </tr>                        
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="used_coupon_tab" role="tabpanel">
            <table id="used_coupon_table"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Coupon Code</th>
                        <th>Discount</th>
                        <th>Valid Upto</th>
                        <th>Used On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usedCoupons as $coupon)
                        <tr>
                            <td>{{ $coupon->coupon_code }}</td>
                            <td>
                                @if (!empty($coupon->percent))
                                {{ $coupon->percent }}%
                                @else
                                    {{ $coupon->amount }}
                                @endif
                            </td>
                            <td>{{ $coupon->formattedStartDate }} to
                                {{ $coupon->formattedEndDate }}</td>
                            <td>{{$coupon->used_on_date_format}}</td>
                            <td><span class="badge bg-danger">Used</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>