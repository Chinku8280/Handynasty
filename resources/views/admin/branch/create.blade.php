@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.add') @lang('app.branch')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST"
                        onkeydown="return event.key != 'Enter';">
                        @csrf

                        <input type="hidden" name="redirect_url" value="{{ url()->previous() }}">

                        <div class="row">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.branch') @lang('app.name')</label>
                                    <input type="text" class="form-control form-control-lg" name="name"
                                        autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.email')</label>
                                    <input type="text" class="form-control form-control-lg" name="email"
                                        autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.password')</label>
                                    <input type="password" class="form-control form-control-lg" name="password"
                                        autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.postalCode')</label>
                                    <input type="postalCode" class="form-control form-control-lg" name="postalCode"
                                        id="postalCode" onkeyup="getAddress()" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.address')</label>
                                    <input type="text" class="form-control form-control-lg" name="address" id="address"
                                        autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label> @lang('app.mobile')</label>
                                    <input type="text" class="form-control form-control-lg" name="mobile"
                                        autocomplete="off">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> @lang('app.openingtime')</label>
                                            <input type="time" class="form-control form-control-lg" name="openingTime"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> @lang('app.closingtime')</label>
                                            <input type="time" class="form-control form-control-lg" name="closingTime"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">@lang('app.image')</label>
                                        <div class="card">
                                            <div class="card-body">
                                                <input type="file" id="input-file-now" name="image"
                                                    accept=".png,.jpg,.jpeg" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="form-group"> --}}
                                {{-- <label for="exampleInputPassword1">@lang('app.image')</label> --}}
                                {{-- <div class="card"> --}}
                                {{-- <div class="card-body"> --}}
                                {{-- <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="dropify" --}}
                                {{-- /> --}}
                                {{-- </div> --}}
                                {{-- </div> --}}
                                {{-- </div> --}}

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

        $('#save-form').click(function() {

            $.easyAjax({
                url: '{{ route('admin.branches.store') }}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file: true,
                data: $('#createForm').serialize()
            })
        });

        function getAddress() {

            var get_postal = $("#postalCode").val();

            $.ajax({
                // url: "https://developers.onemap.sg/commonapi/search?searchVal=" + get_postal +
                //     "&returnGeom=Y&getAddrDetails=Y",

                url: "https://www.onemap.gov.sg/api/common/elastic/search?searchVal=" + get_postal + "&returnGeom=Y&getAddrDetails=Y&pageNum=1",

                success: function(JSON) {
                    if (JSON && JSON.results && JSON.results[0]) {
                        $("#address").val(JSON.results[0].ADDRESS);
                    } else {

                        $("#address").val("Address not found");
                    }
                }
            });
        }
    </script>
@endpush
