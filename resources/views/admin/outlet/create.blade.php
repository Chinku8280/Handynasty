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
                <h3 class="card-title">@lang('app.add') @lang('app.outlet')</h3>
            </div>
            <div class="card-body">
                <form role="form" id="createForm" class="ajax-form" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="outlet_name">@lang('app.outletName') </label>
                                <input type="text" class="form-control" name="outlet_name" id="title" value="" placeholder="Enter Outlet Name" autocomplete="off">
                            </div>
                        </div>                        

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputPassword1">@lang('app.outlet') @lang('app.image')</label>
                                <input type="file" id="input-file-now" name="image"
                                            accept=".png,.jpg,.jpeg"
                                            data-default-file="{{ asset('img/no-image.jpg') }}" class="form-control" />
                            </div>

                            <h6 class="text-danger">** Recommended image resolution: 380 * 180</h6>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">@lang('app.outletDescription') </label>
                                <textarea class="form-control description" name="outlet_description" id="description" placeholder="Enter Outlet Description"></textarea>
                            </div>
                        </div>

                        {{-- <div class="col-md-12">
                            <div class="form-group">
                                <label for="iframe_src">@lang('app.outletIframe')</label>
                                <input name="iframe_src" class="form-control">
                            </div>
                        </div> --}}                        

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">@lang('app.address')</label>
                                <input type="text" name="address" class="form-control" placeholder="Outlet Address">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" name="longitude" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" name="latitude" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">@lang('app.phone')</label>
                                <input type="text" name="phone" class="form-control" placeholder="Outlet Phone Number">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Whatsapp Number</label>
                                <input type="text" name="whatsapp_no" class="form-control" placeholder="Outlet Whatsapp Number">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('app.outlet') @lang('modules.settings.openTime')</label>
                                <input type="text" class="form-control" id="open_time" name="open_time" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('app.outlet') @lang('modules.settings.closeTime')</label>
                                <input type="text" class="form-control" id="close_time" name="close_time"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">@lang('app.status')</label>
                                <select name="status" class="form-control">
                                    <option value="active"> @lang('app.active') </option>
                                    <option value="inactive"> @lang('app.inactive') </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" id="save-form" class="btn btn-success btn-light-round">
                            <i class="fa fa-check"></i> @lang('app.save')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer-js')
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>

    <script>
        $(function() {
            // $('.description').summernote({
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
            });

            $('#save-form').click(function() {

                $('#description').val(ckeditorInstance.getData());

                $.easyAjax({
                    url: '{{ route('admin.outlet.store') }}',
                    container: '#createForm',
                    type: "POST",
                    redirect: true,
                    file: true,
                    data: $('#createForm').serialize(),

                })
            });

        });
    </script>
@endpush