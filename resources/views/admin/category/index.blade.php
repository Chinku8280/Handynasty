@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.css">
    <style>
        .ui-sortable-handle {
            cursor: move;
        }
        .ui-state-highlight {
            height: 2.5em;
            line-height: 1.2em;
            background-color: #f0f0f0;
            border: 1px dashed #ccc;
        }

        #myTable .hidden-category-td {
            display: none;  /* Hide the first column which contains the hidden input */
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @permission('create_category')
                    <div class="d-flex justify-content-center justify-content-md-end mb-3">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                    </div>
                    @endpermission

                    <form action="{{route('admin.categories.sort-update')}}" method="POST">
                        @csrf

                        <div class="table-responsive">
                            <table id="myTable" class="table w-100">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>#</th>
                                        <th>@lang('app.image')</th>
                                        <th>@lang('app.name')</th>
                                        <th>@lang('app.status')</th>
                                        <th>@lang('app.action')</th>
                                    </tr>
                                </thead>
                                <tbody id="cat_tbody">

                                </tbody>
                            </table>
                        </div>

                        <div class="form-group text-right mt-4">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.notification_modal')

@endsection

@push('footer-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {

            @if(session('success'))
                $.showToastr("{{ session('success') }}", 'success');
            @endif

            var table = $('#myTable').dataTable({
                responsive: true,
                // processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: '{!! route('admin.categories.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'hidden_input', name: 'hidden_input', className:"hidden-category-td", searchable: false, orderable: false },
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'name', name: 'name' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('#myTable').on('draw.dt', function () {
                $("#cat_tbody").sortable({
                    cursor: 'row-resize',
                    placeholder: 'ui-state-highlight',
                    opacity: '0.55',
                    items: '.ui-sortable-handle',                    
                }).disableSelection();
            });


            // delete row start

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
                            var url = "{{ route('admin.categories.destroy',':id') }}";
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

            // delete row end

            // send notification start

            $('body').on('click', '.send_notification_btn', function(){
                
                $("#send_notification_form")[0].reset();

                var id = $(this).data('row-id');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'category_id',
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
                    url: "{{route('admin.categories.send-notification')}}",
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
