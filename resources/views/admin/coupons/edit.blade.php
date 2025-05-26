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
                    <h3 class="card-title">@lang('app.edit') @lang('menu.coupon')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="editForm" class="ajax-form" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.coupon') @lang('app.title') </label>
                                    <input type="text" class="form-control" name="coupon_title" value="{{ $coupon->title }}"required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.coupon') @lang('app.code') </label>
                                    <input type="text" class="form-control" name="coupon_code" value="{{ $coupon->coupon_code }}"
                                        autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.coupon') Short @lang('app.description')</label>
                                    <textarea name="short_description" id="short_description" cols="30" class="form-control-lg form-control" rows="1">{{ $coupon->short_description ?? '' }}</textarea>
                                </div>
                            </div>
                       

                            {{-- <div class="col-md-4" id="branch_div">
                                <div class="form-group">
                                    <label>@lang('app.branch')</label>
                                    <select name="branch_id" id="branch_id" class="form-control form-control-lg select2"
                                        style="width: 100%">
                                        <option value="0">@lang('app.allBranch')</option>
                                        @foreach ($branches as $branchOption)
                                            <option value="{{ $branchOption->id }}"
                                                {{ $coupon->branch_id === $branchOption->id ? 'selected' : '' }}>
                                                {{ $branchOption->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-6" id="outlet_div">
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

                            <div class="col-md-6" id="service_div">
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

                            <div class="col-md-4">
                                <label style="">Customer Specific</label>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="is_customer_specific" id="is_customer_specific" value="1" class="form-check-input" style="position: relative; left: 0px;" {{($coupon->is_customer_specific == 1) ? 'checked': ''}}>
                                        Checked if Customer Specific
                                    </label>
                                </div>                   
                            </div>

                            <div class="col-md-8" id="customer_div">
                                <div class="form-group">
                                    <label>@lang('app.customer')</label>
                                    <select name="customer_id[]" id="customer_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple" required {{($coupon->is_customer_specific == 0) ? 'disabled': ''}}>
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
                                        <input type="number" min="0" name="min_age" id="min_age" class="form-control mr-2" value="{{$coupon->min_age ?? 18}}" required>
                                        <span>to</span>
                                        <input type="number" min="0" name="max_age" id="max_age" class="form-control ml-2" value="{{$coupon->max_age ?? 60}}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6" id="gender_div">
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

                            <div class="col-md-4">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.StartTime')</label>
                                    <input type="text" class="form-control" id="start_time" name="start_time"
                                        value="{{ \Carbon\Carbon::parse($coupon->start_date_time)->format($settings->date_format . ' ' . $settings->time_format) }}"
                                        autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.endTime')</label>
                                    <input type="text" class="form-control" id="end_time" name="end_time"
                                        value="{{ \Carbon\Carbon::parse($coupon->end_date_time)->format($settings->date_format . ' ' . $settings->time_format) }} "
                                        autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.usesTime')</label>
                                    <input type="number" class="form-control" name="uses_time"
                                        value="{{ $coupon->uses_limit }}">
                                    <span class="help-block">@lang('messages.howManyTimeUserCanUse')</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.amount')</label>
                                    <input type="number" class="form-control" name="amount"
                                        value="{{ $coupon->amount }}" required>
                                    <span class="help-block">@lang('messages.coupon.forAmountDiscountOrMaximumDiscount')</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.percent')</label>
                                    <input type="number" class="form-control" name="percent"
                                        value="{{ $coupon->percent }}">
                                    <span class="help-block">@lang('messages.coupon.forPercentDiscount')</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.minimumPurchaseAmount')</label>
                                    <input type="number" class="form-control" name="minimum_purchase_amount"
                                        value="{{ $coupon->minimum_purchase_amount }}">
                                    <span class="help-block">@lang('messages.coupon.keepBlankForwithoutMinimumAmount')</span>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="row">
                                    <label style="margin-left: 10px">@lang('app.dayForApply') </label>

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


                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.point') </label>
                                    <input type="number" class="form-control" name="points" value="{{ $coupon->points }}"
                                        autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('app.status')</label>
                                    <select name="status" class="form-control">
                                        <option @if ($coupon->status == 'active') selected @endif value="active">
                                            @lang('app.active') </option>
                                        <option @if ($coupon->status == 'inactive') selected @endif value="inactive">
                                            @lang('app.inactive') </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{!! $coupon->description !!} </textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                @if (!empty($coupon->coupon_image))
                                    <div class="mt-3">
                                        <img src="{{ asset('user-uploads/coupon-images/' . $coupon->coupon_image) }}" width="170" class="img-thumbnail" alt="">
                                    </div>
                                @endif

                                <!-- text input -->
                                <div class="form-group mt-3">
                                    <label>@lang('app.image')</label>
                                    <input type="file" class="form-control" name="coupon_image">
                                    {{-- <span class="help-block">@lang('messages.coupon.keepBlankForwithoutMinimumAmount')</span> --}}
                                </div>

                                <h6 class="text-danger">** Recommended image resolution: 380 * 180</h6>
                            </div>

                            <div class="form-group">
                                <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                        class="fa fa-check"></i> @lang('app.save')</button>
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
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/collaps.js') }}"></script>
    <script src="{{ asset('assets/js/transition.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    
    <script>
        let startDate = '{{ \Carbon\Carbon::parse($coupon->start_date_time)->format('Y-m-d') }}';
        let endDate = '{{ \Carbon\Carbon::parse($coupon->end_date_time)->format('Y-m-d') }}';
        let startTime = '{{ \Carbon\Carbon::parse($coupon->start_date_time)->format('H:i a') }}';
        let endTime = '{{ \Carbon\Carbon::parse($coupon->end_date_time)->format('H:i a') }}';


        $('#customers').select2();

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

        $('#start_time').datetimepicker({
            format: '{{ $date_picker_format }} {{ $time_picker_format }}',
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
            startDate = moment(e.date).format('YYYY-MM-DD');
            startTime = convert(e.date);
        });

        $('#end_time').datetimepicker({
            format: '{{ $date_picker_format }} {{ $time_picker_format }}',
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
            endDate = moment(e.date).format('YYYY-MM-DD');
            endTime = convert(e.date);
        });


        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
        })
        $('.dropify').dropify({
            messages: {
                default: '@lang('app.dragDrop')',
                replace: '@lang('app.dragDropReplace')',
                remove: '@lang('app.remove')',
                error: '@lang('app.largeFile')'
            }
        });


        // ck editor start

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

        // ck editor end

        // $('#save-form').click(function() {
        //     $.easyAjax({
        //         url: '{{ route('admin.coupons.update', $coupon->id) }}',
        //         container: '#editForm',
        //         type: "POST",
        //         redirect: true,
        //         // file: true,
        //         data: $('#editForm').serialize() + '&startDate=' + startDate + '&endDate=' + endDate +
        //             '&startTime=' + startTime + '&endTime=' + endTime,
        //     })
        // });

        $('#save-form').click(function () {

            var min_age = parseInt($("#min_age").val());
            var max_age = parseInt($("#max_age").val());

            // console.log(min_age);
            // console.log(max_age);

            if(min_age <= max_age)
            {
                // Manually update the textarea with CKEditor content
                $('#description').val(ckeditorInstance.getData());
                
                var form = $('#editForm')[0];
                var formData = new FormData(form);

                // Adding extra fields to formData if needed
                formData.append('startDate', startDate);
                formData.append('endDate', endDate);
                formData.append('startTime', startTime);
                formData.append('endTime', endTime);

                $.ajax({
                    url: '{{ route('admin.coupons.update', $coupon->id) }}',
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    
                    success: function (response) {
                        console.log(response);
                        if(response.status == "success")
                        {
                            var msgs = response.message;
                            $.showToastr(msgs, 'success');
                            window.location.href = response.url;
                        }
                    },
                    error: function (response) {
                        console.log(response);
                        // Handle error
                        if(response.responseJSON.errors)
                        {
                            $.each(response.responseJSON.errors, function (key, value) { 
                                $.each(value, function(index, error) {
                                    $.showToastr(error, 'error');
                                });
                            });
                        }
                    }
                });
            }
            else
            {
                $.showToastr("Minimum Age is not greater than Maximum age", 'error');
            }
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

        $(document).ready(function () {
            
            $('body').on('click', '#is_customer_specific', function(){

                if ($(this).is(':checked'))
                { 
                    $("#customer_id").prop("disabled", false);
                }
                else
                {
                    $("#customer_id").prop("disabled", true);
                }

            });

        });
    </script>
@endpush
