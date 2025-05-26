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
                <h3 class="card-title">@lang('app.edit') @lang('app.happening')</h3>
            </div>
            <div class="card-body">
                <form role="form" id="editForm" class="ajax-form" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('app.title') </label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ $happening->title }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mt-3">
                                <img src="{{ asset('user-uploads/happenings/' . $happening->image) }}" width="170" class="img-thumbnail" alt="">
                            </div>
                            <div class="form-group mt-3">
                                <label for="exampleInputPassword1">@lang('app.image')</label>
                                <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">@lang('app.description')</label>
                                <textarea name="description" id="description" cols="30"
                                    class="form-control-lg form-control"
                                    rows="4">{{ $happening->description }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>@lang('app.percentOff') </label>
                                <input type="text" class="form-control" name="off_percentage" value="{{ $happening->off_percentage }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">@lang('app.status')</label>
                                <select name="status" class="form-control">
                                    <option value="active" {{ $happening->status == 'active' ? 'selected' : '' }}>
                                        @lang('app.active') </option>
                                    <option value="inactive"
                                        {{ $happening->status == 'inactive' ? 'selected' : '' }}>
                                        @lang('app.inactive') </option>
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

    <script>
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

        $('#save-form').click(function() {
            $('#put_method').html(`@method('PUT')`);
            $.easyAjax({
                url: '{{ route('admin.discover.update', $happening->id) }}',
                container: '#editForm',
                type: "POST",
                redirect: true,
                data: $('#editForm').serialize(),
                file: true
            });
            $('#put_method').html('');
        });
    </script>
@endpush