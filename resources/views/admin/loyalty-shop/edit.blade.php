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
                    <h3 class="card-title">@lang('app.edit') Loyalty Shop</h3>
                </div>
                <div class="card-body">
                    <form role="form" id="editForm" class="ajax-form" method="POST">
                        @csrf
                        <span id="put_method"></span>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.title') </label>
                                    <input type="text" class="form-control" name="title" id="title" value="{{ $LoyaltyShop->title }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.slug') </label>
                                    <input type="text" class="form-control" name="slug" id="slug" value="{{ $LoyaltyShop->title }}"
                                        autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Short @lang('app.description') (Max 65 Characters)</label>
                                    <textarea name="short_description" id="short_description" cols="30" class="form-control-lg form-control" rows="1">{{ $LoyaltyShop->short_description ?? '' }}</textarea>
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

                            {{-- <div class="col-md-3" id="product_or_service_div">
                                <div class="form-group">
                                    <label>Chosse Type</label>
                                    <select name="loyalty_shop_type" id="loyalty_shop_type" class="form-control" required>
                                        <option value="product" {{($LoyaltyShop->loyalty_shop_type == "product") ? 'selected' : ''}}>@lang('app.products')</option>
                                        <option value="service" {{($LoyaltyShop->loyalty_shop_type == "service") ? 'selected' : ''}}>@lang('app.services')</option>
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-12" id="product_div">
                                <div class="form-group">
                                    <label>@lang('app.products')</label>
                                    <select name="product_id" id="product_id" class="form-control form-control-lg select2" style="width: 100%">
                                        <option value="">Select</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{($LoyaltyShop->product_id == $product->id) ? 'selected' : ''}}>{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- <div class="col-md-9" id="service_div" style="display: {{($LoyaltyShop->loyalty_shop_type == "service") ? '' : 'none'}}">
                                <div class="form-group">
                                    <label>@lang('app.services')</label>
                                    <select name="service_id" id="service_id" class="form-control form-control-lg select2" style="width: 100%">
                                        <option value="">Select</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" {{($LoyaltyShop->service_id == $service->id) ? 'selected' : ''}}>({{$service->time}} {{$service->time_type}}) {{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-3">
                                <label style="">Customer Specific</label>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="is_customer_specific" id="is_customer_specific" value="1" class="form-check-input" style="position: relative; left: 0px;" {{($LoyaltyShop->is_customer_specific == 1) ? 'checked': ''}}>
                                        Checked if Customer Specific
                                    </label>
                                </div>                   
                            </div>
    
                            <div class="col-md-9" id="customer_div">
                                <div class="form-group">
                                    <label>@lang('app.customer')</label>
                                    <select name="customer_id[]" id="customer_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple" required {{($LoyaltyShop->is_customer_specific == 0) ? 'disabled': ''}}>
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
                                        <input type="number" min="0" name="min_age" id="min_age" class="form-control mr-2" value="{{$LoyaltyShop->min_age ?? 18}}" required>
                                        <span>to</span>
                                        <input type="number" min="0" name="max_age" id="max_age" class="form-control ml-2" value="{{$LoyaltyShop->max_age ?? 60}}" required>
                                    </div>
                                </div>
                            </div>   
    
                            <div class="col-md-8" id="gender_div">
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

                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.discount')</label>
                                    <input onkeypress="return isNumberKey(event)" type="number" class="form-control checkAmount" name="discount" id="discount" value="{{$LoyaltyShop->discount}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Discount Type</label>
                                    <select name="discount_type" id="discount_type" class="form-control">
                                        <option value="amount" {{($LoyaltyShop->discount_type == "amount") ? 'selected' : ''}}>Amount</option>
                                        <option value="percent" {{($LoyaltyShop->discount_type == "percent") ? 'selected' : ''}}>Percent</option>
                                    </select>      
                                </div>
                            </div>                  --}}
                      
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Redeem @lang('app.loyalty_point')</label>
                                    <input type="text" class="form-control" name="loyalty_point" min="0" value="{{ $LoyaltyShop->loyalty_point }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.appliedBetweenDateTime')</label>
                                    <input type="text" class="form-control" id="daterange" name="applied_between_dates"
                                        autocomplete="off"
                                        value="{{ \Carbon\Carbon::parse($LoyaltyShop->start_date_time)->translatedFormat($settings->date_format . ' ' . $settings->time_format) }}--{{ \Carbon\Carbon::parse($LoyaltyShop->end_date_time)->translatedFormat($settings->date_format . ' ' . $settings->time_format) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">@lang('app.status')</label>
                                    <select name="status" class="form-control">
                                        <option @if ($LoyaltyShop->status == 'active') selected @endif value="active">
                                            @lang('app.active') </option>
                                        <option @if ($LoyaltyShop->status == 'inactive') selected @endif value="inactive">
                                            @lang('app.inactive') </option>
                                    </select>
                                </div>
                            </div>                            

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Validity</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" name="validity" min="0" value="{{$LoyaltyShop->validity ?? ''}}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary dropdown-toggle" id="time-type-select" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$LoyaltyShop->validity_type ?? ''}}</button>
                                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(564px, 39px, 0px);">
                                                <a class="dropdown-item time_type_dropdown" data-type="months" href="javascript:;">Months</a>
                                                <a class="dropdown-item time_type_dropdown" data-type="years" href="javascript:;">Years</a>                                             
                                            </div>
                                        </div>

                                        <input type="hidden" id="validity_type" name="validity_type" value="{{$LoyaltyShop->validity_type ?? ''}}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label style="">Redeemable</label>
                                <div class="row" style="margin-top: 5px">
                                    <div class="form-group" style="margin-left: 1em">
                                        <label class="">
                                            <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false"
                                                style="position: relative; margin-right: 5px;">
                                                <input type="checkbox" {{ ($LoyaltyShop->is_redeemable == 1) ? 'checked' : '' }} name="is_redeemable" id="is_redeemable" value="1" class="flat-red columnCheck"
                                                    style="position: absolute; opacity: 0; margin-left: 15px;">
                                                <ins class="iCheck-helper"
                                                    style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px;
                                                        background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                            </div>
                                        </label>
                                        <label class="form-check-label" style="margin-left: 5px;">
                                            Checked If Redeemable
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label style="">Welcome Pop</label>
                                <div class="row" style="margin-top: 5px">
                                    <div class="form-group" style="margin-left: 1em">
                                        <label class="">
                                            <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false"
                                                style="position: relative; margin-right: 5px;">
                                                <input type="checkbox" {{ ($LoyaltyShop->is_welcome == 1) ? 'checked' : '' }} name="is_welcome" id="is_welcome" value="1" class="flat-red columnCheck"
                                                    style="position: absolute; opacity: 0; margin-left: 15px;">
                                                <ins class="iCheck-helper"
                                                    style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px;
                                                        background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                            </div>
                                        </label>
                                        <label class="form-check-label" style="margin-left: 5px;">
                                            Checked if Welcome Pop
                                        </label>
                                    </div>
                                </div>
                            </div>                           

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{{ $LoyaltyShop->description }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                @if (!empty($LoyaltyShop->image))
                                    <div class="mt-3">
                                        <img src="{{ asset('user-uploads/loyalty-shop/' . $LoyaltyShop->image) }}" width="180" class="img-thumbnail">
                                    </div>
                                @endif
                                
                                <div class="form-group mt-3">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    
                                    <input type="file" id="input-file-now" name="feature_image"
                                                accept=".png,.jpg,.jpeg"
                                                data-default-file="{{ asset('user-uploads/loyalty-shop/' . $LoyaltyShop->image) }}"
                                                class="form-control" />                                    
                                </div>

                                <h6 class="text-danger">** Recommended image resolution: 200 * 200</h6>
                            </div>

                            <div class="form-group">
                                <button type="button" id="save-form" class="btn btn-success btn-light-round">
                                    <i class="fa fa-check"></i> @lang('app.save')
                                </button>
                            </div>

                            <input type="hidden" name="loyalty_shop_startDate" id="loyalty_shop_startDate"
                                value="{{ \Carbon\Carbon::parse($LoyaltyShop->start_date_time)->format('Y-m-d h:i A') }}">
                            <input type="hidden" name="loyalty_shop_endDate" id="loyalty_shop_endDate"
                                value="{{ \Carbon\Carbon::parse($LoyaltyShop->end_date_time)->format('Y-m-d h:i A') }}">

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

        function createSlug(value) {
            value = value.replace(/\s\s+/g, ' ');
            let slug = value.split(' ').join('-').toLowerCase();
            slug = slug.replace(/--+/g, '-');
            $('#slug').val(slug);
        }

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

        $(function() {

            $('.dropify').dropify({
                messages: {
                    default: '@lang('app.dragDrop')',
                    replace: '@lang('app.dragDropReplace')',
                    remove: '@lang('app.remove')',
                    error: '@lang('app.largeFile')'
                }
            });

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
            

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
            });

            $('.checkAmount').keyup(function() {
                var original_amount = $('#original_amount').val();
                var discount_amount = $('#discount_amount').val();
                if (original_amount != '' && discount_amount != '' && Number(discount_amount) > Number(
                        original_amount)) {
                    $('#discount_amount').focus();
                    $('#discount_amount').val('');
                }
            });

            moment.locale('{{ $settings->locale }}');
            $('input[name="applied_between_dates"]').daterangepicker({
                timePicker: false,
                // minDate: moment().startOf('hour'),
                autoUpdateInput: false,
            });

            $('input[name="applied_between_dates"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('{{ $date_picker_format }} {{ $time_picker_format }}') + '--' +
                    picker.endDate.format('{{ $date_picker_format }} {{ $time_picker_format }}'));
                $('#loyalty_shop_startDate').val(picker.startDate.format('YYYY-MM-DD') + ' ' + convert(picker.startDate));
                $('#loyalty_shop_endDate').val(picker.endDate.format('YYYY-MM-DD') + ' ' + convert(picker.endDate));
            });

            $('#title').keyup(function(e) {
                createSlug($(this).val());
            });

            $('#slug').keyup(function(e) {
                createSlug($(this).val());
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
                        url: "{{ route('admin.loyalty-shop.update', $LoyaltyShop->id) }}",
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

            // $('#loyalty_shop_type').change(function () {
            //     var selectedType = $(this).val();

            //     if (selectedType === 'product') {
            //         $('#product_div').show();
            //         $('#service_div').hide();
            //     } else if (selectedType === 'service') {
            //         $('#service_div').show();
            //         $('#product_div').hide();
            //     } else {
            //         $('#product_div').hide();
            //         $('#service_div').hide();
            //     }
            // });

            $('.time_type_dropdown').click(function () {
                var type = $(this).data('type');

                $('#time-type-select').html(type);
                $('#validity_type').val(type);
            });

        });
    </script>
@endpush
