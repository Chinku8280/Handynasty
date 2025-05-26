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

        .coin-box {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 15px 5px;
            justify-items: center;
            align-items: center;
            /* justify-content: space-around; */
            /* border: 1px solid; */
        }

        .coin-box a {
            padding: 5px;
            border: 1px solid #380814;
            border-radius: 50px;     
            width: 40px;
            height: 40px;      
        }
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
                                                <span class="title">Branch:</span>
                                                <span
                                                    class="text">{{ \App\Location::find($customer->branch_id)->name ?? '' }}</span>
                                            </li>
                                            <li>
                                                <span class="title">Date of Birth:</span>
                                                <span class="text">{{ $customer->dob }}</span>
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
                                                <a href="javascript:;" class="btn btn-outline-light delete-row"
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
                                            <span class="d-none d-sm-block">Loyality
                                                Points</span>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-toggle="tab" href="#pc-8" role="tab"
                                            aria-selected="false" tabindex="-1">
                                            <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                            <span class="d-none d-sm-block">Loyality
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
        $(document).ready(function() {
            // When the link is clicked, open the modal
            $('.btn-outline-light').click(function() {
                $('#customerModal').modal('show');
            });
        });

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
                    $('.loyaltyPointsDisplay_class').text('Available Point:- ' + response.loyaltyPoints);

                    // Add a new row to the loyalty Coins table with the global serial number
                    let newRow = '<tr>' +
                        '<td>' + serialNumber + '</td>' +
                        '<td>' + response.rowData.loyaltyPoints + '</td>' +
                        '<td>' + response.rowData.date + '</td>' +
                        '<td>' + response.rowData.time + '</td>' +
                        '<td><span class="badge bg-success">Active</span></td>' +
                        '</tr>';

                    $('#loyaltyPointsTableBody').prepend(newRow);

                    // Increment the serial number for the next row
                    serialNumber++;
                    $('#loyalty_points').val('');
                    $('#loyality_points').modal('hide');
                },
                error: function(error) {
                    alert('Error saving loyalty Coins');
                }
            });
        }

        function openAssignPackageModal() {
            $('#assign_package').modal('show');
        }

        // loyalty program start

        // function get_loyalty_program_total_hours()
        // {
        //     var full_flag = 1;
        //     var half_flag = parseFloat(parseFloat(full_flag) / 2);

        //     $.ajax({
        //         type: "get",
        //         url: "{{route('admin.customer.loyalty-program.get-total-hours')}}",
        //         data: {'customer_id': {{$customer->id ?? ''}}},
        //         success: function (result) {
        //             console.log(result);

        //             if(result.db_LoyaltyProgramHour)
        //             {          
        //                 $("#coin_box_group").html("");

        //                 var total_hours = parseFloat(result.db_LoyaltyProgramHour.total_hours);

        //                 var mod = parseFloat(total_hours % 1);

        //                 var rest_hours = parseFloat(total_hours - mod);
                        
        //                 var temp=0;

        //                 for(var i=0; i<rest_hours; i++)
        //                 {
        //                     var html = `<a href="#" style="background-color: #380814;"></a>`;

        //                     $("#coin_box_group").append(html);
        //                 }

        //                 if(mod == 0.5)
        //                 {
        //                     temp=1;
                            
        //                     var html = `<a href="#" style="background: linear-gradient(90deg, #380814 50%, white 50%);"></a>`;

        //                     $("#coin_box_group").append(html);
        //                 }

        //                 for(var i=0; i<parseFloat(20-rest_hours-temp); i++)
        //                 {
        //                     var html = `<a href="#"></a>`;

        //                     $("#coin_box_group").append(html);
        //                 }
        //             }
        //         },
        //         error: function (result) {
        //             console.log(result);
        //         }
        //     });
        // }

        // function get_loyalty_program_total_hours() 
        // {
        //     $.ajax({
        //         type: "get",
        //         url: "{{route('admin.customer.loyalty-program.get-total-hours')}}",
        //         data: {'customer_id': {{$customer->id ?? ''}}},
        //         success: function (result) {
        //             var total_slots = 20;

        //             if (result.db_LoyaltyProgramHour) 
        //             {
        //                 $("#coin_box_group").html("");

        //                 var total_hours = parseFloat(result.db_LoyaltyProgramHour.total_hours);
        //                 var hours_per_stamp = parseFloat(result.hours_per_stamp); // Get hours per slot from backend                      

        //                 // Calculate how many slots are fully and partially filled
        //                 var full_slots = Math.floor(total_hours / hours_per_stamp);
        //                 var remaining_hours = total_hours % hours_per_stamp;
        //                 var half_slot = (remaining_hours > 0) ? (remaining_hours / hours_per_stamp) : 0;

        //                 // Append full filled slots
        //                 for (var i = 0; i < full_slots; i++) 
        //                 {
        //                     var html = `<a href="#" style="background-color: #380814;"></a>`;
        //                     $("#coin_box_group").append(html);
        //                 }

        //                 // Append half-filled slot if necessary
        //                 if (half_slot > 0) 
        //                 {
        //                     // var html = `<a href="#" style="background: linear-gradient(90deg, #380814 ${half_slot * 100}%, white ${100 - half_slot * 100}%);"></a>`;
        //                     var html = `<a href="#" style="background: linear-gradient(90deg, #380814 50%, white 50%);"></a>`;
        //                     $("#coin_box_group").append(html);
        //                 }

        //                 // Append empty slots for the rest
        //                 for (var i = full_slots + (half_slot > 0 ? 1 : 0); i < total_slots; i++) 
        //                 {
        //                     var html = `<a href="#"></a>`;
        //                     $("#coin_box_group").append(html);
        //                 }
        //             }
        //             else
        //             {
        //                 $("#coin_box_group").html("");

        //                 for (var i = 0; i < total_slots; i++) 
        //                 {
        //                     var html = `<a href="#"></a>`;
        //                     $("#coin_box_group").append(html);
        //                 }
        //             }
        //         },
        //         error: function (result) {
        //             console.log(result);
        //         }
        //     });
        // }

        function get_loyalty_program_total_hours() 
        {
            $.ajax({
                type: "get",
                url: "{{route('admin.customer.loyalty-program.get-total-hours')}}",
                data: {'customer_id': {{$customer->id ?? ''}}},
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
                                    accumulated_hours += remaining_hours_in_slot;
                                    hours_in_entry -= remaining_hours_in_slot;

                                    // Add the current history ID to the array for this slot
                                    if (!stamp_ids[current_slot]) {
                                        stamp_ids[current_slot] = [];
                                    }
                                    stamp_ids[current_slot].push(history_id);

                                    // Append fully filled slot
                                    var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}" style="background-color: #380814;"></a>`;
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
                            var partial_fill = (accumulated_hours / hours_per_stamp) * 100;
                            var html = `<a href="#" data-id="${stamp_ids[current_slot].join(',')}" style="background: linear-gradient(90deg, #380814 ${partial_fill}%, white ${100 - partial_fill}%);"></a>`;
                            $("#coin_box_group").append(html);
                            current_slot++;
                        }

                        // Fill the remaining empty slots, if any
                        for (var j = current_slot; j < total_slots; j++) 
                        {
                            var html = `<a href="#" data-id=""></a>`;
                            $("#coin_box_group").append(html);
                        }
                    }
                    else
                    {
                        // If no data, append empty slots
                        $("#coin_box_group").html("");

                        for (var i = 0; i < total_slots; i++) 
                        {
                            var html = `<a href="#" data-id=""></a>`;
                            $("#coin_box_group").append(html);
                        }
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }   
        
        function add_service_detail(service_id)
        {
            $.ajax({
                type: "get",
                url: "{{route('admin.get-service-details')}}",
                data: {service_id: service_id},
                success: function (result) {
                    console.log(result);

                    if(result.service)
                    {
                        var service_id = result.service.id;
                        var service_name = result.service.name;
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }

        // loyalty program end

        $(document).ready(function () {
            
            // coupon use start

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

            // voucher use start

            $('body').on('click', '.voucher_use_btn', function (){

                var voucher_id = $(this).data('voucher_id');

                $.ajax({
                    type: "post",
                    url: "{{route('admin.customers.voucher-used-store')}}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'voucher_id': voucher_id,
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

            // loyalty program start

            $('body').on('click', '#loyalty_program_add_btn', function() {
                get_loyalty_program_total_hours();
                $('#loyalty_program_onload_modal').modal('show');
            });

            $('body').on('change', '#loyalty_program_modal_category_id', function(){

                var category_id = $(this).val();

                $.ajax({
                    type: "get",
                    url: "{{route('admin.get-services-by-category')}}",
                    data: {category_id: category_id},
                    success: function (result) {
                        console.log(result);

                        if(result.services.length > 0)
                        {
                            $("#loyalty_program_modal_service_id").html("");

                            $.each(result.services, function (key, value) { 
                                var html = `<option value="${value.id}">${value.name}</option>`;
                            
                                $("#loyalty_program_modal_service_id").append(html);
                            });                       
                        }
                        else
                        {
                            $("#loyalty_program_modal_service_id").html("<option value=''>Select</option>");
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    },
                });

            });

            $('body').on('change', '#loyalty_program_modal_service_id', function(){

                var service_id = $(this).val();

                add_service_detail(service_id);

                // $.ajax({
                //     type: "get",
                //     url: "{{route('admin.get-total-hours-by-services')}}",
                //     data: {services_id: services_id},
                //     success: function (result) {
                //         console.log(result);

                //         $("#loyalty_program_modal_total_hours").text("Total Hours required : " + result.totalHours);
                //     },
                //     error: function (result) {
                //         console.log(result);
                //     },
                // });

            });

            $('body').on('click', '#loyalty_program_modal_apply_btn', function(){

                var time = $("#loyalty_program_modal_time").val();
                var date = $("#loyalty_program_modal_date").val();
                var outlet_id = $("#loyalty_program_modal_outlet_id").val();
                var category_id = $("#loyalty_program_modal_category_id").val();
                var services_id = $("#loyalty_program_modal_service_id").val();

                $.ajax({
                    type: "post",
                    url: "{{route('admin.customer.loyalty-program.store')}}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'date': date,
                        'time': time,
                        'outlet_id': outlet_id,
                        'category_id': category_id,
                        'services_id': services_id,
                        'customer_id': {{$customer->id ?? ''}}
                    },
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

                            get_loyalty_program_total_hours();      
                            
                            loyalty_program_modal_history_table.ajax.reload();
                            // location.reload();
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });

            });

            var loyalty_program_modal_history_table = $('#loyalty_program_modal_history_table, #loyalty_program_history_table').DataTable({
                'responsive': true,
                'pageLength': 10,
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

            $('body').on('click', '#coin_box_group a', function(){

                var id = $(this).data('id');

                console.log(id);

            });

            // loyalty program end

        });
    </script>
@endpush
