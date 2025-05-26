@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">

    <style>
        .collapse.in {
            display: block;
        }

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

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('menu.voucher')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="editForm" class="ajax-form" method="POST">
                        @csrf
                        <span id="put_method"></span>
                        {{-- @method('PUT') --}}

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.title') </label>
                                    <input type="text" class="form-control" name="title" id="title"
                                        value="{{ $voucher_discount_package->title }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.slug') </label>
                                    <input type="text" class="form-control" name="slug" id="slug"
                                        value="{{ $voucher_discount_package->slug }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-12" id="outlet_div">
                                <div class="form-group">
                                    <label>@lang('app.outlet')</label>
                                    <select name="outlet_id[]" id="outlet_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple" required>
                                        <option value="0">All Outlets</option>
                                        @foreach ($outlets as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, $selectedOutlets) ? 'selected' : '' }}>
                                                {{ $item->outlet_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12" id="service_div">
                                <div class="form-group">
                                    <label>@lang('app.service')</label>
                                    <select name="services[]" id="services" class="form-control form-control-lg select2"
                                        multiple="multiple" style="width: 100%" required>
                                        <option value="0">@lang('app.allServices')</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}"
                                                {{ in_array($service->id, $selectedServices) ? 'selected' : '' }}>
                                                ({{$service->time}} {{$service->time_type}}) {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label style="">Customer Specific</label>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="is_customer_specific" id="is_customer_specific" value="1" class="form-check-input" style="position: relative; left: 0px;" {{($voucher_discount_package->is_customer_specific == 1) ? 'checked': ''}}>
                                        Checked if Customer Specific
                                    </label>
                                </div>                   
                            </div>

                            <div class="col-md-9" id="customer_div">
                                <div class="form-group">
                                    <label>@lang('app.customer')</label>
                                    <select name="customer_id[]" id="customer_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple" required {{($voucher_discount_package->is_customer_specific == 0) ? 'disabled': ''}}>
                                        <option value="0">All Customers</option>
                                        @foreach ($customers as $item)
                                            <option value="{{ $item->id }}" {{ in_array($item->id, $selectedCustomers) ? 'selected' : '' }}>{{ $item->name }} ({{$item->mobile}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                   
                            <div class="col-md-4" id="age_div">
                                <div class="form-group">
                                    <label>Customer Age Eligibility</label>    
                                    
                                    <div class="form-inline">
                                        <input type="number" min="0" name="min_age" id="min_age" class="form-control mr-2" value="{{$voucher_discount_package->min_age ?? 18}}" required>
                                        <span>to</span>
                                        <input type="number" min="0" name="max_age" id="max_age" class="form-control ml-2" value="{{$voucher_discount_package->max_age ?? 60}}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4" id="gender_div">
                                <div class="form-group">
                                    <label>Customer Gender Eligibility</label>
                                    <select name="gender[]" id="gender" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple" required>
                                        <option value="all">All</option>
                                        <option value="male" {{ in_array('male', $selectedCustomersGender) ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ in_array('female', $selectedCustomersGender) ? 'selected' : '' }}>Female</option>
                                        <option value="others" {{ in_array('others', $selectedCustomersGender) ? 'selected' : '' }}>Others</option>
                                    </select>
                                </div>
                            </div>

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.discount')</label>
                                    <input type="number" class="form-control checkAmount" name="discount" id="discount"
                                        value="{{ $voucher_discount_package->discount }}">
                                </div>
                            </div> --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Discount Type</label>
                                    <select name="discount_type" id="discount_type" class="form-control">
                                        <option value="amount" {{($voucher_discount_package->discount_type == "amount") ? 'selected' : ''}}>Amount</option>
                                        <option value="percent" {{($voucher_discount_package->discount_type == "percent") ? 'selected' : ''}}>Percent</option>
                                    </select>     
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label>Discount Package</label>
                                <button type="button" class="btn btn-primary ml-4" id="add_dp_btn">Add</button>
                            </div>

                            @php
                                if($voucher_discount_package->discount_type == "percent")
                                {
                                    $display_none = "display: none";
                                }
                                else {
                                    $display_none = "";
                                }
                            @endphp

                            <div class="col-md-10 offset-md-1"> 
                                <div class="table-responsive">                             
                                    <table class="table table-bordered w-100" id="dp_table">
                                        <thead class="thead-dark">
                                            <tr>                                                          
                                                <th class="th_amount_class">{{($voucher_discount_package->discount_type == "percent") ? 'Percent (%)' : 'Amount ($)'}}</th>
                                                <th>Quantity</th>
                                                <th class="th_total_amount_class" style="{{$display_none ?? ''}}">Total Amount ($)</th>
                                                <th>Action</th>                     
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($voucher_discount_package_detail as $item)
                                                <tr>
                                                    <td class="td_dp_amount_class">
                                                        <input type="hidden" name="voucher_id[]" value="{{ $item->voucher_id }}">
                                                        <input type="number" class="form-control dp_amount_class" name="amount[]" value="{{ $item->amount }}" min="0">
                                                    </td>
                                                    <td class="td_dp_qty_class">
                                                        <input type="number" class="form-control dp_qty_class" name="qty[]" value="{{ $item->qty }}" min="0">
                                                    </td>
                                                    <td class="td_dp_total_amount_class" style="{{$display_none ?? ''}}">
                                                        <input type="number" class="form-control dp_total_amount_class" name="total_amount[]" value="{{ $item->total_amount }}" min="0">
                                                    </td>
                                                    <td class="td_delete_btn_class">
                                                        <a class="btn btn-danger btn-sm td_delete_btn" href="#" title="Delete">
                                                            <i class="fa fa-times"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="dp_table_tfoot" style="{{$display_none ?? ''}}">
                                            <tr>
                                                <td></td>
                                                <td class="text-right">Grand Total</td>
                                                <td>
                                                    <input type="number" name="grand_total" id="dp_grand_total" class="form-control dp_grand_total_class" min="0" value="{{$voucher_discount_package->grand_total}}" readonly>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Maximum Discount Amount</label>
                                    <input type="number" class="form-control" name="max_discount" value="{{ $voucher_discount_package->max_discount }}">                               
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.minimumSpendingAmount')</label>
                                    <input type="number" class="form-control" name="minimum_purchase_amount" value="{{ $voucher_discount_package->minimum_purchase_amount }}">
                                    <span
                                        class="help-block">@lang('messages.coupon.keepBlankForwithoutMinimumAmount')</span>
                                </div>
                            </div>

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.voucherLimit')</label>
                                    <input type="number" class="form-control" name="uses_time"
                                        value="{{ $voucher_discount_package->uses_limit }}" min="0">
                                    <span class="help-block">@lang('messages.voucherLimit')</span>
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.customerUseLimit')</label>
                                    <input type="number" class="form-control" name="customer_uses_time"
                                        value="{{ $voucher_discount_package->max_order_per_customer }}" min="0">
                                    <span class="help-block">@lang('messages.howManyTimeCustomerCanUse')</span>
                                </div>
                            </div> --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Redeem @lang('app.loyalty_point')</label>
                                    <input type="text" class="form-control" name="loyalty_point"  value="{{ $voucher_discount_package->loyalty_point }}" min="0">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('app.appliedBetweenDateTime')</label>

                                    @if (!empty($voucher_discount_package->start_date_time))
                                        <input type="text" class="form-control" id="daterange" name="applied_between_dates"
                                        autocomplete="off"
                                        value="{{ \Carbon\Carbon::parse($voucher_discount_package->start_date_time)->translatedFormat($settings->date_format . ' ' . $settings->time_format) }}--{{ \Carbon\Carbon::parse($voucher_discount_package->end_date_time)->translatedFormat($settings->date_format . ' ' . $settings->time_format) }}">
                                    @else
                                        <input type="text" class="form-control" id="daterange" name="applied_between_dates"
                                        autocomplete="off">
                                    @endif
                                    
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">@lang('app.status')</label>
                                    <select name="status" class="form-control">
                                        <option @if ($voucher_discount_package->status == 'active') selected @endif value="active">
                                            @lang('app.active') </option>
                                        <option @if ($voucher_discount_package->status == 'inactive') selected @endif value="inactive">
                                            @lang('app.inactive') </option>
                                    </select>
                                </div>
                            </div>

                           

                            {{-- <div class="col-md-4">
                                <div class="form-group time-picker">
                                    <label>@lang('modules.settings.openTime')</label>
                                    <input type="text" class="form-control" id="open_time" name="open_time"
                                        autocomplete="off" value="{{ $voucher_discount_package->open_time }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group time-picker">
                                    <label>@lang('modules.settings.closeTime')</label>
                                    <input type="text" class="form-control" id="close_time" name="close_time"
                                        autocomplete="off" value="{{ $voucher_discount_package->close_time }}">
                                </div>
                            </div> --}}


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Validity</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" name="validity" min="0" value="{{$voucher_discount_package->validity ?? ''}}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary dropdown-toggle" id="time-type-select" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$voucher_discount_package->validity_type ?? ''}}</button>
                                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(564px, 39px, 0px);">
                                                <a class="dropdown-item time_type_dropdown" data-type="months" href="javascript:;">Months</a>
                                                <a class="dropdown-item time_type_dropdown" data-type="years" href="javascript:;">Years</a>                                             
                                            </div>
                                        </div>

                                        <input type="hidden" id="validity_type" name="validity_type" value="{{$voucher_discount_package->validity_type ?? ''}}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="">Show in loyalty shop</label>
                                <div class="row" style="margin-top: 5px">
                                    <div class="form-group" style="margin-left: 1em">
                                        <label class="">
                                            <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false"
                                                style="position: relative; margin-right: 5px;">
                                                <input type="checkbox" {{ ($voucher_discount_package->is_redeemable == 1) ? 'checked' : '' }} name="is_redeemable" id="is_redeemable" value="1" class="flat-red columnCheck"
                                                    style="position: absolute; opacity: 0; margin-left: 15px;">
                                                <ins class="iCheck-helper"
                                                    style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px;
                                                        background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                            </div>
                                        </label>
                                        <label class="form-check-label" style="margin-left: 5px;">
                                            Show in loyalty shop
                                        </label>
                                    </div>
                                </div>
                            </div>                        
                            
                            <div class="col-md-4">
                                <label style="">@lang('app.is_welcome') </label>
                                <div class="row" style="margin-top: 5px">
                                    <div class="form-group" style="margin-left: 1em">
                                        <label class="">
                                            <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false"
                                                style="position: relative; margin-right: 5px;">
                                                <input type="checkbox" {{ ($voucher_discount_package->is_welcome == 1) ? 'checked' : '' }} name="is_welcome" id="is_welcome" value="1" class="flat-red columnCheck"
                                                    style="position: absolute; opacity: 0; margin-left: 15px;">
                                                <ins class="iCheck-helper"
                                                    style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; 
                                                        background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                            </div>
                                        </label>
                                        <label class="form-check-label" style="margin-left: 5px;">
                                            @lang('app.is_welcome_text')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="col-md-12">
                                <label>@lang('app.dayForApply') </label>
                                <div class="row" style="margin-top: 5px">
                                    @forelse($days as $day)
                                        <div class="form-group" style="margin-left: 20px">
                                            <label class="">
                                                <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false"
                                                    style="position: relative; margin-right: 5px">
                                                    <input type="checkbox" @if (!is_null($selectedDays) && in_array($day, $selectedDays)) checked @endif
                                                        value="{{ $day }}" name="days[]"
                                                        class="flat-red columnCheck"
                                                        style="position: absolute; opacity: 0;">
                                                    <ins class="iCheck-helper"
                                                        style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                </div>
                                                @lang('app.' . strtolower($day))
                                            </label>
                                        </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{{ $voucher_discount_package->description }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                @if (!empty($voucher_discount_package->image))
                                    <div class="mt-3">
                                        <img src="{{ asset('user-uploads/voucher/' . $voucher_discount_package->image) }}" width="180" class="img-thumbnail">
                                    </div>
                                @endif
                                
                                <div class="form-group mt-3">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    
                                    <input type="file" id="input-file-now" name="feature_image"
                                                accept=".png,.jpg,.jpeg"
                                                data-default-file="{{ asset('user-uploads/voucher/' . $voucher_discount_package->image) }}"
                                                class="form-control" />                                    
                                </div>

                                <h6 class="text-danger">** Recommended image resolution: 200 * 200</h6>
                            </div>

                            <div class="form-group">
                                <button type="button" id="save-form" class="btn btn-success btn-light-round">
                                    <i class="fa fa-check"></i> @lang('app.save')
                                </button>
                            </div>

                            <input type="hidden" name="voucher_startDate" id="voucher_startDate"
                                value="{{ \Carbon\Carbon::parse($voucher_discount_package->start_date_time)->format('Y-m-d h:i A') }}">
                            <input type="hidden" name="voucher_endDate" id="voucher_endDate"
                                value="{{ \Carbon\Carbon::parse($voucher_discount_package->end_date_time)->format('Y-m-d h:i A') }}">
                            
                            {{-- <input type="hidden" name="voucher_startTime" id="voucher_startTime"
                                value="{{ \Carbon\Carbon::parse($voucher_discount_package->open_time)->format('h:i A') }}">
                            <input type="hidden" name="voucher_endTime" id="voucher_endTime"
                                value="{{ \Carbon\Carbon::parse($voucher_discount_package->close_time)->format('h:i A') }}"> --}}
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
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>

    <script>
    
        $('.dropify').dropify({
            messages: {
                default: '@lang('app.dragDrop')',
                replace: '@lang('app.dragDropReplace')',
                remove: '@lang('app.remove')',
                error: '@lang('app.largeFile')'
            }
        });

        $('#open_time').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
        }).on('dp.change', function(e) {
            $('#voucher_startTime').val(convert(e.date));
        });

        $('#close_time').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
        }).on('dp.change', function(e) {
            $('#voucher_endTime').val(convert(e.date));
        });

        $(function() {
            // $('#description').summernote({
            //     dialogsInBody: true,
            //     height: 300,
            //     toolbar: [
            //         // [groupName, [list of button]]
            //         ['style', ['bold', 'italic', 'underline', 'clear']],
            //         ['font', ['strikethrough']],
            //         ['fontsize', ['fontsize']],
            //         ['para', ['ul', 'ol', 'paragraph']],
            //         ["view", ["fullscreen"]]
            //     ]
            // });

            var ckeditorInstance;

            ClassicEditor.create(document.querySelector('#description'), {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'bulletedList', 'numberedList', '|',
                        'undo', 'redo'
                    ]
                },
                removePlugins: [
                    'Image',
                    'ImageToolbar',
                    'ImageCaption',
                    'ImageStyle',
                    'ImageResize',
                    'ImageUpload',
                    'CKFinder',
                    'MediaEmbed',
                    'Table',
                    'BlockQuote',
                    'EasyImage'
                ]
            })
            .then(editor => {
                ckeditorInstance = editor;
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });

            $('#save-form').click(function() {

                var min_age = parseInt($("#min_age").val());
                var max_age = parseInt($("#max_age").val());

                // console.log(min_age);
                // console.log(max_age);

                if(min_age <= max_age)
                {
                    $('#put_method').html(`@method('PUT')`);

                    // Manually update the textarea with CKEditor content
                    $('#description').val(ckeditorInstance.getData());

                    $.easyAjax({
                        url: '{{ route('admin.vouchers.update', $voucher_discount_package->id) }}',
                        container: '#editForm',
                        type: "POST",
                        redirect: true,
                        data: $('#editForm').serialize(),
                        file: true
                    });
                    $('#put_method').html('');
                }
                else
                {
                    $.showToastr("Minimum Age is not greater than Maximum age", 'error');
                }
                
            });
        });

        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
        });

        $(function() {
            moment.locale('{{ $settings->locale }}');
            $('input[name="applied_between_dates"]').daterangepicker({
                timePicker: true,
                // minDate: moment().startOf('hour'),
                autoUpdateInput: false,
            });
        });

        $('input[name="applied_between_dates"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('{{ $date_picker_format }} {{ $time_picker_format }}') + '--' +
                picker.endDate.format('{{ $date_picker_format }} {{ $time_picker_format }}'));
            $('#voucher_startDate').val(picker.startDate.format('YYYY-MM-DD') + ' ' + convert(picker.startDate));
            $('#voucher_endDate').val(picker.endDate.format('YYYY-MM-DD') + ' ' + convert(picker.endDate));
        });


        function createSlug(value) {
            value = value.replace(/\s\s+/g, ' ');
            let slug = value.split(' ').join('-').toLowerCase();
            slug = slug.replace(/--+/g, '-');
            $('#slug').val(slug);
        }

        $('#title').keyup(function(e) {
            createSlug($(this).val());
        });

        $('#slug').keyup(function(e) {
            createSlug($(this).val());
        });

        function convert(str) {
            var date = new Date(str);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            hours = ("0" + hours).slice(-2);
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }

        // Function to recalculate total amount
        function calculateTotalAmount() 
        {
            var total_amount = 0;
            var grand_total = 0;

            $("#dp_table tbody tr").each(function () {

                var amount = parseFloat($(this).find('.dp_amount_class').val()) || 0;
                var qty = parseFloat($(this).find('.dp_qty_class').val()) || 0;

                total_amount = (amount * qty);
                grand_total += total_amount;

                $(this).find('.dp_total_amount_class').val(parseFloat(total_amount).toFixed(2));

            });

            $('#dp_grand_total').val(grand_total);
        }

        $(document).ready(function () {

            $('body').on('click', '#is_customer_specific', function(){

                if ($(this).is(':checked'))
                { 
                    $("#customer_id").prop("disabled", false);
                    // $("#is_welcome").prop("disabled", true);
                    // $("#is_welcome").parents(".icheckbox_flat-blue").removeClass('checked');
                    // $("#is_welcome").parents(".icheckbox_flat-blue").addClass('disabled');
                }
                else
                {
                    $("#customer_id").prop("disabled", true);
                    // $("#is_welcome").prop("disabled", false);
                    // $("#is_welcome").parents(".icheckbox_flat-blue").addClass('checked');
                    // $("#is_welcome").parents(".icheckbox_flat-blue").removeClass('disabled');
                }

            });

            // discount package start

            $('body').on('click', '#add_dp_btn', function (e) {

                e.preventDefault();

                var newRow = `<tr>
                                <td class="td_dp_amount_class">
                                    <input type="number" class="form-control dp_amount_class" name="amount[]" min="0">
                                </td>
                                <td class="td_dp_qty_class">
                                    <input type="number" class="form-control dp_qty_class" name="qty[]" min="0">
                                </td>
                                <td class="td_dp_total_amount_class">
                                    <input type="number" class="form-control dp_total_amount_class" name="total_amount[]" min="0">
                                </td>
                                <td class="td_delete_btn_class">
                                    <a class="btn btn-danger btn-sm td_delete_btn" href="#" title="Delete">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>`;

                $('#dp_table tbody').append(newRow);

                calculateTotalAmount();

            });

            // Delegate delete button click for dynamically added rows
            $('body').on('click', '.td_delete_btn', function (e) {
                e.preventDefault();

                var rowCount = $('#dp_table tbody tr').length;

                if (rowCount > 1) {
                    $(this).closest('tr').remove();
                } else {
                    $.showToastr("At least one discount package row must remain.", 'error');
                }

                calculateTotalAmount();
            });

            // Auto-calculate Total amount when amount or Quantity changes
            $('body').on('input', '.dp_amount_class, .dp_qty_class', function () {
                var row = $(this).closest('tr');
                calculateTotalAmount();
            });

            $('body').on('change', '#discount_type', function() {

                var discount_type = $(this).val();

                if(discount_type == "percent")
                {
                    $(".dp_table_tfoot").hide();
                    $(".th_total_amount_class").hide();
                    $('.td_dp_total_amount_class').hide();
                    $(".th_amount_class").text("Percent (%)");
                }   
                else
                {
                    $(".dp_table_tfoot").show();
                    $(".th_total_amount_class").show();
                    $('.td_dp_total_amount_class').show();
                    $(".th_amount_class").text("Amount ($)");
                }

            });

            // discount package end

            $('.time_type_dropdown').click(function () {
                var type = $(this).data('type');

                $('#time-type-select').html(type);
                $('#validity_type').val(type);
            });

            // only for edit
            $("#is_redeemable").prop("disabled", true);

        });
    </script>
@endpush
