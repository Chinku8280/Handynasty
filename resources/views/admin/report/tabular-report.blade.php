<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="card">
                    <div class="card-header d-flex p-3">
                            <div class="container-fluid">
                                <form id="formFilter" action="#">
                                <div class="row">

                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">@lang('report.bookingBetweenDate')</label>
                                        <div class="row">
                                            <div class="col-md-6 col-xs-12">
                                                <div class="calendar">
                                                <input type="text" class="form-control " id="from_date" placeholder="@lang('app.choose') @lang('report.fromDate')" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <div class="calendar">
                                                    <input type="text" class="form-control " id="to_date" placeholder="@lang('app.choose') @lang('report.toDate')" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">@lang('app.location')</label>
                                        <select style="width:100%" selected name="location" id="location" class="form-control select2">
                                            <option value="">@lang('app.filter') @lang('app.location'): @lang('app.viewAll')</option>
                                            @foreach ($locations as $location)
                                                <option value="{{$location->id}}">{{$location->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">@lang('app.service')</label>
                                        <select name="service_name" id="service_name" class="form-control select2" style="width:100%">
                                            <option selected value="">@lang('app.filter') @lang('app.service'): @lang('app.viewAll')</option>
                                            @foreach ($services as $service)
                                                <option value="{{$service->name}}">{{$service->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">@lang('app.customer')</label>
                                        <select name="customer_id" id="customer_id" class="form-control select2" style="width:100%">
                                            <option selected value="">@lang('modules.booking.selectCustomer'): @lang('app.viewAll')</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ ucwords($customer->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">@lang('app.employee')</label>
                                        <select name="employee_id" id="employee_id" class="form-control select2" style="width:100%">
                                            <option selected value="">@lang('app.filter') @lang('app.employee'): @lang('app.viewAll')</option>
                                            @foreach ($staffs as $staff)
                                                <option value="{{$staff->id}}">{{$staff->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">@lang('app.booking') @lang('app.type')</label>
                                        <select style="width:100%" selected name="booking_type" id="booking_type" class="form-control select2">
                                            <option value="">@lang('app.filter') @lang('app.booking'): @lang('app.viewAll')</option>
                                            <option value="booking">@lang('app.service')</option>
                                            <option value="deal">@lang('app.deal')</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">@lang('report.bookingStatus')</label>
                                        <select style="width:100%" selected name="booking_status" id="booking_status" class="form-control select2">
                                            <option value="">@lang('app.filter') @lang('app.status'): @lang('app.viewAll')</option>
                                            <option value="completed">@lang('app.completed')</option>
                                            <option value="pending">@lang('app.pending')</option>
                                            <option value="approved">@lang('app.approved')</option>
                                            <option value="in progress">@lang('app.in progress')</option>
                                            <option value="canceled">@lang('app.canceled')</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">@lang('app.payment') @lang('app.status')</label>
                                        <select style="width:100%" selected name="payment" id="payment" class="form-control select2">
                                            <option value="">@lang('app.filter') @lang('app.payment'): @lang('app.viewAll')</option>
                                                <option value="completed">Paid</option>
                                                <option value="pending">Pending</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 text-right">
                                    <button type="button" id="filter" class="btn btn-primary"><i class="fa fa-filter"></i> @lang('app.filter')</button>
                                    <button type="reset" id="resetbtn" class="btn btn-danger"><i class="fa fa-times"></i> @lang('app.reset')</button>
                                </div>
                            </div>
        <input type="hidden" name="tabular_startDate" id="tabular_startDate">
        <input type="hidden" name="tabular_endDate" id="tabular_endDate">
                            </form>
                            </div>


                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div class="table-responsive">
                                    <table id="tabularTable" class="table" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('app.service') #</th>
                                                <th>@lang('app.customer')</th>
                                                <th>@lang('report.bookingDate')</th>
                                                <th>@lang('report.bookingTime')</th>
                                                <th>@lang('app.serviceName')</th>
                                                <th>@lang('app.employee')</th>
                                                <th>@lang('app.status')</th>
                                                <th>@lang('app.amount')</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="7" style="text-align:right">Total:</th>
                                                <th id="total"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- ./card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
    </div>
</div>

@push('footer-js')
<script>
    $(function()
    {
        var table = $('#tabularTable').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csvHtml5', text: '@lang("app.exportCSV")' }
            ],
            ajax: {
            url: "{!! route('admin.reports.tabularTable') !!}",
            data: function (d) {
                    d.from_date       = $('#tabular_startDate').val(),
                    d.to_date         = $('#tabular_endDate').val(),
                    d.customer_id     = $('#customer_id').val(),
                    d.service_name    = $('#service_name').val(),
                    d.employee_id     = $('#employee_id').val(),
                    d.booking_status  = $('#booking_status').val(),
                    d.booking_type    = $('#booking_type').val(),
                    d.location        = $('#location').val(),
                    d.payment         = $('#payment').val()
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                { data: 'customer_name', name: 'customer_name' },
                { data: 'booking_date', name: 'booking_date' },
                { data: 'booking_time', name: 'booking_time' },
                { data: 'service_name', name: 'service_name' },
                { data: 'employee_name', name: 'employee_name' },
                { data: 'booking_status', name: 'booking_status' },
                { data: 'amount', name: 'amount' }
            ],
            "drawCallback": function( settings ) {
                $('#total').html(this.api().ajax.json().sums)
            }
        });

        $('#filter').click(function()
        {
            if(($("#from_date").val()!='' && $('#to_date').val()=='') || ($("#from_date").val()=='' && $('#to_date').val()!='')){
                if($("#from_date").val()==''){
                    $('#from_date').focus();
                }
                else{
                    $('#to_date').focus();
                }
                return toastr.error('@lang("report.invalidDateSelection")');
            }
            table.draw();
        });

        $('#resetbtn').click(function(){
            $("#formFilter").trigger("reset");
            $("#customer_id").val('').trigger('change');
            $("#service_name").val('').trigger('change');
            $("#employee_id").val('').trigger('change');
            $("#booking_status").val('').trigger('change');
            $("#booking_type").val('').trigger('change');
            $("#location").val('').trigger('change');
            $("#payment").val('').trigger('change');
            $("#tabular_startDate").val('').trigger('change');
            $("#tabular_endDate").val('').trigger('change');
            table.draw();
        });

        $('#from_date').datetimepicker({
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
            },
            useCurrent: false,
        }).on("dp.change", function (e) {
            $('#tabular_startDate').val(moment(e.date).format('YYYY-MM-DD'));
        });
        
        $('#to_date').datetimepicker({
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
            },
            useCurrent: false,
        }).on("dp.change", function (e) {
            $('#tabular_endDate').val(moment(e.date).format('YYYY-MM-DD'));
        });

    });
</script>
@endpush
