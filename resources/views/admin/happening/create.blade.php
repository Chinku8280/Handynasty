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
                <h3 class="card-title">@lang('app.add') @lang('app.happening')</h3>
            </div>
            <div class="card-body">
                <form role="form" id="createForm" class="ajax-form" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.title') </label>
                                <input type="text" class="form-control" name="title" id="title" value=""
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputPassword1">@lang('app.image')</label>
                                <input type="file" id="input-file-now" name="image"
                                            accept=".png,.jpg,.jpeg"
                                            data-default-file="{{ asset('img/no-image.jpg') }}" class="form-control" />
                            </div>

                            <h6 class="text-danger">** Recommended image resolution: 250 * 150</h6>
                        </div>

                        <div class="col-md-6">
                            <!-- text input -->
                            <div class="form-group">
                                <label>@lang('app.StartTime')</label>
                                <input type="text" class="form-control" id="start_time" name="start_date_time"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- text input -->
                            <div class="form-group">
                                <label>@lang('app.endTime')</label>
                                <input type="text" class="form-control" id="end_time" name="end_date_time"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer Age Eligibility</label>    
                                
                                <div class="form-inline">
                                    <input type="number" min="0" name="min_age" id="min_age" class="form-control mr-2" value="18" required>
                                    <span>to</span>
                                    <input type="number" min="0" name="max_age" id="max_age" class="form-control ml-2" value="60" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer Gender Eligibility</label>
                                <select name="gender[]" id="gender" class="form-control form-control-lg select2"
                                    style="width: 100%" multiple="multiple" required>
                                    <option value="all" selected>All</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" id="outlet_div">
                            <div class="form-group">
                                <label>@lang('app.outlet')</label>
                                <select name="outlet_id[]" id="outlet_id" class="form-control form-control-lg select2"
                                    style="width: 100%" multiple="multiple" required>
                                    <option value="0">All Outlets</option>
                                    @foreach ($outlets as $item)
                                        <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">@lang('app.description')</label>
                                <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">@lang('app.status')</label>
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
        function convert(str) 
        {
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

        let startDate = '';
        let endDate = '';
        let startTime = '';
        let endTime = '';

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

        $(function() {
            // $('#description').summernote({
            //     dialogsInBody: true,
            //     height: 300,
            //     toolbar: [
            //         // [groupName, [list of button]]
            //         ['style', ['bold', 'italic', 'underline', 'clear']],
            //         ['font', ['strikethrough']],
            //         // ['fontsize', ['fontsize']],
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


            // $('#title').keyup(function(e) {
            //     createSlug($(this).val());
            // });


            $('#save-form').click(function() {

                var min_age = parseInt($("#min_age").val());
                var max_age = parseInt($("#max_age").val());

                // console.log(min_age);
                // console.log(max_age);

                if(min_age <= max_age)
                {     
                    // Manually update the textarea with CKEditor content
                    $('#description').val(ckeditorInstance.getData());

                    $.easyAjax({
                        url: '{{ route('admin.happening.store') }}',
                        container: '#createForm',
                        type: "POST",
                        redirect: true,
                        file: true,
                        data: $('#createForm').serialize() + '&startDate=' + startDate + '&endDate=' + endDate +
                            '&startTime=' + startTime + '&endTime=' + endTime,               
                    });
                }
                else
                {
                    $.showToastr("Minimum Age is not greater than Maximum age", 'error');
                }
            });

        });
    </script>
@endpush