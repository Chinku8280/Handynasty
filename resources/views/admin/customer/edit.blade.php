@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('app.customer')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm"  class="ajax-form" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="row">
                                    {{-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.prefix')</label>
                                            <input type="text" class="form-control form-control-lg" name="prefix"
                                                value="{{ ucwords($customer->prefix) }}">
                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.fname')<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" name="fname"
                                                value="{{ ucwords($customer->fname) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.lname')</label>
                                            <input type="text" class="form-control form-control-lg" name="lname"
                                                value="{{ ucwords($customer->lname) }}">
                                        </div>
                                    </div> --}}

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('app.name') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" name="full_name"
                                                value="{{ ucwords($customer->name) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- text input -->
                                <div class="row">
                                    <div class="col-md-4">                                        
                                        <div class="form-group">
                                            <label for="gender">@lang('app.gender')</label>
                                            <select class="form-control form-control-lg" name="gender" id="gender">
                                                <option value="male" {{ $customer->gender === 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ $customer->gender === 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ $customer->gender === 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>                                                                            
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.email') <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control form-control-lg" name="email"
                                                value="{{ ucwords($customer->email) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.password')</label>
                                            <input type="password" class="form-control form-control-lg" name="password">
                                            <span class="help-block">@lang('messages.leaveBlank')</span>
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
                                                    {{-- <option value="{{ $item->id }}"
                                                        {{ $customer->outlet_id === $item->id ? 'selected' : '' }}>
                                                        {{ $item->outlet_name }}
                                                    </option> --}}

                                                    @if (Session::has('outlet_id'))
                                                        @if (!empty($customer->outlet_id))
                                                            @if ($customer->outlet_id == $item->id)
                                                                <option value="{{ $item->id }}" selected>{{ $item->outlet_name }}</option>
                                                            @else
                                                                <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>
                                                            @endif
                                                        @else
                                                            @if (Session::get('outlet_id') == $item->id)
                                                                <option value="{{ $item->id }}" selected>{{ $item->outlet_name }}</option>
                                                            @else
                                                                <option value="{{ $item->id }}">{{ $item->outlet_name }}</option>                                            
                                                            @endif
                                                        @endif

                                                    @elseif (!empty($customer->outlet_id))
                                                        @if ($customer->outlet_id == $item->id)
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
                                            <input type="text" readonly class="form-control form-control-lg"
                                                name="mobile" value="{{ $customer->mobile }}" required>
                                        </div>
                                        <div class="col-md-3 ">
                                            @if ($customer->mobile_verified)
                                                <span class="text-success">
                                                    @lang('app.verified')
                                                </span>
                                            @else
                                                <span class="text-danger">
                                                    @lang('app.notVerified')
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('app.dob')</label>
                                            <input type="date" class="form-control form-control-lg" name="dob"
                                                value="{{ ucwords($customer->dob) }}">
                                        </div>
                                    </div>                                   
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">@lang('app.status')</label>
                                            <select name="status" class="form-control">
                                                <option @if ($customer->status == 'active') selected @endif value="active">
                                                    @lang('app.active') </option>
                                                <option @if ($customer->status == 'inactive') selected @endif value="inactive">
                                                    @lang('app.inactive') </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">                                  
                                    <div class="col-md-4">
                                        @if (!empty($customer->user_image_url))
                                            <div class="mt-3">
                                                <img src="{{ $customer->user_image_url }}" width="150" class="img-thumbnail">
                                            </div>
                                        @endif
                                        
                                        <div class="form-group mt-3">
                                            <label for="exampleInputPassword1">@lang('app.image')</label>                                      
                                            <input type="file" id="input-file-now" name="image"
                                                        accept=".png,.jpg,.jpeg"
                                                        data-default-file="{{ $customer->user_image_url }}" class="form-control" />                                                                        
                                        </div>

                                        <h6 class="text-danger">** Recommended image resolution: 225 * 225</h6>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-primary btn-light-round"><i class="fa fa-check"></i> @lang('app.update')</button>
                                </div>

                            </div>
                            {{-- <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.name')</label>
                                    <input type="text" class="form-control form-control-lg" name="name" value="{{ ucwords($customer->name) }}">
                                </div>

                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.email')</label>
                                    <input type="email" class="form-control form-control-lg" name="email" value="{{ $customer->email }}">
                                </div>

                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.password')</label>
                                    <input type="password" class="form-control form-control-lg" name="password">
                                    <span class="help-block">@lang('messages.leaveBlank')</span>
                                </div>

                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.mobile')</label>
                                    <div class="row">
                                        <div class="col-md-11">
                                            <input type="text" readonly class="form-control form-control-lg" name="mobile" value="{{ $customer->formatted_mobile }}">
                                        </div>
                                        <div class="col-md-1 text-center d-flex justify-content-center align-items-center">
                                            @if ($customer->mobile_verified)
                                                <span class="text-success">
                                                    @lang('app.verified')
                                                </span>
                                            @else
                                                <span class="text-danger">
                                                    @lang('app.notVerified')
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" data-default-file="{{ $customer->user_image_url  }}" class="dropify"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                                class="fa fa-check"></i> @lang('app.save')</button>
                                </div>

                            </div> --}}
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
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('#save-form').click(function () {

            $.easyAjax({
                url: '{{route('admin.customers.update', $customer->id)}}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file:true
            })
        });
    </script>

@endpush
