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
                    {{-- @permission('create_business_service')
                    <div class="d-flex justify-content-center justify-content-md-end mb-3">                                  
                        <a href="{{ route('admin.business-services.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>                                   
                    </div>
                    @endpermission --}}

                    <div class="row justify-content-end mb-3">                                       
                        <div class="col-3">
                            <div class="form-group">
                                <select name="category_id" id="category_id" class="form-control form-control-lg">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if (!empty($service) && $service->category->id == $category->id)
                                            selected
                                        @endif>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-auto">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="filter_btn">Filter</button>
                            </div>
                        </div>

                        @permission('create_business_service')
                            <div class="col-auto">
                                <a href="{{ route('admin.business-services.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>                                   
                            </div>
                        @endpermission
                    </div>

                    <form action="{{route('admin.business-services.sort-update')}}" method="POST">
                        @csrf

                        <div class="table-responsive">
                            <table id="myTable" class="table w-100">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <th>#</th>
                                        <th>@lang('app.image')</th>
                                        <th>@lang('app.name')</th>
                                        {{-- <th>@lang('app.location')</th> --}}
                                        {{-- <th>@lang('app.outlet')</th> --}}
                                        <th>@lang('app.category')</th>
                                        <th>@lang('app.price')</th>
                                        <th>Time required</th>
                                        <th>@lang('app.assign') Therapist</th>
                                        <th>@lang('app.status')</th>
                                        <th>@lang('app.action')</th>
                                    </tr>
                                </thead>
                                <tbody id="service_tbody">

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

            var table = $('#myTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                // ajax: '{!! route('admin.business-services.index') !!}',
                ajax: {
                    url: '{!! route('admin.business-services.index') !!}',
                    data: function (data) {
                        data.category_id = $("#category_id").find(':selected').val(); // Pass category_id as parameter
                    }
                },
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[0, 'DESC']],
                columns: [
                    { data: 'hidden_input', name: 'hidden_input', className:"hidden-category-td", searchable: false, orderable: false },
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'name', name: 'name' },
                    // { data: 'location_id', name: 'location_id' },
                    // { data: 'outlet_id', name: 'outlet_id' },
                    { data: 'category_id', name: 'category_id' },
                    { data: 'price', name: 'price' },
                    { data: 'time', name: 'time' },
                    { data: 'users', name: 'users' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });

            new $.fn.dataTable.FixedHeader( table );

            $('#myTable').on('draw.dt', function () {
                $("#service_tbody").sortable({
                    cursor: 'row-resize',
                    placeholder: 'ui-state-highlight',
                    opacity: '0.55',
                    items: '.ui-sortable-handle',                    
                }).disableSelection();
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
                        var url = "{{ route('admin.business-services.destroy',':id') }}";
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

            $('body').on('click', '.duplicate-row', function () {
                var id = $(this).data('row-id');

                var url = "{{ route('admin.business-services.create').'?service_id=:id' }}";
                url = url.replace(':id', id);
                location.href = url;
            })

            // filter start

            $("body").on('click', '#filter_btn', function(){

                table.ajax.reload(); 

            });

            // filter end

            // send notification start

            $('body').on('click', '.send_notification_btn', function(){
                
                $("#send_notification_form")[0].reset();

                var id = $(this).data('row-id');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'service_id',
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
                    url: "{{route('admin.business-services.send-notification')}}",
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
