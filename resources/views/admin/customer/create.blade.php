@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.create') @lang('app.customer')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="row">
                                    {{-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.prefix')</label>
                                            <input type="text" class="form-control form-control-lg" name="prefix">
                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.fname')<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" name="fname" required>                                            
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.lname')</label>
                                            <input type="text" class="form-control form-control-lg" name="lname">
                                        </div>
                                    </div> --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('app.name') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" name="full_name" required>                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender">@lang('app.gender')</label>
                                            <select class="form-control form-control-lg" name="gender" id="gender" class="form-control">
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>                                         
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.email') <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control form-control-lg" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.password') <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control form-control-lg" name="password" required>
                                        </div>
                                    </div>
                                </div>

                                

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.outlet')</label>
                                            <select class="form-control form-control-lg" name="outlet_id">
                                                <option value="">Select</option>
                                                @foreach ($outlet as $item)
                                                    @if (Session::has('outlet_id'))
                                                        @if (Session::get('outlet_id') == $item->id)
                                                            <option value="{{ $item->id }}" selected>{{ $item->outlet_name }}</option>
                                                        @else
                                                            <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>                                            
                                                        @endif
                                                    @else
                                                        <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>
                                                    @endif                      
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.mobile') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" name="mobile" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.dob')</label>
                                            <input type="date" class="form-control form-control-lg" name="dob">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">                                   
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">@lang('app.image')</label>                                   
                                            <input type="file" id="input-file-now" name="image"
                                                        accept=".png,.jpg,.jpeg" class="form-control" />                                       
                                        </div>
    
                                        <h6 class="text-danger">** Recommended image resolution: 225 * 225</h6>
                                    </div>
                                </div>

                               

                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-primary btn-light-round"><i class="fa fa-check"></i> @lang('app.save')</button>
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
                url: '{{ route('admin.customers.store') }}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>
@endpush
