@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('app.branch')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    
                    <form role="form" id="createForm"  class="ajax-form" method="POST" onkeydown="return event.key != 'Enter';">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.branch') @lang('app.name')</label>
                                    <input type="text" class="form-control form-control-lg" name="name" value="{{ $location->name }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.email')</label>
                                    <input type="text" class="form-control form-control-lg" value="{{ $location->email }}" name="email" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.password')</label>
                                    <input type="password" class="form-control form-control-lg" name="password" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.postalCode')</label>
                                    <input type="postalCode" class="form-control form-control-lg" name="postalCode" value="{{ $location->postalCode }}"
                                        id="postalCode" onkeyup="getAddress()" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.address')</label>
                                    <input type="text" class="form-control form-control-lg" name="address" id="address" value="{{ $location->address }}"
                                        autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.mobile')</label>
                                    <input type="text" class="form-control form-control-lg" name="mobile" value="{{ $location->mobile }}"
                                        autocomplete="off">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> @lang('app.openingtime')</label>
                                            <input type="time" class="form-control form-control-lg" name="openingTime" value="{{ $location->openingTime }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> @lang('app.closingtime')</label>
                                            <input type="time" class="form-control form-control-lg" name="closingTime" value="{{ $location->closingTime }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mt-3">
                                        <img src="{{ $location->branch_image_url }}" width="150" class="img-thumbnail">
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="exampleInputPassword1">@lang('app.image')</label>
                                        <div class="card">
                                            <div class="card-body">
                                                <input type="file" id="input-file-now" name="image"
                                                    accept=".png,.jpg,.jpeg" class="form-control"
                                                    data-default-file="{{ $location->branch_image_url }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

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

    <script>
          $('.dropify').dropify({
            messages: {
                default: '@lang('app.dragDrop')',
                replace: '@lang('app.dragDropReplace')',
                remove: '@lang('app.remove')',
                error: '@lang('app.largeFile')'
            }
        });

        $('#save-form').click(function () {
        var locationId = {{ $location->id }}; 
        var updateUrl = '{{ route('admin.branches.update', ':id') }}';
        updateUrl = updateUrl.replace(':id', locationId);

        $.easyAjax({
            url: updateUrl,
            container: '#createForm',
            type: "POST",
            redirect: true,
            file: true,
            data: $('#createForm').serialize()
        });
    });

    </script>

@endpush
