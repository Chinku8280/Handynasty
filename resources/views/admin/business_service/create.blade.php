@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
    
    <style>
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #999;
        }
        .select2-dropdown .select2-search__field:focus, .select2-search--inline .select2-search__field:focus {
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
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.add') @lang('app.service')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.service') @lang('app.name')</label>
                                    <input type="text" name="name" id="name" class="form-control form-control-lg" @if (!empty($service)) value="{{ $service->name }}" @endif autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.service') @lang('app.slug')</label>
                                    <input type="text" name="slug" id="slug" class="form-control form-control-lg" @if (!empty($service)) value="{{ $service->slug }}" @endif autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.service') Short @lang('app.description')</label>
                                    <textarea name="short_description" id="short_description" cols="30" class="form-control-lg form-control" rows="1">{{ !empty($service) ? $service->short_description : '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.service') @lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{{ !empty($service) ? $service->description : '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.price')</label>
                                    <input onkeypress="return isNumberKey(event)" type="number" step="0.01" min="0" name="price" id="price" class="form-control form-control-lg" @if (!empty($service)) value="{{ $service->price }}" @endif/>
                                </div>
                            </div>

                            {{-- <div class="col-md-4">

                                <div class="form-group">
                                    <label>@lang('modules.services.discount')</label>
                                    <div class="input-group">
                                        <input onkeypress="return isNumberKey(event)" type="number" max="100" class="form-control form-control-lg" name="discount" id="discount" min="0" @if (!empty($service)) value="{{ $service->discount }}" @endif>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary dropdown-toggle" id="discount-type-select" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @if (!empty($service))
                                                    @if ($service->discount_type == 'percent')
                                                        @lang('modules.services.percent')
                                                    @else
                                                        @lang('modules.services.fixed')
                                                    @endif
                                                @else
                                                        @lang('modules.services.percent')
                                                @endif
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item discount_type" data-type="percent" href="javascript:;">@lang('modules.services.percent')</a>
                                                <a class="dropdown-item discount_type" data-type="fixed" href="javascript:;">@lang('modules.services.fixed')</a>
                                            </div>
                                        </div>

                                        <input type="hidden" id="discount-type" name="discount_type" value="percent">

                                    </div>

                                </div>
                            </div> --}}

                            {{-- <div class="col-md-3 offset-md-1">
                                <div class="form-group">
                                    <label>@lang('modules.services.discountedPrice')</label>
                                    <p class="form-control-static" id="discounted-price" style="font-size: 1.5rem">--</p>
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.location')</label>
                                    <div class="input-group">
                                        <select name="location_id" id="location_id" class="form-control form-control-lg">
                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}" @if (!empty($service) && $service->location->id == $location->id)
                                                    selected
                                                @endif>{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-success" onclick="javascript: location = '{{ route('admin.branches.create') }}';" type="button"><i class="fa fa-plus"></i> @lang('app.add')</button>
                                        </div>
                                    </div>

                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.category')</label>
                                    <div class="input-group">
                                        <select name="category_id" id="category_id" class="form-control form-control-lg">
                                            <option value="">Select</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" @if (!empty($service) && $service->category->id == $category->id)
                                                    selected
                                                @endif>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-success" id="add-category" type="button"><i class="fa fa-plus"></i> @lang('app.add')</button>
                                        </div>
                                    </div>

                                </div>
                            </div>                                                    

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.services.time')</label>
                                    <div class="input-group">
                                        <input onkeypress="return isNumberKey(event)" type="number" class="form-control form-control-lg" name="time" @if (!empty($service)) value="{{ $service->time }}" @endif>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary dropdown-toggle" id="time-type-select" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @if (!empty($service))
                                                    @switch($service->time_type)
                                                        @case('minutes')
                                                            @lang('app.minutes')
                                                            @break
                                                        @case('hours')
                                                            @lang('app.hours')
                                                            @break
                                                        {{-- @case('days')
                                                            @lang('app.days')
                                                            @break --}}
                                                        @default
                                                    @endswitch
                                                @else
                                                    @lang('app.minutes')
                                                @endif
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item time_type" data-type="minutes" href="javascript:;">@lang('app.minutes')</a>
                                                <a class="dropdown-item time_type" data-type="hours" href="javascript:;">@lang('app.hours')</a>
                                                {{-- <a class="dropdown-item time_type" data-type="days" href="javascript:;">@lang('app.days')</a> --}}
                                            </div>
                                        </div>

                                        <input type="hidden" id="time-type" name="time_type" value="minutes">

                                    </div>

                                </div>
                            </div>    
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Loyalty Coin</label>
                                    <input type="number" min="0" name="loyalty_point" id="loyalty_point" class="form-control form-control-lg" @if (!empty($service)) value="{{ $service->loyalty_point }}" @endif />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.select') @lang('app.outlet')</label>                               
                                    <select name="outlet_id[]" id="outlet_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple" required>
                                        {{-- <option value="0">All Outlets</option>
                                        @foreach ($outlets as $item)
                                            <option value="{{ $item->id }}" @if(!empty($service) && in_array($item->id, $selectedOutlets)) selected @endif>
                                                {{ $item->outlet_name }}
                                            </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>   

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.assign') Therapist</label>
                                    <select name="employee_ids[]" id="employee_ids" class="form-control form-control-lg select2" multiple="multiple" style="width: 100%">
                                        <option value="0">@lang('app.select') Therapist</option>
                                        @foreach($employees as $employee)
                                            <option @if(!empty($service) && in_array($employee->id, $selectedUsers)) selected @endif value="{{ $employee->id }}">{{ $employee->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="button" class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button" style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i> File Select Or Upload</button>
                                <div id="file-upload-box" >
                                    <div class="row" id="file-dropzone">
                                        <div class="col-md-12">
                                            <div class="dropzone"
                                                    id="file-upload-dropzone">
                                                {{ csrf_field() }}
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-danger">** Recommended image resolution: 160 * 160</h6>
                                {{-- <h6 class="text-danger">@lang('modules.theme.recommendedResolutionNote')</h6> --}}
                                <h6 class="text-danger">@lang('modules.businessServices.defaultImageNotice')</h6>
                            </div>

                            <input type="hidden" name="serviceID" id="serviceID">

                            <div class="col-md-6">
                                <label style="">Most Popular</label>
                                <div class="row" style="margin-top: 5px">
                                    <div class="form-group" style="margin-left: 1em">
                                        <label class="">
                                            <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false"
                                                style="position: relative; margin-right: 5px;">
                                                <input type="checkbox" @if (!empty($service)) {{ ($service->is_popular == 1) ? 'checked' : '' }} @endif name="is_popular" id="is_popular" value="1" class="flat-red columnCheck"
                                                    style="position: absolute; opacity: 0; margin-left: 15px;">
                                                <ins class="iCheck-helper"
                                                    style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px;
                                                        background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                            </div>
                                        </label>
                                        <label class="form-check-label" style="margin-left: 5px;">
                                            Checked If Most Popular
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">                        
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
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    
    <script>

        $(function () {
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
            // })

            @if (!empty($service))
                // $('#discount-type').val('{{ $service->discount_type }}');
                $('#time-type').val('{{ $service->time_type }}');
            @endif

            // calculateDiscountedPrice();

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
            });

            $('#name').keyup(function(e) {
                createSlug($(this).val());
            });

            $('#slug').keyup(function(e) {
                createSlug($(this).val());
            });

            $('.time_type').click(function () {
                var type = $(this).data('type');

                $('#time-type-select').html(type);
                $('#time-type').val(type);
            });

            // $('.discount_type').click(function () {
            //     var type = $(this).data('type');

            //     $('#discount-type-select').html(type);
            //     $('#discount-type').val(type);
            //     calculateDiscountedPrice();
            // });

            $('#add-category').click(function () {
                window.location = '{{ route("admin.categories.create") }}';
            });

            // $('#discount, #price').keyup(function () {
            //     calculateDiscountedPrice();
            // });

            // $('#discount, #price').change(function () {
            //     calculateDiscountedPrice();
            // });

            // $('#discount, #price').on('wheel', function () {
            //     calculateDiscountedPrice();
            // });

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



        // file upload plugins start

        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("#file-upload-dropzone", {
            url: "{{ route('admin.business-services.storeImages') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            init: function () {
                myDropzone = this;
            },
            dictDefaultMessage: "@lang('app.dropzone.defaultMessage')",
            dictRemoveFile: "@lang('app.dropzone.removeFile')"
        });

        myDropzone.on('sending', function(file, xhr, formData) {
            var id = $('#serviceID').val();
            formData.append('service_id', id);
        });

        myDropzone.on('completemultiple', function () {
            var msgs = "@lang('messages.createdSuccessfully')";
            $.showToastr(msgs, 'success');
            window.location.href = '{{ route('admin.business-services.index') }}'
        });

        // file upload plugins end

        function createSlug(value) {
            value = value.replace(/\s\s+/g, ' ');
            let slug = value.split(' ').join('-').toLowerCase();
            slug = slug.replace(/--+/g, '-');
            $('#slug').val(slug);
        }


        $('#save-form').click(function () {
            // Manually update the textarea with CKEditor content
            $('#description').val(ckeditorInstance.getData());

            $.easyAjax({
                url: '{{route('admin.business-services.store')}}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file:true,
                data: $('#createForm').serialize(),
                success: function (response) {
                    if (myDropzone.getQueuedFiles().length > 0) {
                        serviceID = response.serviceID;
                        $('#serviceID').val(response.serviceID);
                        myDropzone.processQueue();
                    }
                    else{
                        var msgs = "@lang('messages.createdSuccessfully')";
                        $.showToastr(msgs, 'success');
                        window.location.href = '{{ route('admin.business-services.index') }}'
                    }
                }
            })
        });


        

        // function calculateDiscountedPrice() {
        //     var price = $('#price').val();
        //     var discount = $('#discount').val();
        //     var discountType = $('#discount-type').val();

        //     if (discountType == 'percent') {
        //         if(discount > 100){
        //             $('#discount').val(100);
        //             discount = 100;
        //         }
        //     }
        //     else {
        //         if (parseInt(discount) > parseInt(price)) {
        //             $('#discount').val(price);
        //             discount = price;
        //         }
        //     }

        //     var discountedPrice = price;

        //     if(discount >= 0 && discount >= '' && price != '' && price > 0){
        //         if(discountType == 'percent'){
        //             discountedPrice = parseFloat(price)-(parseFloat(price)*(parseFloat(discount)/100));
        //         }
        //         else{
        //             discountedPrice = parseFloat(price)-parseFloat(discount);
        //         }
        //     }
        //     if(discount != '' && price != '' && price > 0){
        //         $('#discounted-price').html(discountedPrice.toFixed(2));
        //     }
        //     else {
        //         $('#discounted-price').html('--');
        //     }
        // }


        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
            return true;
        }


        $(document).ready(function () {
            
            $('body').on('change', '#category_id', function() {

                var category_id = $(this).val();
                console.log(category_id);

                $.ajax({
                    type: "get",
                    url: "{{route('admin.categories.get-outlets-by-single-categoryId')}}",
                    data: {category_id: category_id},
                    success: function (response) {
                        console.log(response);

                        $("#outlet_id").html("");

                        if(response.outlets.length > 0)
                        {
                            $("#outlet_id").append(`<option value="0">All Outlets</option>`);

                            $.each(response.outlets, function (key, value) { 
                                 
                                var html = `<option value="${value.id}">${value.outlet_name}</option>`;

                                $("#outlet_id").append(html);
                            });
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    },
                });

            });

        });

    </script>
@endpush
