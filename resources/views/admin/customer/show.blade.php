@extends('layouts.master')

@push('head-css')
    <style>
        #myTable td {
            padding: 0;
        }

        .status {
            font-size: 80%;
        }

        .widget-user-2 .widget-user-image>img {
            width: 120px;
            height: 120px;
            position: relative;
            top: 0;
            left: 25px;
        }

        .profile-view .profile-img .avatar {
            font-size: 24px;
            height: 120px;
            line-height: 150px;
            margin: 0;
            width: 130px;
            border-radius: 50%;
            display: inline-block;
            overflow: hidden;
            vertical-align: middle;
            position: relative;
        }

        .profile-view .profile-img-wrap {
            height: 125px;
            width: 150px;
            position: absolute;
            background: #fff;
            overflow: hidden;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            cursor: pointer;
            margin: 0 auto;
            position: relative;

        }

        .profile-view .profile-basic {
            margin-left: 170px;
        }

        .profile-info-left {
            border-right: 2px dashed #fffdfe;
        }

        .personal-info {
            list-style: none;
            margin-bottom: 0;
            padding: 0;
        }

        .personal-info li {
            margin-bottom: 10px;
        }

        .profile-ul .nav-item .nav-link {
            color: #380814
        }

        .profile-ul .nav-item .nav-link.active {
            color: #380814 !important;
            border-color: #380814 !important;
            border-bottom: none;
        }

        .l_points {
            border: 2px solid #380814;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
            background-color: #380814;
            color: #fff;
            font-weight: 700;
            line-height: normal;
        }

        /* loyalty program start */

        /* .coin_box_group {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px 5px;
            justify-items: center;
            align-items: center;
        } */

        .first_coin_box_group, .second_coin_box_group {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px 5px;
            justify-items: center;
            align-items: center;
        }

        .coin_box_group .coin_box {
            padding: 5px;
            border: 1px solid #380814;
            border-radius: 50px;     
            width: 45px !important;
            height: 45px !important;  
            position: relative;    
        }
        .coin_box_group .reward_class{
            border: 4px #b7a58d solid;
        }
        .coin_box_group .span_reward_class{
            position: absolute;
            top: 35px;
            left: -5px;
        }

        .span_extra_reward_class img{
            width: 43px;
            height: 43px;
            position: absolute;
            top: 0px;
            left: 0px;
        }

        .span_free_reward_class {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 45px; /* Match the coin_box size */
            height: 45px; /* Match the coin_box size */
            border-radius: 50%; /* Make it circular */
            color: #380814; /* Text color */
            font-size: 10px; /* Adjust font size */
            font-weight: bold; /* Make the text bold */
            position: absolute;
            top: 0;
            left: 0;
            text-align: center;
            line-height: 1.2; /* Adjust line height for better text alignment */
        }

        /* loyalty program end */

        /* health question start */

        .multi_date_signature_table tr td, .multi_date_signature_table tr th
        {
            border: 1px solid;
        }

        .multi_date_signature_table tr td
        {
            width: 15%;
        }

        .multi_date_signature_table tr td img
        {
            width: 50%;
        }

        /* health question end */

        /* voucher start */

        #available_voucher_table td.description-cell, #used_voucher_table td.description-cell {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px; /* adjust this as needed */
        }

        /* voucher end */

    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-widget widget-user-2">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" src="{{ $customer->user_image_url }}" height="60em"
                                    width="60em" alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                        </div>
                        <div class="col-md-10 text-white">
                            <div class="row">
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="profile-info-left">
                                                <h3 class="user-name m-t-0 fw-bold">
                                                    {{ $customer->prefix . ' ' . $customer->fname . ' ' . $customer->lname }}
                                                </h3>
                                                <h6 class="company-role mt-0 mb-2">
                                                    <strong>Email :-
                                                    </strong> {{ $customer->email }}
                                                </h6>
                                                <h6 class="company-role mt-0 mb-2">
                                                    <strong>Gender :-
                                                    </strong> {{ $customer->gender }}
                                                </h6>
                                                <small>Referral
                                                    Code :- 012356</small>
                                                <div class="staff-id">
                                                    Customer ID : CLT-0001
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <ul class="personal-info">
                                                <li>
                                                    <span class="title">Phone:</span>
                                                    <span class="text"><a
                                                            href="">{{ $customer->mobile }}</a></span>
                                                </li>
                                                <li>
                                                    <span class="title">Email:</span>
                                                    <span class="text"><a href="">{{ $customer->email }}</a></span>
                                                </li>

                                                <li>
                                                    <span class="title">Outlet:</span>
                                                    <span
                                                        class="text">{{ \App\Outlet::find($customer->outlet_id)->outlet_name ?? '' }}</span>
                                                </li>
                                                <li>
                                                    <span class="title">Date of Birth:</span>
                                                    <span class="text">{{ !empty($customer->dob) ? date('d-m-Y', strtotime($customer->dob)) : '' }}</span>
                                                </li>  
                                                
                                                <li>
                                                    <span class="title">Status:</span>
                                                    <span class="text">
                                                        @if ($customer->status == 'active')
                                                            <label class="badge badge-success">Active</label>
                                                        @elseif ($customer->status == 'inactive')
                                                            <label class="badge badge-danger">Inactive</label>
                                                        @endif
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="">
                                        <div class="d-flex justify-content-end">
                                            <h3 class="widget-user-username">{{ ucwords($customer->name) }}
                                                @permission('update_customer')
                                                    <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                                        class="btn btn-outline-light">@lang('app.edit')</a>
                                                @endpermission
                                                @permission('delete_customer')
                                                    <a href="javascript:;" class="btn btn-outline-light customer_delete_btn"
                                                        data-row-id="{{ $customer->id }}">@lang('app.delete')</a>
                                                @endpermission

                                            </h3>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="card-footer row">

                    <div class="col-md-10 offset-md-2">
                        <div class="row">

                            <div class="col-md-12">
                                <h4>@lang('modules.customer.bookingStats')</h4>
                            </div>
                            <div class="col-md-12">
                                <div class="row" id="customer-stats">
                                    @include('partials.customer_stats')
                                </div>
                            </div>
                        </div>
                    </div>

                </div> --}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="" id="filter-status" class="form-control">
                                    <option value="">@lang('app.filter') @lang('app.status'): @lang('app.viewAll')</option>
                                    <option value="completed">@lang('app.completed')</option>
                                    <option value="pending">@lang('app.pending')</option>
                                    <option value="in progress">@lang('app.in progress')</option>
                                    <option value="canceled">@lang('app.canceled')</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control datepicker" name="filter_date" id="filter-date"
                                    placeholder="@lang('app.booking') @lang('app.date')">
                                <input type="hidden" name="hidden_date" id="hidden_date">
                            </div>
                        </div> --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <button type="button" id="reset-filter" class="btn btn-danger"><i class="fa fa-times"></i>
                                    @lang('app.reset')</button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" style="text-align: right;">
                                <button type="button" class="btn" id="loyalty_program_add_btn_2" style="background-color: #541726;color: #fff;">Add Stamp</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <div class="card-footer">
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified profile-ul" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" data-toggle="tab" href="#pc-1" role="tab"
                                                aria-selected="true">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="d-none d-sm-block">Appointment</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-2" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="d-none d-sm-block">Invoice</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-3" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                <span class="d-none d-sm-block">Coupons</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-4" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Offers</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-5" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Loyalty
                                                    Coins</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-8" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Loyalty
                                                    Program</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-6" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Vouchers</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-7" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Packages</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-9" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Health Questionnaire</span>
                                            </a>
                                        </li>

                                        {{-- <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-toggle="tab" href="#pc-10" role="tab"
                                                aria-selected="false" tabindex="-1">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Loyalty Shop Product</span>
                                            </a>
                                        </li> --}}

                                    </ul>
                                </div>
                                {{-- 
                                <table id="myTable" class="table table-borderless w-100">
                                    <thead class="hide">
                                        <tr>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                </table> --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="tab-content p-3 text-muted">

                                            @include('admin.customer.tabs.appointment-tab')

                                            @include('admin.customer.tabs.invoice-tab')
                                            
                                            @include('admin.customer.tabs.coupon-tab')

                                            @include('admin.customer.tabs.offers-tab')

                                            @include('admin.customer.tabs.loyality-point-tab')

                                            @include('admin.customer.tabs.loyality-program-tab')

                                            @include('admin.customer.tabs.voucher-tab')

                                            @include('admin.customer.tabs.packages-tab')

                                            @include('admin.customer.tabs.health-question-tab')

                                            {{-- @include('admin.customer.tabs.loyalty-shop-product-tab') --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5 offset-md-1" id="booking-detail">

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.customer.modals.loyalty-program-modal')

@endsection

@push('footer-js')
    <script>
        $(document).ready(function () {
            $('#filter-status').change(function () {
                var selectedStatus = $(this).val();

                $('.tab-content .tab-pane').each(function () {
                    var tableId = $(this).find('table').attr('id');
                    filterTable(tableId, selectedStatus);
                });
            });

            function filterTable(tableId, selectedStatus) {
                $('#' + tableId + ' tbody tr').each(function () {
                    var rowStatus = $(this).find('td:last-child span').text().toLowerCase();

                    if (selectedStatus === '' || rowStatus === selectedStatus.toLowerCase()) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
        $(document).ready(function() {

            $('.select2').select2();

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
                    next: "fa fa-angle-double-right",
                }
            }).on("dp.change", function(e) {
                $('#hidden_date').val(moment(e.date).format('YYYY-MM-DD'));
                table._fnDraw();
            });

            function updateBooking(currEle) {
                let url = '{{ route('admin.bookings.update', ':id') }}';
                url = url.replace(':id', currEle.data('booking-id'));

                $.easyAjax({
                    url: url,
                    container: '#update-form',
                    type: "POST",
                    data: $('#update-form').serialize(),
                    success: function(response) {
                        if (response.status == "success") {
                            $('#booking-detail').hide().html(response.view).fadeIn('slow');
                            $('#customer-stats').hide().html(response.customerStatsView).fadeIn('slow');
                            table._fnDraw();
                        }
                    }
                })
            }

            $('body').on('click', '#update-booking', function() {
                let cartItems = $("input[name='cart_prices[]']").length;

                if (cartItems === 0) {
                    swal('@lang('modules.booking.addItemsToCart')');
                    $('#cart-item-error').html('@lang('modules.booking.addItemsToCart')');
                    return false;
                } else {
                    $('#cart-item-error').html('');
                    var updateButtonEl = $(this);
                    if ($('#booking-status').val() == 'completed' && $('#payment-status').val() ==
                        'pending' && $('.fa.fa-money').parent().text().indexOf('cash') !== -1) {
                        swal({
                            text: '@lang('modules.booking.changePaymentStatus')',
                            closeOnClickOutside: false,
                            buttons: [
                                'NO', 'YES'
                            ]
                        }).then(function(isConfirmed) {
                            if (isConfirmed) {
                                $('#payment-status').val('completed');
                            }
                            updateBooking(updateButtonEl);
                        });
                    } else {
                        updateBooking(updateButtonEl);
                    }
                }

            });

            var table = $('#myTable').dataTable({
                responsive: true,
                // processing: true,
                "searching": false,
                serverSide: true,
                "ordering": false,
                ajax: {
                    'url': '{!! route('admin.bookings.index') !!}',
                    "data": function(d) {
                        return $.extend({}, d, {
                            "filter_status": $('#filter-status').val(),
                            "filter_customer": '{{ $customer->id }}',
                            "filter_date": $('#hidden_date').val(),
                        });
                    }
                },
                language: languageOptions(),
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [{
                    data: 'id',
                    name: 'id'
                }]
            });
            new $.fn.dataTable.FixedHeader(table);

            $('body').on('click', '.delete-row', function() {
                var id = $(this).data('row-id');
                swal({
                        icon: "warning",
                        buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                        dangerMode: true,
                        title: "@lang('errors.areYouSure')",
                        text: "@lang('errors.deleteWarning')",
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            var url = "{{ route('admin.bookings.destroy', ':id') }}";
                            url = url.replace(':id', id);

                            var token = "{{ csrf_token() }}";

                            $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {
                                    '_token': token,
                                    '_method': 'DELETE'
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        $.unblockUI();
                                        // swal("Deleted!", response.message, "success");
                                        table._fnDraw();
                                        $('#booking-detail').html('');
                                    }
                                }
                            });
                        }
                    });
            });

            $('body').on('click', '.cancel-row', function() {
                var id = $(this).data('row-id');
                swal({
                        icon: "warning",
                        buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                        dangerMode: true,
                        title: "@lang('errors.areYouSure')",
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            var url = "{{ route('admin.bookings.requestCancel', ':id') }}";
                            url = url.replace(':id', id);

                            var token = "{{ csrf_token() }}";

                            $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {
                                    '_token': token,
                                    '_method': 'POST'
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        $.unblockUI();
                                        // swal("Deleted!", response.message, "success");
                                        table._fnDraw();
                                        $('#booking-detail').html('');
                                    }
                                }
                            });
                        }
                    });
            });

            $('#myTable').on('click', '.view-booking-detail', function() {
                let bookingId = $(this).data('booking-id');
                let url = '{{ route('admin.bookings.show', ':id') }}';
                url = url.replace(':id', bookingId);

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function(response) {
                        if (response.status == "success") {
                            $('html, body').animate({
                                scrollTop: $("#booking-detail").offset().top - 50
                            }, 2000);
                            $('#booking-detail').hide().html(response.view).fadeIn('slow');
                        }
                    }
                });
            });

            $('body').on('click', '.edit-booking', function() {
                let bookingId = $(this).data('booking-id');
                let url = '{{ route('admin.bookings.edit', ':id') }}';
                url = url.replace(':id', bookingId);

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function(response) {
                        if (response.status == "success") {
                            $('#booking-detail').hide().html(response.view).fadeIn('slow');
                        }
                    }
                });
            });

            $('#filter-status, #filter-customer').change(function() {
                table._fnDraw();
            })

            $('#reset-filter').click(function() {
                $('#filter-status, #filter-date').val('');
                $("#filter-customer").val('').trigger('change');
                $("#hidden_date").val('').trigger('change');
                table._fnDraw();
            })

            
            $('body').on('click', '.send-reminder', function() {
                let bookingId = $(this).data('booking-id');
                $.easyAjax({
                    type: 'POST',
                    url: '{{ route('admin.bookings.sendReminder') }}',
                    data: {
                        bookingId: bookingId,
                        _token: '{{ csrf_token() }}'
                    }
                });
            });
        });

        $(document).ready(function() {
            // $('.assign-button').each(function() {
            //     var button = $(this);
            //     var packageId = button.data('package-id');
            //     var userId = button.data('user-id');
            //     var status = localStorage.getItem('status_' + packageId + '_' + userId) || button.data(
            //         'status');

            //     if (status == 1) {
            //         button.text('Assigned');
            //         button.removeClass('btn-success').addClass('btn-danger');
            //     }

            //     button.data('status', status);
            // });

            // $('.assign-button').click(function() {
            //     var button = $(this);
            //     var packageId = button.data('package-id');
            //     var userId = button.data('user-id');
            //     var status = button.data('status');

            //     if (status == 1) {
            //         // If already assigned, do nothing
            //         return;
            //     }

            //     $.ajax({
            //         url: '{{ route('admin.assign.package') }}',
            //         type: 'POST',
            //         data: {
            //             packageId: packageId,
            //             userId: userId,
            //             _token: '{{ csrf_token() }}'
            //         },
            //         success: function(response) {
            //             localStorage.setItem('status_' + packageId + '_' + userId, response
            //                 .status);

            //             button.data('status', response.status);
            //             button.text('Assigned');
            //             button.removeClass('btn-success').addClass('btn-danger');
            //             button.prop('disabled', true);
            //         },
            //         error: function(error) {
            //             // Handle the error if needed
            //             console.error(error);
            //         }
            //     });
            // });            
        });
        function assignPackage() {
            var packageId = $('#package_select').val();
            var userId = '{{ $customer->id }}';

            $.ajax({
                url: '{{ route('admin.assign.package') }}',
                type: 'POST',
                data: {
                    packageId: packageId,
                    userId: userId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // console.log("Success:", response);

                    if (response.message === 'Package already assigned to the user' || response.message === 'Package assigned successfully') {

                        if (response.action === 'redirect') {
                            window.location.href = response.url;
                            return;
                        }

                        var serialNumber = $('#PackageTableBody tr').length + 1;

                        var newRow = '<tr>' +
                            '<td>' + serialNumber + '</td>' +
                            '<td>' + response.title + '</td>' +
                            '<td>' + response.amount + '</td>' +
                            '<td>' + response.coin + '</td>' +
                            '<td><span class="badge bg-success">Active</span></td>' +
                            '</tr>';

                        $('#PackageTableBody').prepend(newRow);

                        $('#assign_package').modal('hide');
                    } else {
                        console.error("Unexpected response:", response);
                    }
                },
                error: function(error) {
                    console.error("Error:", error);
                }
            });
        }
    </script>

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script> --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <script>
        function openSecondModal() {
            $('#loyality_points').modal('show');
        }

        let serialNumber = 1;

        function saveLoyaltyPoints(customerId) {
            let loyaltyPoints = $('#loyalty_points').val();

            $.easyAjax({
                type: 'POST',
                url: '{{ route('admin.customers.storeLoyaltyPoints', ['customer' => ':id']) }}'.replace(':id',
                    customerId),
                data: {
                    loyalty_points: loyaltyPoints,
                    id: customerId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.status == "success")
                    {
                        $("#loyalty_program_onload_modal #loyalty_program_modal_loyalty_point").text(response.loyaltyPoints);
                        $('.loyaltyPointsDisplay_class').text('Available Point:- ' + response.loyaltyPoints);

                        // Add a new row to the loyalty Coins table with the global serial number
                        let newRow = '<tr>' +
                            '<td>' + serialNumber + '</td>' +
                            '<td>' + response.rowData.loyaltyPoints + '</td>' +
                            '<td>' + response.rowData.date + '</td>' +
                            '<td>' + response.rowData.time + '</td>' +
                            '<td>' + response.rowData.points_type + '</td>' +
                            '<td><span class="badge bg-success">Active</span></td>' +
                            '</tr>';

                        $('#loyaltyPointsTable').prepend(newRow);

                        // Increment the serial number for the next row
                        serialNumber++;
                        $('#loyalty_points').val('');
                        $('#loyality_points').modal('hide');
                    }
                    else
                    {
                        $.showToastr(response.message, 'error');
                    }
                },
                error: function(error) {
                    alert('Error saving loyalty Coins');
                }
            });
        }

        function openAssignPackageModal() {
            $('#assign_package').modal('show');
        }

        // *** loyalty program start ***

        /*

        function get_loyalty_program_progress_tracker(category_id = '') 
        {
            // var category_id = $("#loyalty_program_modal_category_id").find(':selected')val();

            // console.log(category_id);

            if(category_id)
            {
                $.ajax({
                    type: "get",
                    url: "{{route('admin.customer.loyalty-program.get-progress-tracker')}}",
                    data: {'customer_id': {{$customer->id ?? ''}}, category_id: category_id},
                    success: function (result) {
                        var total_slots = 20;

                        if (result.db_LoyaltyProgramHour && result.loyalty_program_history_details) 
                        {
                            $("#coin_box_group").html("");

                            var total_hours = parseFloat(result.db_LoyaltyProgramHour.total_hours);
                            var hours_per_stamp = parseFloat(result.hours_per_stamp); // Get hours per stamp from backend
                            var history = result.loyalty_program_history_details; // Get loyalty history details

                            var accumulated_hours = 0; // Track the hours we've processed
                            var current_slot = 0; // Track the current slot being filled
                            var stamp_ids = []; // Hold the history IDs for each slot

                            // Loop through the history entries to fill the slots
                            for (var i = 0; i < history.length; i++) 
                            {
                                var hours_in_entry = history[i].hours;
                                var history_id = history[i].loyalty_program_history_id;

                                // While there are hours left in this entry
                                while (hours_in_entry > 0 && current_slot < total_slots) 
                                {
                                    // Calculate remaining hours to fill the current slot
                                    var remaining_hours_in_slot = hours_per_stamp - accumulated_hours;

                                    // If the current history entry can fill the slot completely
                                    if (hours_in_entry >= remaining_hours_in_slot) 
                                    {               
                                        if(current_slot == 9 || current_slot == 19)
                                        {
                                            var reward_html = "<span class='span_reward_class'>+Reward</span>";
                                            var reward_class = "reward_class";

                                            if(current_slot == 9)
                                            {
                                                var title_html = "Free one 1 hour voucher reward";
                                            }
                                            else if(current_slot == 19)
                                            {
                                                var title_html = "Free two 1 hour voucher reward";
                                            }
                                            else
                                            {
                                                var title_html = "";
                                            }
                                        }   
                                        else
                                        {
                                            var reward_html = "<span class='span_reward_class'></span>";
                                            var reward_class = "";

                                            var title_html = "";
                                        }

                                        accumulated_hours += remaining_hours_in_slot;
                                        hours_in_entry -= remaining_hours_in_slot;

                                        // Add the current history ID to the array for this slot
                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        // Append fully filled slot
                                        var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                        <div class="coin_box ${reward_class}" style="background-color: #380814;" title="${title_html}">${reward_html}</div>                                                       
                                                    </a>`;

                                        $("#coin_box_group").append(html);                                

                                        // Move to the next slot
                                        current_slot++;
                                        accumulated_hours = 0; // Reset accumulated hours for the new slot                                 
                                    }
                                    else // If the current entry only partially fills the slot
                                    {
                                        accumulated_hours += hours_in_entry;

                                        // Add the current history ID to the array for this slot
                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        hours_in_entry = 0; // No more hours left in this entry
                                    }
                                }
                            }

                            // After processing all history entries, check for a partially filled slot
                            if (accumulated_hours > 0 && current_slot < total_slots) 
                            {
                                if(current_slot == 9 || current_slot == 19)
                                {
                                    var reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    var reward_class = "reward_class";

                                    if(current_slot == 9)
                                    {
                                        var title_html = "Free one 1 hour voucher reward";
                                    }
                                    else if(current_slot == 19)
                                    {
                                        var title_html = "Free two 1 hour voucher reward";
                                    }
                                    else
                                    {
                                        var title_html = "";
                                    }
                                }   
                                else
                                {
                                    var reward_html = "<span class='span_reward_class'></span>";
                                    var reward_class = "";

                                    var title_html = "";
                                }

                                var partial_fill = (accumulated_hours / hours_per_stamp) * 100;
                                var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                <div class="coin_box ${reward_class}" style="background: linear-gradient(90deg, #380814 50%, white 50%);" title="${title_html}">${reward_html}</div>
                                            </a>`;

                                $("#coin_box_group").append(html);
                                current_slot++;
                            }

                            // Fill the remaining empty slots, if any
                            for (var j = current_slot; j < total_slots; j++) 
                            {
                                if(j == 9 || j == 19)
                                {
                                    var reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    var reward_class = "reward_class";

                                    if(j == 9)
                                    {
                                        var title_html = "Free one 1 hour voucher reward";
                                    }
                                    else if(j == 19)
                                    {
                                        var title_html = "Free two 1 hour voucher reward";
                                    }
                                    else
                                    {
                                        var title_html = "";
                                    }
                                }   
                                else
                                {
                                    var reward_html = "<span class='span_reward_class'></span>";
                                    var reward_class = "";

                                    var title_html = "";
                                }

                                var html = `<a href="#" data-id="">
                                                <div class="coin_box ${reward_class}" title="${title_html}">${reward_html}</div>    
                                            </a>`;

                                $("#coin_box_group").append(html);
                            }
                        }
                        else
                        {
                            // If no data, append empty slots
                            $("#coin_box_group").html("");

                            for (var i = 0; i < total_slots; i++) 
                            {
                                if(i == 9 || i == 19)
                                {
                                    var reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    var reward_class = "reward_class";

                                    if(i == 9)
                                    {
                                        var title_html = "Free one 1 hour voucher reward";
                                    }
                                    else if(i == 19)
                                    {
                                        var title_html = "Free two 1 hour voucher reward";
                                    }
                                    else
                                    {
                                        var title_html = "";
                                    }
                                }   
                                else
                                {
                                    var reward_html = "<span class='span_reward_class'></span>";
                                    var reward_class = "";

                                    var title_html = "";
                                }

                                var html = `<a href="#" data-id="">
                                                <div class="coin_box ${reward_class}" title="${title_html}">${reward_html}</div>    
                                            </a>`;

                                $("#coin_box_group").append(html);
                            }
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
            else
            {
                $("#coin_box_group").html("");
            }
        } 
            
        */ 

        /*

        function get_loyalty_program_progress_tracker(category_id = '') 
        {
            // var category_id = $("#loyalty_program_modal_category_id").find(':selected')val();

            // console.log(category_id);

            if(category_id)
            {
                $.ajax({
                    type: "get",
                    url: "{{route('admin.customer.loyalty-program.get-progress-tracker')}}",
                    data: {'customer_id': {{$customer->id ?? ''}}, category_id: category_id},
                    success: function (result) {
                        var total_slots = 20;

                        if (result.db_LoyaltyProgramHour && result.loyalty_program_history_details) 
                        {
                            // $("#coin_box_group").html("");
                            $(".first_coin_box_group").html("");
                            $(".second_coin_box_group").html("");

                            var total_hours = parseFloat(result.db_LoyaltyProgramHour.total_hours);
                            var hours_per_stamp = parseFloat(result.hours_per_stamp); // Get hours per stamp from backend
                            var history = result.loyalty_program_history_details; // Get loyalty history details

                            var accumulated_hours = 0; // Track the hours we've processed
                            var current_slot = 0; // Track the current slot being filled
                            var stamp_ids = []; // Hold the history IDs for each slot

                            // Loop through the history entries to fill the slots
                            for (var i = 0; i < history.length; i++) 
                            {
                                var hours_in_entry = history[i].hours;
                                var history_id = history[i].loyalty_program_history_id;

                                // While there are hours left in this entry
                                while (hours_in_entry > 0 && current_slot < total_slots) 
                                {
                                    // Calculate remaining hours to fill the current slot
                                    var remaining_hours_in_slot = hours_per_stamp - accumulated_hours;

                                    // If the current history entry can fill the slot completely
                                    if (hours_in_entry >= remaining_hours_in_slot) 
                                    {               
                                        if(current_slot == 9 || current_slot == 19)
                                        {
                                            var reward_html = "<span class='span_reward_class'>+Reward</span>";
                                            var reward_class = "reward_class";

                                            if(current_slot == 9)
                                            {
                                                var title_html = "Free one 1 hour voucher reward";
                                            }
                                            else if(current_slot == 19)
                                            {
                                                var title_html = "Free two 1 hour voucher reward";
                                            }
                                            else
                                            {
                                                var title_html = "";
                                            }
                                        }   
                                        else
                                        {
                                            var reward_html = "<span class='span_reward_class'></span>";
                                            var reward_class = "";

                                            var title_html = "";
                                        }

                                        accumulated_hours += remaining_hours_in_slot;
                                        hours_in_entry -= remaining_hours_in_slot;

                                        // Add the current history ID to the array for this slot
                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        // Append fully filled slot
                                        var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                        <div class="coin_box ${reward_class}" style="background-color: #380814;" title="${title_html}">${reward_html}</div>                                                       
                                                    </a>`;

                                        if (i < 10) {
                                            $(".first_coin_box_group").append(html);
                                        } else {
                                            $(".second_coin_box_group").append(html);
                                        }

                                        // $("#coin_box_group").append(html);                                

                                        // Move to the next slot
                                        current_slot++;
                                        accumulated_hours = 0; // Reset accumulated hours for the new slot                                 
                                    }
                                    else // If the current entry only partially fills the slot
                                    {
                                        accumulated_hours += hours_in_entry;

                                        // Add the current history ID to the array for this slot
                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        hours_in_entry = 0; // No more hours left in this entry
                                    }
                                }
                            }

                            // After processing all history entries, check for a partially filled slot
                            if (accumulated_hours > 0 && current_slot < total_slots) 
                            {
                                if(current_slot == 9 || current_slot == 19)
                                {
                                    var reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    var reward_class = "reward_class";

                                    if(current_slot == 9)
                                    {
                                        var title_html = "Free one 1 hour voucher reward";
                                    }
                                    else if(current_slot == 19)
                                    {
                                        var title_html = "Free two 1 hour voucher reward";
                                    }
                                    else
                                    {
                                        var title_html = "";
                                    }
                                }   
                                else
                                {
                                    var reward_html = "<span class='span_reward_class'></span>";
                                    var reward_class = "";

                                    var title_html = "";
                                }

                                var partial_fill = (accumulated_hours / hours_per_stamp) * 100;
                                var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                <div class="coin_box ${reward_class}" style="background: linear-gradient(90deg, #380814 50%, white 50%);" title="${title_html}">${reward_html}</div>
                                            </a>`;

                                // $("#coin_box_group").append(html);

                                if (current_slot < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                current_slot++;
                            }

                            // Fill the remaining empty slots, if any
                            for (var j = current_slot; j < total_slots; j++) 
                            {
                                if(j == 9 || j == 19)
                                {
                                    var reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    var reward_class = "reward_class";

                                    if(j == 9)
                                    {
                                        var title_html = "Free one 1 hour voucher reward";
                                    }
                                    else if(j == 19)
                                    {
                                        var title_html = "Free two 1 hour voucher reward";
                                    }
                                    else
                                    {
                                        var title_html = "";
                                    }
                                }   
                                else
                                {
                                    var reward_html = "<span class='span_reward_class'></span>";
                                    var reward_class = "";

                                    var title_html = "";
                                }

                                var html = `<a href="#" data-id="">
                                                <div class="coin_box ${reward_class}" title="${title_html}">${reward_html}</div>    
                                            </a>`;

                                // $("#coin_box_group").append(html);

                                if (j < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }
                            }
                        }
                        else
                        {
                            // If no data, append empty slots

                            // $("#coin_box_group").html("");
                            $(".first_coin_box_group").html("");
                            $(".second_coin_box_group").html("");

                            for (var i = 0; i < total_slots; i++) 
                            {
                                if(i == 9 || i == 19)
                                {
                                    var reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    var reward_class = "reward_class";

                                    if(i == 9)
                                    {
                                        var title_html = "Free one 1 hour voucher reward";
                                    }
                                    else if(i == 19)
                                    {
                                        var title_html = "Free two 1 hour voucher reward";
                                    }
                                    else
                                    {
                                        var title_html = "";
                                    }
                                }   
                                else
                                {
                                    var reward_html = "<span class='span_reward_class'></span>";
                                    var reward_class = "";

                                    var title_html = "";
                                }

                                var html = `<a href="#" data-id="">
                                                <div class="coin_box ${reward_class}" title="${title_html}">${reward_html}</div>    
                                            </a>`;

                                // $("#coin_box_group").append(html);

                                if (i < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // Extra free stamps start

                                if(i == 9)                                               
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(i == 19)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }              
                                
                                // Extra free stamps end
                            }
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
            else
            {
                // $("#coin_box_group").html("");
                $(".first_coin_box_group").html("");
                $(".second_coin_box_group").html("");
            }
        } 

        */

        /*

        function get_loyalty_program_progress_tracker(category_id = '') 
        {
            if (category_id) {
                $.ajax({
                    type: "get",
                    url: "{{route('admin.customer.loyalty-program.get-progress-tracker')}}",
                    data: {'customer_id': {{$customer->id ?? ''}}, category_id: category_id},
                    success: function (result) {
                        var total_slots = 20;

                        $("#loyalty_program_modal_db_total_hours").text(result.total_hours);
                        $("#loyalty_program_modal_db_balance_hours").text(result.balance_hours);
                        $("#loyalty_program_hours_group").show();

                        if (result.db_LoyaltyProgramHour && result.loyalty_program_history_details) 
                        {
                            var total_hours = parseFloat(result.db_LoyaltyProgramHour.total_hours);
                            var hours_per_stamp = parseFloat(result.hours_per_stamp);
                            var history = result.loyalty_program_history_details;

                            var accumulated_hours = 0;
                            var current_slot = 0;
                            var stamp_ids = [];

                            $(".first_coin_box_group, .second_coin_box_group").html("");

                            for (var i = 0; i < history.length; i++) 
                            {
                                var hours_in_entry = history[i].hours;
                                var history_id = history[i].loyalty_program_history_id;

                                while (hours_in_entry > 0 && current_slot < total_slots) 
                                {
                                    var remaining_hours_in_slot = hours_per_stamp - accumulated_hours;

                                    if (hours_in_entry >= remaining_hours_in_slot) {
                                        accumulated_hours += remaining_hours_in_slot;
                                        hours_in_entry -= remaining_hours_in_slot;

                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        var reward_html = "";
                                        var reward_class = "";
                                        var title_html = "";

                                        if (current_slot == 9 || current_slot == 19) {
                                            reward_html = "<span class='span_reward_class'>+Reward</span>";
                                            reward_class = "reward_class";
                                            title_html = current_slot == 9
                                                ? "Free one 1 hour voucher reward"
                                                : "Free two 1 hour voucher reward";
                                        }

                                        var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                        <div class="coin_box ${reward_class}" style="background-color: #380814;" title="${title_html}">${reward_html}</div>
                                                    </a>`;

                                        if (current_slot < 10) {
                                            $(".first_coin_box_group").append(html);
                                        } else {
                                            $(".second_coin_box_group").append(html);
                                        }

                                        // extra free stamp start

                                        if(current_slot==9)
                                        {
                                            var extraHtml = `<a href="#" data-id="">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                                    <span class="span_extra_reward_class">
                                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                    </span>
                                                                </div>
                                                            </a>`;

                                            $(".first_coin_box_group").append(extraHtml);
                                        }
                                        else if(current_slot==19)
                                        {
                                            var extraHtml = `<a href="#" data-id="">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                                    <span class="span_extra_reward_class">
                                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                    </span>
                                                                </div>
                                                            </a>
                                                            <a href="#" data-id="">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                                    <span class="span_extra_reward_class">
                                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                    </span>
                                                                </div>
                                                            </a>`;

                                            $(".second_coin_box_group").append(extraHtml);
                                        }

                                        // extra free stamp end

                                        current_slot++;
                                        accumulated_hours = 0;
                                    } else {
                                        accumulated_hours += hours_in_entry;

                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        hours_in_entry = 0;
                                    }
                                }                             
                            }

                            if (accumulated_hours > 0 && current_slot < total_slots) 
                            {
                                var partial_fill = (accumulated_hours / hours_per_stamp) * 100;
                                var reward_html = "";
                                var reward_class = "";
                                var title_html = "";

                                if (current_slot == 9 || current_slot == 19) {
                                    reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    reward_class = "reward_class";
                                    title_html = current_slot == 9
                                        ? "Free one 1 hour voucher reward"
                                        : "Free two 1 hour voucher reward";
                                }

                                var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                <div class="coin_box ${reward_class}" style="background: linear-gradient(90deg, #380814 ${partial_fill}%, white ${partial_fill}%);" title="${title_html}">${reward_html}</div>
                                            </a>`;

                                if (current_slot < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(current_slot==9)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(current_slot==19)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end

                                current_slot++;
                            }

                            for (var j = current_slot; j < total_slots; j++) 
                            {
                                var reward_html = "";
                                var reward_class = "";
                                var title_html = "";

                                if (j == 9 || j == 19) {
                                    reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    reward_class = "reward_class";
                                    title_html = j == 9
                                        ? "Free one 1 hour voucher reward"
                                        : "Free two 1 hour voucher reward";
                                }

                                var html = `<a href="#" data-id="">
                                                <div class="coin_box ${reward_class}" title="${title_html}">${reward_html}</div>
                                            </a>`;

                                if (j < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(j==9)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(j==19)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end
                            }
                        } 
                        else 
                        {
                            $(".first_coin_box_group, .second_coin_box_group").html("");

                            for (var i = 0; i < total_slots; i++) {
                                var reward_html = "";
                                var reward_class = "";
                                var title_html = "";

                                if (i == 9 || i == 19) {
                                    reward_html = "<span class='span_reward_class'>+Reward</span>";
                                    reward_class = "reward_class";
                                    title_html = i == 9
                                        ? "Free one 1 hour voucher reward"
                                        : "Free two 1 hour voucher reward";
                                }

                                var html = `<a href="#" data-id="">
                                                <div class="coin_box ${reward_class}" title="${title_html}">${reward_html}</div>
                                            </a>`;

                                if (i < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(i==9)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(i==19)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end
                            }
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            } 
            else 
            {
                $("#loyalty_program_modal_db_total_hours").text(0);
                $("#loyalty_program_modal_db_balance_hours").text(10);
                $("#loyalty_program_hours_group").hide();

                $(".first_coin_box_group, .second_coin_box_group").html("");
            }
        }

        */

        /*

        function get_loyalty_program_progress_tracker(category_id = '') 
        {
            if (category_id) {
                $.ajax({
                    type: "get",
                    url: "{{route('admin.customer.loyalty-program.get-progress-tracker')}}",
                    data: {'customer_id': {{$customer->id ?? ''}}, category_id: category_id},
                    success: function (result) {
                        var total_slots = 20;

                        $("#loyalty_program_modal_db_total_hours").text(result.total_hours);
                        $("#loyalty_program_modal_db_balance_hours").text(result.balance_hours);
                        $("#loyalty_program_hours_group").show();

                        if (result.db_LoyaltyProgramHour && result.loyalty_program_history_details) 
                        {
                            var total_hours = parseFloat(result.db_LoyaltyProgramHour.total_hours);
                            var hours_per_stamp = parseFloat(result.hours_per_stamp);
                            var history = result.loyalty_program_history_details;

                            var accumulated_hours = 0;
                            var current_slot = 0;
                            var stamp_ids = [];

                            $(".first_coin_box_group, .second_coin_box_group").html("");

                            for (var i = 0; i < history.length; i++) 
                            {
                                var hours_in_entry = history[i].hours;
                                var history_id = history[i].loyalty_program_history_id;

                                while (hours_in_entry > 0 && current_slot < total_slots) 
                                {
                                    var remaining_hours_in_slot = hours_per_stamp - accumulated_hours;

                                    if (hours_in_entry >= remaining_hours_in_slot) {
                                        accumulated_hours += remaining_hours_in_slot;
                                        hours_in_entry -= remaining_hours_in_slot;

                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                        <div class="coin_box" style="background-color: #380814;">
                                                            <span class='span_reward_class'></span>
                                                        </div>
                                                    </a>`;

                                        if (current_slot < 10) {
                                            $(".first_coin_box_group").append(html);
                                        } else {
                                            $(".second_coin_box_group").append(html);
                                        }

                                        // extra free stamp start

                                        if(current_slot==9)
                                        {
                                            var extraHtml = `<a href="#" data-id="">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                                    <span class="span_extra_reward_class">
                                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                    </span>
                                                                </div>
                                                            </a>`;

                                            $(".first_coin_box_group").append(extraHtml);
                                        }
                                        else if(current_slot==19)
                                        {
                                            var extraHtml = `<a href="#" data-id="">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                                    <span class="span_extra_reward_class">
                                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                    </span>
                                                                </div>
                                                            </a>
                                                            <a href="#" data-id="">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                                    <span class="span_extra_reward_class">
                                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                    </span>
                                                                </div>
                                                            </a>`;

                                            $(".second_coin_box_group").append(extraHtml);
                                        }

                                        // extra free stamp end

                                        current_slot++;
                                        accumulated_hours = 0;
                                    } else {
                                        accumulated_hours += hours_in_entry;

                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        hours_in_entry = 0;
                                    }
                                }                             
                            }

                            if (accumulated_hours > 0 && current_slot < total_slots) 
                            {
                                var partial_fill = (accumulated_hours / hours_per_stamp) * 100;

                                var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                <div class="coin_box" style="background: linear-gradient(90deg, #380814 ${partial_fill}%, white ${partial_fill}%);">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (current_slot < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(current_slot==9)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(current_slot==19)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end

                                current_slot++;
                            }

                            for (var j = current_slot; j < total_slots; j++) 
                            {                            
                                var html = `<a href="#" data-id="">
                                                <div class="coin_box">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (j < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(j==9)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(j==19)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end
                            }
                        } 
                        else 
                        {
                            $(".first_coin_box_group, .second_coin_box_group").html("");

                            for (var i = 0; i < total_slots; i++) 
                            {                     
                                var html = `<a href="#" data-id="">
                                                <div class="coin_box">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (i < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(i==9)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(i==19)
                                {
                                    var extraHtml = `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end
                            }
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            } 
            else 
            {
                $("#loyalty_program_modal_db_total_hours").text(0);
                $("#loyalty_program_modal_db_balance_hours").text(10);
                $("#loyalty_program_hours_group").hide();

                $(".first_coin_box_group, .second_coin_box_group").html("");
            }
        }

        */

        

        function get_loyalty_program_progress_tracker(category_id = '') 
        {
            if (category_id) {
                $.ajax({
                    type: "get",
                    url: "{{route('admin.customer.loyalty-program.get-progress-tracker')}}",
                    data: {'customer_id': {{$customer->id ?? ''}}, category_id: category_id},
                    success: function (result) {

                        $("#loyalty_program_modal_db_total_hours").text(result.total_hours);
                        $("#loyalty_program_modal_db_balance_hours").text(result.balance_hours);
                        $("#loyalty_program_hours_group").show();
                        const totalCircles = 20;
                        const totalTime = result.total_hours; // in hours
                        const hoursPerCircle = result.hours_per_stamp; 

                        const totalUnits = totalTime / hoursPerCircle;
                        const filledCircles = Math.floor(totalUnits);  // 2
                        const hasHalfCircle = totalUnits % 1 >= 0.5; // true
                       
                        // Clear existing content
                        $('.first_coin_box_group').empty();
                        // console.log(filledCircles);
                        let circle_count = 0;
                        // Append filled circles
                        for (let i = 0; i < filledCircles && circle_count < totalCircles; i++) {
                            $('.first_coin_box_group').append(
                                '<a href="#"><div class="coin_box full" style="background-color: #380814;"><span class="span_reward_class"></span></div></a>'
                            );

                            
                            circle_count++;
                            if(circle_count == 10){
                                var extraHtml = totalUnits >= 10
                                                ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`
                                                : `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;
                            $(".first_coin_box_group").append(extraHtml);
                            }
                        }

                        

                        // Append half circle if needed
                        if (hasHalfCircle && circle_count < totalCircles) {
                            $('.first_coin_box_group').append(
                                '<a href="#"><div class="coin_box half" style="background: linear-gradient(90deg, #380814 50%, white 50%);><span class="span_reward_class"></span></div></a>'
                            );
                            circle_count++;
                            if(circle_count == 10){
                                var extraHtml = totalUnits >= 10
                                                ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`
                                                : `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;
                            $(".first_coin_box_group").append(extraHtml);
                            }

                        }

                       

                        // Append remaining empty circles
                        // const remaining = totalCircles - filledCircles - (hasHalfCircle ? 1 : 0);
                        // const remaining = totalCircles - circle_count;
                        for (let i = circle_count; i < totalCircles; i++) {
                            $('.first_coin_box_group').append(
                                '<a href="#"><div class="coin_box"><span class="span_reward_class"></span></div></a>'
                            );

                            circle_count++;
                            if(circle_count == 10){
                                var extraHtml = totalUnits >= 10
                                                ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`
                                                : `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;
                            $(".first_coin_box_group").append(extraHtml);
                            }
                        }

                         // Custom circle after 20
                            if (circle_count >= 20) {
                                var extraHtml = totalUnits >= 20
                                                ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    
                                                    `
                                                : `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>
                                                    
                                                    `;
                                    $(".first_coin_box_group").append(extraHtml);
                            }

                       

                        return 
                       
                        $("#loyalty_program_modal_db_total_hours").text(result.total_hours);
                        $("#loyalty_program_modal_db_balance_hours").text(result.balance_hours);
                        $("#loyalty_program_hours_group").show();

                        if (result.db_LoyaltyProgramHour && result.loyalty_program_history_details) 
                        {
                            var total_hours = parseFloat(result.db_LoyaltyProgramHour.total_hours);
                            var hours_per_stamp = parseFloat(result.hours_per_stamp);
                            var history = result.loyalty_program_history_details;

                            var accumulated_hours = 0;
                            var current_slot = 0;
                            var stamp_ids = [];

                            $(".first_coin_box_group, .second_coin_box_group").html("");

                            for (var i = 0; i < history.length; i++) 
                            {
                                var hours_in_entry = history[i].hours;
                                var history_id = history[i].loyalty_program_history_id;

                                while (hours_in_entry > 0 && current_slot < total_slots) 
                                {
                                    var remaining_hours_in_slot = hours_per_stamp - accumulated_hours;

                                    if (hours_in_entry >= remaining_hours_in_slot) {
                                        accumulated_hours += remaining_hours_in_slot;
                                        hours_in_entry -= remaining_hours_in_slot;

                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                        <div class="coin_box" style="background-color: #380814;">
                                                            <span class='span_reward_class'></span>
                                                        </div>
                                                    </a>`;

                                        if (current_slot < 10) {
                                            $(".first_coin_box_group").append(html);
                                        } else {
                                            $(".second_coin_box_group").append(html);
                                        }

                                        // extra free stamp start

                                        if (current_slot == 9) {
                                            var extraHtml = accumulated_hours >= hours_per_stamp
                                                ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`
                                                : `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;
                                            $(".first_coin_box_group").append(extraHtml);
                                        } else if (current_slot == 19) {
                                            var extraHtml = accumulated_hours >= hours_per_stamp
                                                ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>
                                                    <a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                            <span class="span_extra_reward_class">
                                                                <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                            </span>
                                                        </div>
                                                    </a>`
                                                : `<a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;
                                            $(".second_coin_box_group").append(extraHtml);
                                        }

                                        // extra free stamp end

                                        current_slot++;
                                        accumulated_hours = 0;
                                    } else {
                                        accumulated_hours += hours_in_entry;

                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        hours_in_entry = 0;
                                    }
                                }                             
                            }

                            if (accumulated_hours > 0 && current_slot < total_slots) 
                            {
                                var partial_fill = (accumulated_hours / hours_per_stamp) * 100;

                                var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                <div class="coin_box" style="background: linear-gradient(90deg, #380814 ${partial_fill}%, white ${partial_fill}%);">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (current_slot < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                // Extra free stamp logic

                                if (current_slot == 9) {
                                    var extraHtml = accumulated_hours >= hours_per_stamp
                                        ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>`
                                        : `<a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>`;
                                    $(".first_coin_box_group").append(extraHtml);
                                } else if (current_slot == 19) {
                                    var extraHtml = accumulated_hours >= hours_per_stamp
                                        ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>`
                                        : `<a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>
                                            <a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>`;
                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end

                                current_slot++;
                            }

                            for (var j = current_slot; j < total_slots; j++) 
                            {                            
                                var html = `<a href="#" data-id="">
                                                <div class="coin_box">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (j < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                // Extra free stamp logic

                                if (j == 9) {
                                    var extraHtml = accumulated_hours >= hours_per_stamp
                                        ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>`
                                        : `<a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>`;
                                    $(".first_coin_box_group").append(extraHtml);
                                } else if (j == 19) {
                                    var extraHtml = accumulated_hours >= hours_per_stamp
                                        ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>`
                                        : `<a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>
                                            <a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>`;
                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end
                            }
                        } 
                        else 
                        {
                            $(".first_coin_box_group, .second_coin_box_group").html("");

                            for (var i = 0; i < total_slots; i++) 
                            {                     
                                var html = `<a href="#" data-id="">
                                                <div class="coin_box">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (i < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                // Extra free stamp logic

                                if (i == 9) {
                                    var extraHtml = accumulated_hours >= hours_per_stamp
                                        ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>`
                                        : `<a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>`;
                                    $(".first_coin_box_group").append(extraHtml);
                                } else if (i == 19) {
                                    var extraHtml = accumulated_hours >= hours_per_stamp
                                        ? `<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 20 stamps">
                                                    <span class="span_extra_reward_class">
                                                        <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                    </span>
                                                </div>
                                            </a>`
                                        : `<a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>
                                            <a href="#" data-id="">
                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                </div>
                                            </a>`;
                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end
                            }
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            } 
            else 
            {
                $("#loyalty_program_modal_db_total_hours").text(0);
                $("#loyalty_program_modal_db_balance_hours").text(10);
                $("#loyalty_program_hours_group").hide();

                $(".first_coin_box_group, .second_coin_box_group").html("");
            }
        }

        

        /*

        function get_loyalty_program_progress_tracker(category_id = '') 
        {
            if (category_id) 
            {
                $.ajax({
                    type: "get",
                    url: "{{route('admin.customer.loyalty-program.get-progress-tracker')}}",
                    data: {'customer_id': {{$customer->id ?? ''}}, category_id: category_id},
                    success: function (result) {
                        var total_slots = 20;

                        $("#loyalty_program_modal_db_total_hours").text(result.total_hours);
                        $("#loyalty_program_modal_db_balance_hours").text(result.balance_hours);
                        $("#loyalty_program_hours_group").show();

                        if (result.db_LoyaltyProgramHour && result.loyalty_program_history_details) 
                        {
                            var total_hours = parseFloat(result.db_LoyaltyProgramHour.total_hours);
                            var hours_per_stamp = parseFloat(result.hours_per_stamp);
                            var history = result.loyalty_program_history_details;

                            var accumulated_hours = 0;
                            var current_slot = 0;
                            var stamp_ids = [];

                            $(".first_coin_box_group, .second_coin_box_group").html("");

                            for (var i = 0; i < history.length; i++) 
                            {
                                var hours_in_entry = history[i].hours;
                                var history_id = history[i].loyalty_program_history_id;

                                while (hours_in_entry > 0 && current_slot < total_slots) 
                                {
                                    var remaining_hours_in_slot = hours_per_stamp - accumulated_hours;

                                    if (hours_in_entry >= remaining_hours_in_slot) {
                                        accumulated_hours += remaining_hours_in_slot;
                                        hours_in_entry -= remaining_hours_in_slot;

                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                        <div class="coin_box" style="background-color: #380814;">
                                                            <span class='span_reward_class'></span>
                                                        </div>
                                                    </a>`;

                                        if (current_slot < 10) {
                                            $(".first_coin_box_group").append(html);
                                        } else {
                                            $(".second_coin_box_group").append(html);
                                        }

                                        // extra free stamp start

                                        if(current_slot==9)
                                        {
                                            var extraHtml = `<a href="#" data-id="" id="first_reward">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                                </div>
                                                            </a>`;

                                            $(".first_coin_box_group").append(extraHtml);
                                        }
                                        else if(current_slot==19)
                                        {
                                            var extraHtml = `<a href="#" data-id="" id="second_reward">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                                </div>
                                                            </a>
                                                            <a href="#" data-id="" id="third_reward">
                                                                <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                                    <span class="span_free_reward_class">Free 1 Hour</span>
                                                                </div>
                                                            </a>`;

                                            $(".second_coin_box_group").append(extraHtml);
                                        }

                                        // extra free stamp end

                                        current_slot++;
                                        accumulated_hours = 0;
                                    } else {
                                        accumulated_hours += hours_in_entry;

                                        if (!stamp_ids[current_slot]) {
                                            stamp_ids[current_slot] = [];
                                        }
                                        stamp_ids[current_slot].push(history_id);

                                        hours_in_entry = 0;
                                    }
                                }                             
                            }

                            if (accumulated_hours > 0 && current_slot < total_slots) 
                            {
                                var partial_fill = (accumulated_hours / hours_per_stamp) * 100;

                                var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}">
                                                <div class="coin_box" style="background: linear-gradient(90deg, #380814 ${partial_fill}%, white ${partial_fill}%);">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (current_slot < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(current_slot==9)
                                {
                                    var extraHtml = `<a href="#" data-id="" id="first_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(current_slot==19)
                                {
                                    var extraHtml = `<a href="#" data-id="" id="second_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="" id="third_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end

                                current_slot++;
                            }

                            for (var j = current_slot; j < total_slots; j++) 
                            {                            
                                var html = `<a href="#" data-id="">
                                                <div class="coin_box">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (j < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(j==9)
                                {
                                    var extraHtml = `<a href="#" data-id="" id="first_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(j==19)
                                {
                                    var extraHtml = `<a href="#" data-id="" id="second_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="" id="third_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end
                            }


                            if(result.db_LoyaltyProgramHour.free_one_reward_voucher_flag == 2)
                            {
                                $("#first_reward").replaceWith(`<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank" id="first_reward">
                                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                                        <span class="span_extra_reward_class">
                                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                        </span>
                                                                    </div>
                                                                </a>`);
                            }

                            if(result.db_LoyaltyProgramHour.free_one_reward_voucher_flag == 3)
                            {
                                $("#second_reward").replaceWith(`<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank" id="second_reward">
                                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                                        <span class="span_extra_reward_class">
                                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                        </span>
                                                                    </div>
                                                                </a>`);
                            }

                            if(result.db_LoyaltyProgramHour.free_one_reward_voucher_flag == 4)
                            {
                                $("#third_reward").replaceWith(`<a href="{{ route('admin.customers.show', $customer->id) }}#pc-6" data-id="" target="_blank" id="third_reward">
                                                                    <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                                        <span class="span_extra_reward_class">
                                                                            <img src="{{asset('assets/img/free-stamp.webp')}}" alt="">
                                                                        </span>
                                                                    </div>
                                                                </a>`);
                            }
                        } 
                        else 
                        {
                            $(".first_coin_box_group, .second_coin_box_group").html("");

                            for (var i = 0; i < total_slots; i++) 
                            {                     
                                var html = `<a href="#" data-id="">
                                                <div class="coin_box">
                                                    <span class='span_reward_class'></span>
                                                </div>
                                            </a>`;

                                if (i < 10) {
                                    $(".first_coin_box_group").append(html);
                                } else {
                                    $(".second_coin_box_group").append(html);
                                }

                                // extra free stamp start

                                if(i==9)
                                {
                                    var extraHtml = `<a href="#" data-id="" id="first_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;

                                    $(".first_coin_box_group").append(extraHtml);
                                }
                                else if(i==19)
                                {
                                    var extraHtml = `<a href="#" data-id="" id="second_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>
                                                    <a href="#" data-id="" id="third_reward">
                                                        <div class="coin_box extra_reward_class" title="Extra free 1-hour after 10 stamps">
                                                            <span class="span_free_reward_class">Free 1 Hour</span>
                                                        </div>
                                                    </a>`;

                                    $(".second_coin_box_group").append(extraHtml);
                                }

                                // extra free stamp end
                            }
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });       
            } 
            else 
            {
                $("#loyalty_program_modal_db_total_hours").text(0);
                $("#loyalty_program_modal_db_balance_hours").text(10);
                $("#loyalty_program_hours_group").hide();

                $(".first_coin_box_group, .second_coin_box_group").html("");
            }
        }

        */


        function calculate_total_hours()
        {
            var total_service_hours = 0;

            $("#loyalty_program_modal_sevices_table_tbody tr").each(function () {

                var service_hours = parseFloat($(this).find('.td_service_hours_class').text());
                var service_qty = parseInt($(this).find('.td_service_qty_class input').val());

                total_service_hours += (service_hours * service_qty);

            });

            $("#loyalty_program_modal_total_hours").text("Total Hours required : " + parseFloat(total_service_hours));
        }

        function calculate_total_loyalty_points()
        {
            var total_service_loyalty_point = 0;

            $("#loyalty_program_modal_sevices_table_tbody tr").each(function () {

                var service_loyalty_point = parseInt($(this).find('.td_service_hours_class').data('service_loyalty_point'));
                var service_qty = parseInt($(this).find('.td_service_qty_class input').val());

                total_service_loyalty_point += (service_loyalty_point * service_qty);

            });

            $("#loyalty_point_form").find("#loyalty_points").val(parseInt(total_service_loyalty_point));
            $("#loyalty_program_modal_form").find("#hidden_loyalty_points").val(parseInt(total_service_loyalty_point));
        }

        function get_categorie_by_single_outlet(outlet_id)
        {
            $.ajax({
                type: "get",
                url: "{{route('admin.get-categories-by-single-outlet')}}",
                data: {outlet_id: outlet_id},
                success: function (result) {
                    console.log(result);

                    $("#loyalty_program_modal_sevices_table").find("#loyalty_program_modal_sevices_table_tbody").html("");
                    $("#loyalty_program_modal_total_hours").text("");

                    if(result.categories_loyalty_program.length > 0)
                    {
                        $("#loyalty_program_modal_category_id").html("<option value=''>Select Category</option>");

                        $.each(result.categories_loyalty_program, function (key, value) { 
                            var html = `<option value="${value.id}">${value.name}</option>`;
                        
                            $("#loyalty_program_modal_category_id").append(html);
                        });                       
                    }
                    else
                    {
                        $("#loyalty_program_modal_category_id").html("<option value=''>Select Category</option>");
                        $("#loyalty_program_modal_service_id").html("<option value=''>Select Service</option>");
                    }
                },
                error: function (result) {
                    console.log(result);
                },
            });
        }

        @if (Session::has('outlet_id'))
            get_categorie_by_single_outlet({{ Session::get('outlet_id') }});
        @endif

        // *** loyalty program end ***

        $(document).ready(function () {

            // Get URL parameters
            // Check if there's a hash in the URL (like #pc-9)
            if(window.location.hash) 
            {
                // Activate the tab that corresponds to the hash
                $('a[href="' + window.location.hash + '"]').tab('show');
            }
            
            // Alternative approach using URL parameter
            var urlParams = new URLSearchParams(window.location.search);
            if(urlParams.get('tab') === 'health') 
            {
                $('a[href="#pc-9"]').tab('show');
            }
            else if(urlParams.get('tab') === 'voucher') 
            {
                $('a[href="#pc-6"]').tab('show');
            }           

            // Remove the "?tab=" parameter from the URL if it exists
            if (window.location.href.includes('?tab=')) 
            {
                var newUrl = window.location.href.split('?tab=')[0];
                history.replaceState(null, '', newUrl);
            }

            // customer delete start

            $('body').on('click', '.customer_delete_btn', function() {
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                })
                .then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.customers.destroy', ':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'DELETE'
                            },
                            success: function(response) {
                                console.log(response);
                            }
                        });
                    }
                });
            });

            // customer delete end

            // *** loyalty points start ***
            
            $('#loyaltyPointsTable').dataTable({
                paging: false,
                pagelength: 30
            });

            // *** loyalty points end ***

            // *** coupon tab start ***

            $('#available_coupon_table, #used_coupon_table').dataTable({
                paging: false,
                pagelength: 30
            });
            
            // coupon use start ***

            $('body').on('click', '.coupon_use_btn', function (){

                var coupon_id = $(this).data('coupon_id');

                $.ajax({
                    type: "post",
                    url: "{{route('admin.customers.coupon-used-store')}}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'coupon_id': coupon_id,
                        'customer_id': {{$customer->id ?? ''}}
                    },
                    success: function (result) {
                        console.log(result);

                        if(result.status == "success")
                        {
                            $.showToastr(result.message, 'success');
                            location.reload();
                        }
                        else
                        {
                            $.showToastr(result.message, 'error');
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });

            });

            // coupon use end

            // *** coupon tab end ***

            // *** voucher tab start ***

            $('#available_voucher_table, #used_voucher_table').dataTable({
                paging: false,
                pagelength: 30
            });

            // voucher use start

            $('body').on('click', '.voucher_use_btn', function (){

                var voucher_id = $(this).data('voucher_id');
                var voucher_redeem_id = $(this).data('voucher_redeem_id');
                
                $.ajax({
                    type: "post",
                    url: "{{route('admin.customers.voucher-used-store')}}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'voucher_id': voucher_id,
                        'voucher_redeem_id': voucher_redeem_id,
                        'customer_id': {{$customer->id ?? ''}}
                    },
                    success: function (result) {
                        console.log(result);

                        if(result.status == "success")
                        {
                            $.showToastr(result.message, 'success');
                            location.reload();
                        }
                        else
                        {
                            $.showToastr(result.message, 'error');
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });

            });

            // voucher use end

            // *** voucher tab end ***

            // *** loyalty program start ***

            // open loyalty program modal from loyalty program tab

            $('body').on('click', '#loyalty_program_add_btn', function() {
                get_loyalty_program_progress_tracker();
                $("#loyalty_program_onload_modal #loyalty_point_group").hide();
                $('#loyalty_program_onload_modal').modal('show');
            });

            // open loyalty program modal from outside

            $('body').on('click', '#loyalty_program_add_btn_2', function() {
                console.log("object");
                get_loyalty_program_progress_tracker();
                $("#loyalty_program_onload_modal #loyalty_point_group").show();
                $('#loyalty_program_onload_modal').modal('show');
            });

            // select outlet and fetch categories

            $('body').on('change', '#loyalty_program_modal_outlet_id', function(){

                var outlet_id = $(this).val();

                get_loyalty_program_progress_tracker();

                get_categorie_by_single_outlet(outlet_id);

                // $.ajax({
                //     type: "get",
                //     url: "{{route('admin.get-categories-by-single-outlet')}}",
                //     data: {outlet_id: outlet_id},
                //     success: function (result) {
                //         console.log(result);

                //         $("#loyalty_program_modal_sevices_table").find("#loyalty_program_modal_sevices_table_tbody").html("");
                //         $("#loyalty_program_modal_total_hours").text("");

                //         if(result.categories_loyalty_program.length > 0)
                //         {
                //             $("#loyalty_program_modal_category_id").html("<option value=''>Select Category</option>");

                //             $.each(result.categories_loyalty_program, function (key, value) { 
                //                 var html = `<option value="${value.id}">${value.name}</option>`;
                            
                //                 $("#loyalty_program_modal_category_id").append(html);
                //             });                       
                //         }
                //         else
                //         {
                //             $("#loyalty_program_modal_category_id").html("<option value=''>Select Category</option>");
                //             $("#loyalty_program_modal_service_id").html("<option value=''>Select Service</option>");
                //         }
                //     },
                //     error: function (result) {
                //         console.log(result);
                //     },
                // });

            });

            // select categories and fetch services

            $('body').on('change', '#loyalty_program_modal_category_id', function(){

                var category_id = $(this).val();
                var outlet_id = $("#loyalty_program_modal_outlet_id").val();

                get_loyalty_program_progress_tracker(category_id);

                $.ajax({
                    type: "get",
                    url: "{{route('admin.get-services-by-category-outlet')}}",
                    data: {category_id: category_id, outlet_id: outlet_id},
                    success: function (result) {
                        console.log(result);

                        $("#loyalty_program_modal_sevices_table").find("#loyalty_program_modal_sevices_table_tbody").html("");
                        $("#loyalty_program_modal_total_hours").text("");

                        if(result.services.length > 0)
                        {
                            $("#loyalty_program_modal_service_id").html("<option value=''>Select Service</option>");

                            $.each(result.services, function (key, value) { 
                                var html = `<option value="${value.id}">(${value.time} ${value.time_type}) ${value.name}</option>`;
                            
                                $("#loyalty_program_modal_service_id").append(html);
                            });                       
                        }
                        else
                        {
                            $("#loyalty_program_modal_service_id").html("<option value=''>Select Service</option>");
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    },
                });

            });

            // select service and add on services table

            $('body').on('change', '#loyalty_program_modal_service_id', function(){

                var service_id = $(this).val();

                if (service_id) 
                {
                    $.ajax({
                        type: "get",
                        url: "{{route('admin.get-service-details')}}",
                        data: {service_id: service_id},
                        success: function (result) {
                            console.log(result);

                            if (result.service) 
                            {
                                var service_id = result.service.id;
                                var service_name = result.service.name;
                                var service_hours = result.service.hours;

                                // Check if the service ID already exists in the table
                                var existingRow = $("#loyalty_program_modal_sevices_table").find(`#loyalty_program_modal_sevices_table_tbody tr td.td_service_id_class input[name='td_service_id[]'][value='${service_id}']`).closest('tr');
                                
                                if (existingRow.length) 
                                {
                                    // If the service ID exists, increase the quantity
                                    var qtyInput = existingRow.find('.td_service_qty_class input[name="td_service_qty[]"]');
                                    var currentQty = parseInt(qtyInput.val()) || 0;
                                    qtyInput.val(currentQty + 1);
                                } 
                                else 
                                {
                                    // If the service ID does not exist, add a new row
                                    var html = `<tr>
                                                    <td class="td_service_id_class" hidden>
                                                        <input type="hidden" name="td_service_id[]" value="${service_id}">
                                                    </td>

                                                    <td class="td_service_name_class">${service_name}</td>

                                                    <td class="td_service_qty_class">
                                                        <input type="number" class="form-control" name="td_service_qty[]" min="1" value="1" style="width:80%;">
                                                    </td>

                                                    <td class="td_service_hours_class" data-service_loyalty_point="${result.service.loyalty_point}">${service_hours}</td>

                                                    <td class="td_delete_btn_class">
                                                        <a class="btn btn-danger btn-sm td_delete_btn" href="#" title="Delete">
                                                            <i class="fa fa-times"></i>
                                                        </a>
                                                    </td>
                                                </tr>`;

                                    $("#loyalty_program_modal_sevices_table").find("#loyalty_program_modal_sevices_table_tbody").append(html);
                                }

                                calculate_total_hours();
                                calculate_total_loyalty_points();
                            }
                        },
                        error: function (result) {
                            console.log(result);
                        }
                    });
                }
            
            });

            $('body').on('change', '#loyalty_program_modal_sevices_table .td_service_qty_class', function() {             

                calculate_total_hours();
                calculate_total_loyalty_points();

            });

            // delete service from services table

            $('body').on('click', '#loyalty_program_modal_sevices_table .td_delete_btn', function() {

                $(this).parents('tr').remove();

                calculate_total_hours();
                calculate_total_loyalty_points();

            });

            // apply

            $('body').on('submit', '#loyalty_program_modal_form', function(e){

                var category_id = $(this).find('#loyalty_program_modal_category_id').val();

                // console.log(category_id);

                e.preventDefault();

                $.ajax({
                    type: "post",
                    url: "{{route('admin.customer.loyalty-program.store')}}",
                    data: $(this).serialize(),
                    success: function (result) {
                        console.log(result);

                        if(result.status == "error")
                        {
                            $.each(result.error, function (key, value) { 

                                $.showToastr(value, 'error');                 
                                                        
                            });
                        }
                        else if(result.status == "success")
                        {
                            $.showToastr(result.message, 'success');

                            get_loyalty_program_progress_tracker(category_id);                        
                            
                            $("#loyalty_program_modal_sevices_table").find("#loyalty_program_modal_sevices_table_tbody").html("");
                            $("#loyalty_program_modal_total_hours").text("");

                            loyalty_program_modal_history_table.ajax.reload();                         
                        }
                        else
                        {
                            $.showToastr(result.message, 'error');
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });

            });

            // display history table

            var loyalty_program_modal_history_table = $('#loyalty_program_modal_history_table, #loyalty_program_history_table').DataTable({
                'responsive': true,
                'pageLength': 30,
                'searching': false,
                "lengthChange": false,
                ajax: {
                    url: "{{route('admin.customer.get-loyalty-program-history-table-data')}}",
                    type: 'GET',
                    data : function(data){
                        data.customer_id = {{$customer->id ?? ''}}
                    }
                },
            });

            // save loyalty point from loyalty program modal

            $('body').on('submit', '#loyalty_point_form', function(e){

                e.preventDefault();

                var customer_id = $(this).find("input[name='customer_id']").val();

                $.ajax({
                    type: "post",
                    url: '{{ route('admin.customers.storeLoyaltyPoints', ['customer' => ':id']) }}'.replace(':id', customer_id),
                    data: $(this).serialize() + "&id="+customer_id,
                    success: function (response) {
                        console.log(response);

                        if(response.status == "success")
                        {
                            $.showToastr(response.message, 'success');
                        }
                        else
                        {
                            $.showToastr(response.message, 'error');
                        }

                        $("#loyalty_program_onload_modal #loyalty_program_modal_loyalty_point").text(response.loyaltyPoints);
                        $('.loyaltyPointsDisplay_class').text('Available Coin:- ' + response.loyaltyPoints);

                    },
                    error: function (response) {
                        console.log(response);
                    }
                });

            });

            // loyalty program history data delete start

            $('body').on('click', '.loyalty_program_delete_btn', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                })
                .then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.customer.loyalty-program-history-table-data.delete') }}";

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, 'id': id},
                            success: function (response) {
                                if (response.status == "success") {
                                    // $.showToastr(response.message, 'success');
                                    loyalty_program_modal_history_table.ajax.reload();
                                } else {
                                    $.showToastr(response.message, 'error');
                                }
                            }
                        });
                    }
                });
            });

            // loyalty program history data delete end

            // *** loyalty program end ***

            // *** health questionnarie start ***

            $('body').on('click', '#no_to_all_btn', function(){

                $('input[type=radio][value="no"]').each(function() {
                    // Ensure only one radio is checked per group (name)
                    var name = $(this).attr('name');
                    $('input[name="' + name + '"][value="no"]').prop('checked', true);
                });

                // Optional: Clear all the *_details textarea fields if setting all to no
                $('textarea[name$="_details"]').val('');

            });

            $('body').on('submit', '#health_qstn_form', function(e){

                e.preventDefault();

                var formData = new FormData($(this)[0]);

                $.ajax({
                    type: "post",
                    url: "{{route('admin.customers.health-question.store')}}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response);
                       
                        if(response.status == "error")
                        {
                            $.each(response.error, function (key, value) { 

                                $.showToastr(value, 'error');                 
                                                        
                            });
                        }
                        else if(response.status == "success")
                        {
                            $.showToastr(response.message, 'success');
                            
                            window.location.href = window.location.pathname + '?tab=health#pc-9';
                            // location.reload();
                        }
                        else
                        {
                            $.showToastr(response.message, 'error');
                        }                           
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });

            });

            // *** health questionnarie end ***


            // *** loyalty shop product tab start ***

            $('#available_product_table, #used_product_table').dataTable({
                paging: false,
                pagelength: 30
            });

            // loyalty shop product use start

            $('body').on('click', '.loyalty_shop_use_btn', function (){

                var loyalty_shop_id = $(this).data('loyalty_shop_id');
                var loyalty_shop_redeem_id = $(this).data('loyalty_shop_redeem_id');
                
                $.ajax({
                    type: "post",
                    url: "{{route('admin.customers.loyalty-shop-product-used-store')}}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'loyalty_shop_id': loyalty_shop_id,
                        'loyalty_shop_redeem_id': loyalty_shop_redeem_id,
                        'customer_id': {{$customer->id ?? ''}}
                    },
                    success: function (result) {
                        console.log(result);

                        if(result.status == "success")
                        {
                            $.showToastr(result.message, 'success');
                            window.location.href = window.location.pathname + '?tab=voucher#pc-6';
                            // location.reload();
                        }
                        else
                        {
                            $.showToastr(result.message, 'error');
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });

            });

            // loyalty shop product use end

            // *** loyalty shop product tab end ***

        });
    </script>
@endpush
