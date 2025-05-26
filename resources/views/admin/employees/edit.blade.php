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
                    <h3 class="card-title">@lang('app.edit') @lang('menu.employee')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.name')</label>
                                    <input type="text" class="form-control form-control-lg" name="name"
                                        value="{{ $employee->name }}" autocomplete="off">
                                </div>

                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.email')</label>
                                    <input type="email" class="form-control form-control-lg" name="email"
                                        value="{{ $employee->email }}" autocomplete="off">
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
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="form-row">
                                                <div class="col-md-4 mb-2">
                                                    <select name="calling_code" id="calling_code"
                                                        class="form-control select2">
                                                        @foreach ($calling_codes as $code => $value)
                                                            <option value="{{ $value['dial_code'] }}"
                                                                @if ($employee->calling_code) {{ $employee->calling_code == $value['dial_code'] ? 'selected' : '' }} @endif>
                                                                {{ $value['dial_code'] . ' - ' . $value['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="mobile"
                                                        value="{{ $employee->mobile }}" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-center d-flex justify-content-center align-items-center">
                                            @if ($employee->mobile_verified)
                                                <span class="text-success">
                                                    @lang('app.verified')
                                                </span>
                                            @else
                                                <span class="text-danger">
                                                    @lang('app.notVerified')
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="form-group">
                                    <label>@lang('app.employeeGroup')</label>
                                    <div class="input-group">
                                        <select name="group_id" id="group_id" class="form-control form-control-lg">
                                            <option value="0">@lang('app.selectEmployeeGroup')</option>
                                            @foreach ($groups as $group)
                                                <option @if ($group->id == $employee->group_id) selected @endif
                                                    value="{{ $group->id }}">{{ $group->name }}</option>
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
                                    <select name="role_id" id="role_id" class="form-control form-control-lg">
                                        @foreach ($roles as $role)
                                            <option @if ($role->id == $employee->role->id) selected @endif
                                                value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" id="assign_service_group">
                                    <label>@lang('app.assignServices')</label>
                                    <select name="service_id[]" id="service_id" class="form-control" multiple="multiple"
                                        style="width: 100%">
                                        <option value=""> @lang('app.selectServices') </option>
                                        @foreach ($businessServices as $service)
                                            <option @if (in_array($service->id, $selectedServices)) selected @endif
                                                value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>@lang('app.outlet')</label>
                                    <select name="outlet_id[]" id="outlet_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple">
                                        <option value="0">All Outlets</option>
                                        @foreach ($outlets as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, $selectedOutlets) ? 'selected' : '' }}>
                                                {{ $item->outlet_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>POS @lang('app.outlet')</label>
                                    <select name="pos_outlet_id[]" id="pos_outlet_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple">
                                        <option value="0">All Outlets</option>
                                        @foreach ($outlets as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, $selected_pos_outlets) ? 'selected' : '' }}>
                                                {{ $item->outlet_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="image_display_in_app">Image Display In Mobile Application</label>
                                        <select name="image_display_in_app" class="form-control">
                                            <option value="yes"
                                                {{ $employee->image_display_in_app == 'yes' ? 'selected' : '' }}> Yes
                                            </option>
                                            <option value="no"
                                                {{ $employee->image_display_in_app == 'no' ? 'selected' : '' }}> No
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    @if (!empty($employee->user_image_url))
                                        <div class="mt-3">
                                            <img src="{{ $employee->user_image_url }}" width="150" class="img-thumbnail">
                                        </div>
                                    @endif
                                    
                                    <div class="form-group mt-3">
                                        <label for="exampleInputPassword1">@lang('app.image')</label>
                                        <div class="card">
                                            <div class="card-body">
                                                <input type="file" id="input-file-now" name="image"
                                                    accept=".png,.jpg,.jpeg"
                                                    data-default-file="{{ $employee->user_image_url }}"
                                                    class="form-control" />
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="text-danger">** Recommended image resolution: 225 * 225</h6>
                                </div>

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
        $("#service_id").select2({
            placeholder: "Select Services",
            allowClear: true
        });
        $('.dropify').dropify({
            messages: {
                default: '@lang('app.dragDrop')',
                replace: '@lang('app.dragDropReplace')',
                remove: '@lang('app.remove')',
                error: '@lang('app.largeFile')'
            }
        });
        $('#add-group').click(function() {
            window.location = '{{ route('admin.employee-group.create') }}';
        })
        $('#save-form').click(function() {

            $.easyAjax({
                url: '{{ route('admin.employee.update', $employee->id) }}',
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
