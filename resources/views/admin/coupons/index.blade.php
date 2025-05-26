@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('create_coupon'))
                        <div class="d-flex justify-content-center justify-content-md-end mb-3">
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.coupon') @lang('app.code')</th>
                                {{-- <th>@lang('app.code')</th> --}}
                                <th>@lang('app.startOn')</th>
                                <th>@lang('app.expireOn')</th>
                                <th>@lang('app.amountOrPercent')</th>
                                <th>@lang('app.status')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--coupon detail Modal--}}
    <div class="modal fade bs-modal-lg in" id="coupon-detail-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">@lang('app.coupon')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.close')</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--coupon detail Modal Ends--}}

    @include('admin.partials.notification_modal')
@endsection

@push('footer-js')
    <script>
        $(document).ready(function() {
            var table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.coupons.data') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                    $('.role_id').select2({
                        width: '100%'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'title', name: 'title' },
                    { data: 'start_date_time', name: 'start_date_time' },
                    { data: 'end_date_time', name: 'end_date_time' },
                    { data: 'amount', name: 'amount' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('click', '.view-coupon', function() {
                var id = $(this).data('row-id');
                var url = "{{ route('admin.coupons.show',':id') }}";
                url = url.replace(':id', id);
                $('#modelHeading').html('Show Coupon');
                $.ajaxModal('#coupon-detail-modal', url);
            });

            $('body').on('click', '.delete-row', function(){
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
                            var url = "{{ route('admin.coupons.destroy',':id') }}";
                            url = url.replace(':id', id);

                            var token = "{{ csrf_token() }}";

                            $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {'_token': token, '_method': 'DELETE'},
                                success: function (response) {
                                    if (response.status == "success") {
                                        $.unblockUI();
                                        // swal("Deleted!", response.message, "success");
                                        table._fnDraw();
                                    }
                                }
                            });
                        }
                    });
            });
        } );

        $(document).ready(function() {
            // $('#myTable').on('click', '.send-notification', function() {
            //     var couponId = $(this).data('row-id');

            //     $.ajax({
            //         url: '{{ route("admin.coupons.sendNotification") }}',
            //         method: 'POST',
            //         data: { coupon_id: couponId },
            //         headers: {
            //             "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            //         },
            //         success: function(response) {
            //             console.log(response);                        
            //             alert('Notification Sent Successfully.');
            //         },
            //         error: function(error) {
            //             alert('Error sending notification.');
            //         }
            //     });
            // });


            // send notification start

            $('body').on('click', '.send_notification_btn', function(){
                
                $("#send_notification_form")[0].reset();

                var id = $(this).data('row-id');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'coupon_id',
                    value: id
                }).appendTo('#send_notification_form');

                $("#send_notification_modal").modal('show');
                
            });

            $('#send_notification_modal').on('hidden.bs.modal', function () {
                $("#send_notification_form")[0].reset();
            });


            $('body').on('submit', '#send_notification_form', function(e){

                e.preventDefault();
                
                $.ajax({
                    type: "post",
                    url: "{{route('admin.coupons.send-notification')}}",
                    data: $(this).serialize(),
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

                            $("#send_notification_form")[0].reset();
                            $("#send_notification_modal").modal('hide');
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

            // send notification end
        });
    </script>
@endpush
