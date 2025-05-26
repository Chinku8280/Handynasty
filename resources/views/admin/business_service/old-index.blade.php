@extends('layouts.master')

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

                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('app.image')</th>
                                    <th>@lang('app.name')</th>
                                    {{-- <th>@lang('app.location')</th> --}}
                                    {{-- <th>@lang('app.outlet')</th> --}}
                                    <th>@lang('app.category')</th>
                                    <th>@lang('app.price')</th>
                                    <th>@lang('app.assign') Therapist</th>
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
@endsection

@push('footer-js')
    <script>
        $(document).ready(function() {
            var table = $('#myTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
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
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'name', name: 'name' },
                    // { data: 'location_id', name: 'location_id' },
                    // { data: 'outlet_id', name: 'outlet_id' },
                    { data: 'category_id', name: 'category_id' },
                    { data: 'price', name: 'price' },
                    { data: 'users', name: 'users' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });

            new $.fn.dataTable.FixedHeader( table );

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
        } );
    </script>
@endpush
