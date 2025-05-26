@foreach ($outlet as $item)
    <div class="row">
        <div class="col-md-12">
            <h4 class="text-uppercase mb-4">
                <span>Outlet : {{$item->outlet_name}}</span>
            </h4>

            <h5 class="text-uppercase mb-4">
                @lang('modules.dashboard.totalBooking'): <span id="total-booking">0</span>
            </h5>
        </div>

        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box link-stats" onclick="location.href='{{ route('admin.bookings.index', 'status=completed') }}'">
                <span class="info-box-icon bg-success"><i class="fa fa-calendar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('modules.dashboard.completedBooking')</span>
                    <span class="info-box-number" id="completed-booking">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box link-stats" onclick="location.href='{{ route('admin.bookings.index', 'status=pending') }}'">
                <span class="info-box-icon bg-warning"><i class="fa fa-calendar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('modules.dashboard.pendingBooking')</span>
                    <span class="info-box-number" id="pending-booking">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box link-stats" onclick="location.href='{{ route('admin.bookings.index', 'status=approved') }}'">
                <span class="info-box-icon bg-info"><i class="fa fa-calendar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('modules.dashboard.approvedBooking')</span>
                    <span class="info-box-number" id="approved-booking">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box link-stats" onclick="location.href='{{ route('admin.bookings.index', 'status=in progress') }}'">
                <span class="info-box-icon bg-primary"><i class="fa fa-calendar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('modules.dashboard.inProgressBooking')</span>
                    <span class="info-box-number" id="in-progress-booking">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box link-stats" onclick="location.href='{{ route('admin.bookings.index', 'status=canceled') }}'">
                <span class="info-box-icon bg-danger"><i class="fa fa-calendar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('modules.dashboard.canceledBooking')</span>
                    <span class="info-box-number" id="canceled-booking">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box link-stats" onclick="location.href='{{ route('admin.bookings.index') }}'">
                <span class="info-box-icon bg-secondary"><i class="fa fa-building"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('modules.dashboard.walkInBookings')</span>
                    <span class="info-box-number" id="offline-booking">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-12">
            <div class="info-box link-stats" onclick="location.href='{{ route('admin.bookings.index') }}'">
                <span class="info-box-icon bg-info"><i class="fa fa-internet-explorer"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">@lang('modules.dashboard.onlineBookings')</span>
                    <span class="info-box-number" id="online-booking">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        @if($user->is_admin)
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box link-stats" onclick="location.href='{{ route('admin.customers.index') }}'">
                    <span class="info-box-icon bg-dark-gradient"><i class="fa fa-users"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">@lang('modules.dashboard.totalCustomers')</span>
                        <span class="info-box-number" id="total-customers">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success">{{ $settings->currency->currency_symbol }}</span>

                    <div class="info-box-content">
                        <span class="info-box-text">@lang('modules.dashboard.totalEarning')</span>
                        <span class="info-box-number" id="total-earning">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        @endif
    </div>
@endforeach