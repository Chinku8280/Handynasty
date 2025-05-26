@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('app.category')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm"  class="ajax-form" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $category->id }}">
                        <div class="row">
                            <div class="col-md">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.category') @lang('app.name')</label>
                                    <input type="text" name="name" id="name" class="form-control form-control-lg" value="{{ $category->name }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>@lang('app.category') @lang('app.slug')</label>
                                    <input type="text" name="slug" id="slug" class="form-control form-control-lg" value="{{ $category->slug }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.select') @lang('app.outlet')</label>                             
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

                            <div class="col-md-12">
                                @if (!empty($category->category_image_url))
                                    <div class="mt-3">
                                        <img src="{{ $category->category_image_url  }}" width="150" class="img-thumbnail">
                                    </div>
                                @endif
                                
                                <div class="form-group mt-3">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>                               
                                    <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="form-control"
                                                   data-default-file="{{ $category->category_image_url  }}"/>                                 
                                </div>

                                <h6 class="text-danger">** Recommended image resolution: 160 * 160</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('app.status')</label>
                                    <select name="status" id="" class="form-control form-control-lg">
                                        <option
                                                @if($category->status == 'active') selected @endif
                                                value="active">@lang('app.active')</option>
                                        <option
                                                @if($category->status == 'deactive') selected @endif
                                        value="deactive">@lang('app.deactive')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label style="">Loyalty Program</label>
                                <div class="row" style="margin-top: 5px">
                                    <div class="form-group" style="margin-left: 1em">
                                        <label class="">
                                            <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false"
                                                style="position: relative; margin-right: 5px;">
                                                <input type="checkbox" {{ ($category->is_loyalty_program == 1) ? 'checked' : '' }} name="is_loyalty_program" id="is_loyalty_program" value="1" class="flat-red columnCheck"
                                                    style="position: absolute; opacity: 0; margin-left: 15px;">
                                                <ins class="iCheck-helper"
                                                    style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px;
                                                        background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                            </div>
                                        </label>
                                        <label class="form-check-label" style="margin-left: 5px;">
                                            Checked If Loyalty Program
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

    <script>
        function createSlug(value) {
            value = value.replace(/\s\s+/g, ' ');
            let slug = value.split(' ').join('-').toLowerCase();
            slug = slug.replace(/--+/g, '-');
            $('#slug').val(slug);
        }

        $(document).ready(function () {

            $('.dropify').dropify({
                messages: {
                    default: '@lang("app.dragDrop")',
                    replace: '@lang("app.dragDropReplace")',
                    remove: '@lang("app.remove")',
                    error: '@lang('app.largeFile')'
                }
            });

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

            $('#save-form').click(function () {

                $.easyAjax({
                    url: '{{route('admin.categories.update', $category->id)}}',
                    container: '#createForm',
                    type: "POST",
                    redirect: true,
                    file:true,
                    data: $('#createForm').serialize()
                })
            });

        });
    </script>

@endpush
