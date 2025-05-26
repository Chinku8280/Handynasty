@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content p-3 text-muted">   
                            
                            @permission('create_faqs')    
                                <div class="d-flex justify-content-center justify-content-md-end mb-3">
                                    <a href="{{ route('admin.faq.create') }}" class="btn btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                                </div>
                            @endpermission
                            
                            <div class="table-responsive">
                                <table id="myTable" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>           
                                            <th>@lang('app.faq')</th>
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
        </div>
    </div>
</div>

{{-- Show FAQ Detail Modal --}}
<div class="modal fade bs-modal-lg in" id="faq-detail-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">@lang('app.faq')</h4>
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
{{-- End Modal --}}

@endsection

@push('footer-js')
<script>
    $(document).ready(function() {
            var table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.faq.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex'}, 
                    { data: 'question', name: 'question' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '15%' }
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

            $('body').on('click', '.view-faq', function() {
                var id = $(this).data('row-id');
                var url = "{{ route('admin.faq.show',':id') }}";
                url = url.replace(':id', id);
                $('#modelHeading').html('Show FAQ Detail');
                $.ajaxModal('#faq-detail-modal', url);
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
                            var url = "{{ route('admin.faq.destroy',':id') }}";
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
</script>
@endpush