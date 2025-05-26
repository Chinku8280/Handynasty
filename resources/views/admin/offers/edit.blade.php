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
                    <h3 class="card-title">@lang('app.edit') @lang('menu.offer')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="editForm" class="ajax-form" method="POST">
                        @csrf
                        <span id="put_method"></span>
                        {{-- @method('PUT') --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.title') </label>
                                    <input type="text" class="form-control" name="title" id="title"
                                        value="{{ $offer->title }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-4" id="branch_div">
                                <div class="form-group">
                                    <label>@lang('app.branch')</label>
                                    <select name="branch_id" id="branch_id" class="form-control form-control-lg select2"
                                        style="width: 100%">
                                        <option value="">@lang('app.selectBranch')</option>
                                        @foreach ($branches as $branchOption)
                                            <option value="{{ $branchOption->id }}"
                                                {{ $offer->branch_id === $branchOption->id ? 'selected' : '' }}>
                                                {{ $branchOption->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.max_person')</label>
                                    <input type="number" class="form-control" name="max_person"
                                        value="{{ $offer->max_person }}" min="0">
                                    <span class="help-block">@lang('messages.max_person')</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.discount') @lang('app.discount')</label>
                                    <input type="number" class="form-control checkAmount" name="discount" id="discount"
                                        value="{{ $offer->discount }}">
                                </div>
                            </div>



                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.StartTime')</label>
                                    <input type="datetime-local" class="form-control" name="start_date_time"
                                        id="start_date_time" value="{{ $offer->start_date_time }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.endTime')</label>
                                    <input type="datetime-local" class="form-control" name="end_date_time"
                                        id="end_date_time" value="{{ $offer->end_date_time }}">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>@lang('app.age_range')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="min_age" placeholder="Min Age" min="0" value="{{ $offer->min_age }}">
                                        <span class="input-group-addon">&nbsp; to &nbsp;</span>
                                        <input type="number" class="form-control" name="max_age" placeholder="Max Age" min="0" value="{{ $offer->max_age }}">
                                    </div>
                                </div>
                            </div>                            
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.select') @lang('app.gender')</label>
                                    <select name="gender" class="form-control">
                                        <option value="">Select Gender</option>
                                        <option value="male" @if($offer->gender == 'male') selected @endif>Male</option>
                                        <option value="female" @if($offer->gender == 'female') selected @endif>Female</option>
                                        <option value="other" @if($offer->gender == 'other') selected @endif>Other</option>
                                    </select>
                                </div>
                            </div>                            

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">@lang('app.status')</label>
                                    <select name="status" class="form-control">
                                        <option @if ($offer->status == 'active') selected @endif value="active">
                                            @lang('app.active') </option>
                                        <option @if ($offer->status == 'inactive') selected @endif value="inactive">
                                            @lang('app.inactive') </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{{ $offer->description }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mt-3">
                                    <img src="{{ asset('user-uploads/offer/' . $offer->image) }}" width="150" class="img-thumbnail">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="feature_image"
                                                accept=".png,.jpg,.jpeg"
                                                data-default-file="{{ asset('user-uploads/offer/' . $offer->image) }}"
                                                class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="button" id="save-form" class="btn btn-success btn-light-round">
                                    <i class="fa fa-check"></i> @lang('app.save')
                                </button>
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
            $('#offer_startTime').val(convert(e.date));
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
            $('#offer_endTime').val(convert(e.date));
        });

        $(function() {
            $('#description').summernote({
                dialogsInBody: true,
                height: 300,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ["view", ["fullscreen"]]
                ]
            });
        });

        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
        });

        $('#save-form').click(function() {
            $('#put_method').html(`@method('PUT')`);
            $.easyAjax({
                url: '{{ route('admin.offers.update', $offer->id) }}',
                container: '#editForm',
                type: "POST",
                redirect: true,
                data: $('#editForm').serialize(),
                file: true
            });
            $('#put_method').html('');
        });


        $(function() {
            moment.locale('{{ $settings->locale }}');
            $('input[name="applied_between_dates"]').daterangepicker({
                timePicker: true,
                minDate: moment().startOf('hour'),
                autoUpdateInput: false,
            });
        });

        $('input[name="applied_between_dates"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('{{ $date_picker_format }} {{ $time_picker_format }}') + '--' +
                picker.endDate.format('{{ $date_picker_format }} {{ $time_picker_format }}'));
            $('#offer_startDate').val(picker.startDate.format('YYYY-MM-DD') + ' ' + convert(picker.startDate));
            $('#offer_endDate').val(picker.endDate.format('YYYY-MM-DD') + ' ' + convert(picker.endDate));
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
    </script>
@endpush
