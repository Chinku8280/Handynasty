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
                    <h3 class="card-title">@lang('app.add') @lang('menu.offer')</h3>
                </div>
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.title') </label>
                                    <input type="text" class="form-control" name="title" id="title" value=""
                                        autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-4" id="branch_div">
                                <div class="form-group">
                                    <label>@lang('app.branch')</label>
                                    <select name="branch_id" id="branch_id" class="form-control form-control-lg select2"
                                        style="width: 100%">
                                        <option value="">@lang('app.selectBranch')</option>
                                        @foreach ($branches as $branches)
                                            <option value="{{ $branches->id }}">{{ $branches->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.max_person')</label>
                                    <input type="number" class="form-control" name="max_person" min="0">
                                    <span class="help-block">@lang('messages.max_person')</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.discount')</label>
                                    <input type="number" class="form-control checkAmount" name="discount" id="discount">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.StartTime')</label>
                                    <input type="datetime-local" class="form-control" name="start_date_time"
                                        id="start_date_time">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.endTime')</label>
                                    <input type="datetime-local" class="form-control" name="end_date_time"
                                        id="end_date_time">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>@lang('app.age_range')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="min_age" placeholder="Min Age" min="0">
                                        <span class="input-group-addon">&nbsp; to &nbsp;</span>
                                        <input type="number" class="form-control" name="max_age" placeholder="Max Age" min="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.select') @lang('app.gender')</label>
                                    <select name="gender" class="form-control">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">@lang('app.status')</label>
                                    <select name="status" class="form-control">
                                        <option value="active"> @lang('app.active') </option>
                                        <option value="inactive"> @lang('app.inactive') </option>
                                    </select>
                                </div>
                            </div>

                            {{-- 
                            <div class="col-md-12">
                                <label style="">@lang('app.dayForApply') </label>
                                <div class="row" style="margin-top: 5px">
                                    @forelse($days as $day)
                                        <div class="form-group" style="margin-left: 1em">
                                            <label class="">
                                                <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false"
                                                    style="position: relative; margin-right: 5px;">
                                                    <input type="checkbox" checked value="{{ $day }}"
                                                        name="days[]" class="flat-red columnCheck"
                                                        style="position: absolute; opacity: 0; margin-left: 15px;">
                                                    <ins class="iCheck-helper"
                                                        style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                </div>
                                                @lang('app.' . strtolower($day))
                                            </label>
                                        </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div> --}}

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="feature_image"
                                                accept=".png,.jpg,.jpeg" data-default-file="{{ asset('img/no-image.jpg') }}"
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

            $.easyAjax({
                url: '{{ route('admin.offers.store') }}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file: true,
                data: $('#createForm').serialize(),

            })
        });

         </script>
@endpush
