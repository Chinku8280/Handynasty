@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                   
                    @permission('create_loyalty_shop')   
                        <div class="d-flex justify-content-center justify-content-md-end mb-3">
                            <a href="{{ route('admin.loyalty-shop.create') }}" class="btn btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                        </div>
                    @endpermission
                    
                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('app.image')</th>
                                    <th>@lang('app.title')</th>
                                    {{-- <th>Type</th> --}}
                                    <th>@lang('app.startOn')</th>
                                    <th>@lang('app.expireOn')</th>
                                    {{-- <th>@lang('app.discount')</th> --}}
                                    <th>Redeem Loyalty Coin</th>
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


    <div class="modal fade bs-modal-lg in" id="loyalty_shop_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">@lang('app.loyalty_shop')</h4>
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
@endsection

@push('footer-js')
    <script>
        $(document).ready(function () {
            
            var table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{!! route('admin.loyalty-shop.index') !!}",
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
                    // { data: 'loyalty_shop_type', name: 'loyalty_shop_type' },
                    { data: 'start_date_time', name: 'start_date_time' },
                    { data: 'end_date_time', name: 'end_date_time' },
                    // { data: 'discount', name: 'discount' },
                    { data: 'loyalty_point', name: 'loyalty_point' },
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


            $('body').on('click', '.view_loyalty_shop', function() {
                var id = $(this).data('row-id');
                var url = "{{ route('admin.loyalty-shop.show',':id') }}";
                url = url.replace(':id', id);
                $('#modelHeading').html('Show voucher');
                $.ajaxModal('#loyalty_shop_modal', url);
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
                        var url = "{{ route('admin.loyalty-shop.destroy',':id') }}";
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

        });
    </script>
@endpush
