@extends('layouts.master')

@push('head-css')
    
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="row justify-content-end mb-3">  
                        @permission('create_product')                                                                        
                            <div class="col-auto">
                                <a href="{{ route('admin.products.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>                                   
                            </div>
                        @endpermission
                    </div>

                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('app.image')</th>
                                    <th>@lang('app.name')</th>
                                    <th>@lang('app.price')</th>
                                    <th>@lang('app.status')</th>
                                    <th>@lang('app.action')</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{--product detail Modal--}}
    <div class="modal fade bs-modal-lg in" id="product-detail-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Product</h4>
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
    {{--product detail Modal Ends--}}
@endsection

@push('footer-js')
    <script>
        $(document).ready(function () {

            var table = $('#myTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                // ajax: '{!! route('admin.business-services.index') !!}',
                ajax: {
                    url: '{!! route('admin.products.index') !!}',
                },
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[0, 'DESC']],
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'price', name: 'price' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });

            new $.fn.dataTable.FixedHeader( table );
            
            // view product
            $('body').on('click', '.view_product', function() {
                var id = $(this).data('row-id');
                var url = "{{ route('admin.products.show',':id') }}";
                url = url.replace(':id', id);
                $('#modelHeading').html('Show product');
                $.ajaxModal('#product-detail-modal', url);
            });

            // delete product
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
                        var url = "{{ route('admin.products.destroy',':id') }}";
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
                                    // table._fnDraw();
                                    table.ajax.reload(); 
                                }
                            }
                        });
                    }
                });
            });

        });
        

    </script>
@endpush
