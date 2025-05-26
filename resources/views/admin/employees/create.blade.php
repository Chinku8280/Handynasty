@extends('layouts.master')

@section('content')
    <style>
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #999;
        }

        .select2-dropdown .select2-search__field:focus,
        .select2-search--inline .select2-search__field:focus {
            border: 0px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            margin: 0 13px;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #cfd1da;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__clear {
            cursor: pointer;
            float: right;
            font-weight: bold;
            margin-top: 8px;
            margin-right: 15px;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.add') @lang('menu.employee')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.name')</label>
                                    <input type="text" class="form-control form-control-lg" name="name" value=""
                                        autocomplete="off">
                                </div>

                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.email')</label>
                                    <input type="email" class="form-control form-control-lg" name="email" value=""
                                        autocomplete="off">
                                </div>

                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.password')</label>
                                    <input type="password" class="form-control form-control-lg" name="password">
                                    <span class="help-block">@lang('messages.leaveBlank')</span>
                                </div>

                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.mobile')</label>
                                    <div class="form-row">
                                        <div class="col-md-2 mb-2">
                                            <select name="calling_code" id="calling_code" class="form-control select2">
                                                @foreach ($calling_codes as $code => $value)
                                                    <option value="{{ $value['dial_code'] }}">
                                                        {{ $value['dial_code'] . ' - ' . $value['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="mobile" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="form-group">
                                    <label>@lang('app.employeeGroup')</label>
                                    <div class="input-group">
                                        <select name="group_id" id="group_id" class="form-control form-control-lg select2">
                                            <option value="0">@lang('app.selectEmployeeGroup')</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-success" id="add-group" type="button"><i
                                                    class="fa fa-plus"></i> @lang('app.add')</button>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="form-group">
                                    <label>@lang('app.assignRole')</label>
                                    <select name="role_id" id="role_id" class="form-control form-control-lg select2">
                                        <option value="0" disabled>@lang('app.selectEmployeeRole')</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" id="assign_service_group">
                                    <label>@lang('app.assignServices')</label>
                                    <select name="business_service_id[]" id="business_service_id"
                                        class="form-control form-control-lg select2" multiple="multiple"
                                        style="width: 100%">
                                        <option value="0" disabled>@lang('app.selectServices')</option>
                                        @foreach ($business_services as $business_service)
                                            <option value="{{ $business_service->id }}">{{ $business_service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>@lang('app.outlet')</label>
                                    <select name="outlet_id[]" id="outlet_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple">
                                        <option value="0">All Outlets</option>
                                        @foreach ($outlets as $item)
                                            <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>POS @lang('app.outlet')</label>
                                    <select name="pos_outlet_id[]" id="pos_outlet_id"
                                        class="form-control form-control-lg select2" style="width: 100%" multiple="multiple">
                                        <option value="0">All Outlets</option>
                                        @foreach ($outlets as $item)
                                            <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="image_display_in_app">Image Display In Mobile Application</label>
                                    <select name="image_display_in_app" class="form-control">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg"
                                        data-default-file="{{ asset('img/default-avatar-user.png') }}"
                                        class="form-control" />
                                </div>

                                <h6 class="text-danger">** Recommended image resolution: 225 * 225</h6>

                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                            class="fa fa-check"></i> @lang('app.save')</button>
                                </div>

                            </div>
                        </div>

                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection

@push('footer-js')
    <script>
        $("#business_service_id").select2({
            placeholder: "Select Services",
            allowClear: true
        });
        $('#add-group').click(function() {
            window.location = '{{ route('admin.employee-group.create') }}';
        })
        $('#save-form').click(function() {

            $.easyAjax({
                url: '{{ route('admin.employee.store') }}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file: true
            })
        });

        function show_hide_assign_service_group(role_name)
        {
            if(role_name == "Therapist")
            {
                $("#assign_service_group").show();
            }
            else
            {
                $("#assign_service_group").hide();
            }
        }

        show_hide_assign_service_group($("#role_id").find(':selected').text());

        $(document).ready(function () {
            
            $('body').on('change', '#role_id', function(){

                var role_id = $(this).val();
                var role_name = $(this).find(':selected').text();

                show_hide_assign_service_group(role_name)

            });

        });
    </script>
@endpush
