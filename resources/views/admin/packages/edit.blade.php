@extends('layouts.master')

@push('head-css')
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
<style>
    .collapse.in{
       display: block;
   }
   
   input::-webkit-outer-spin-button,
   input::-webkit-inner-spin-button
   {
   -webkit-appearance: none;
   margin: 0;
   }

   /* Firefox */
   input[type=number]
   {
   -moz-appearance: textfield;
   }
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('menu.package')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="editForm"  class="ajax-form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.package') @lang('app.code') </label>
                                    <input type="text" class="form-control" name="title" value="{{ $package->title }}" autocomplete="off">
                                </div>
                            </div>   
                       
                            <div class="col-md-4">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.amount')</label>
                                    <input type="number" class="form-control" name="amount"  value="{{ $package->amount }}">                                  
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.coin')</label>
                                    <input type="number" class="form-control" name="coin"  value="{{ $package->coin }}">                                 
                                </div>
                            </div>                      
                        
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">@lang('app.status')</label>
                                    <select name="status" class="form-control">
                                        <option @if($package->status == 'active') selected @endif value="active"> @lang('app.active') </option>
                                        <option @if($package->status == 'inactive') selected @endif value="inactive"> @lang('app.inactive') </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{!! $package->description !!} </textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                            class="fa fa-check"></i> @lang('app.save')</button>
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
<script src="{{ asset('assets/js/collaps.js') }}"></script>
<script src="{{ asset('assets/js/transition.js') }}"></script>
<script>
      let startDate = '';
        let endDate = '';
        let startTime = '';
        let endTime = '';
  
    $('#customers').select2();

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
    $('#save-form').click(function () {
    $.easyAjax({
        url: '{{ route('admin.packages.update', $package->id) }}',
        container: '#editForm',
        type: "POST",
        redirect: true,
        data: $('#editForm').serialize(),
    });
});

    function convert(str) 
    {
        var date = new Date(str);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        hours = ("0" + hours).slice(-2);
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }

</script>

@endpush
