@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('create_voucher'))
                        <div class="d-flex justify-content-center justify-content-md-end mb-3">
                            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.image')</th>
                                <th>@lang('app.title')</th>
                                <th>@lang('app.startOn')</th>
                                <th>@lang('app.expireOn')</th>
                                <th>@lang('app.discount')</th>
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

    {{--voucher detail Modal--}}
    <div class="modal fade bs-modal-lg in" id="voucher-detail-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">@lang('app.voucher')</h4>
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

    @include('admin.partials.notification_modal')
@endsection

@push('footer-js')
    <script>
        $(document).ready(function() {
            var table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.vouchers.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'title', name: 'title' },
                    { data: 'start_date_time', name: 'start_date_time' },
                    { data: 'end_date_time', name: 'end_date_time' },
                    { data: 'percentage', name: 'percentage' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '16%' }
                ],
                // 'columnDefs': [
                //     {
                //         "targets": 7, // your case first column
                //         "className": "text-left",
                //         "width": "15%"
                //     },
                // ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('click', '.view-voucher', function() {
                var id = $(this).data('row-id');
                var url = "{{ route('admin.vouchers.show',':id') }}";
                url = url.replace(':id', id);
                $('#modelHeading').html('Show voucher');
                $.ajaxModal('#voucher-detail-modal', url);
            });

            $('body').on('click', '.delete-row', function(){
                var id = $(this).data('row-id');
                var voucher_dp_id = $(this).data('row-voucher_dp_id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            var url = "{{ route('admin.vouchers.destroy',':voucher_dp_id') }}";
                            url = url.replace(':voucher_dp_id', voucher_dp_id);

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
            //     var voucherId = $(this).data('row-id');

            //     $.ajax({
            //         url: '{{ route("admin.vouchers.sendNotification") }}',
            //         method: 'POST',
            //         data: { voucher_id: voucherId },
            //         headers: {
            //             "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            //         },
            //         success: function(response) {
            //             // console.log(response);
            //             alert('Notification sent successfully!');
            //         },
            //         error: function(error) {
            //             // console.error(error);
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
                    name: 'voucher_id',
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
                    url: "{{route('admin.vouchers.send-notification')}}",
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
