<div class="modal-header">
    <h4 class="modal-title">@lang('app.feedback') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">        
                <div class="col-md-6">
                    <br>
                    <h6 class="text-uppercase">@lang('app.name')</h6>
                    <p>{{ $feedback->fullname }}</p>
                </div>     
                {{-- <div class="col-md-6">
                    <br>
                    <h6 class="text-uppercase">@lang('app.fname')</h6>
                    <p>{{ $feedback->first_name }}</p>
                </div> 
                <div class="col-md-6">
                    <br>
                    <h6 class="text-uppercase">@lang('app.fname')</h6>
                    <p>{{ $feedback->last_name }}</p>
                </div> --}}
                <div class="col-md-6">
                    <br>
                    <h6 class="text-uppercase">@lang('app.country')</h6>
                    <p>{{ $feedback->country }}</p>
                </div> 
                <div class="col-md-6">
                    <br>
                    <h6 class="text-uppercase">@lang('app.email')</h6>
                    <p>{{ $feedback->email }}</p>
                </div>
                <div class="col-md-12">
                    <br>
                    <h6 class="text-uppercase">@lang('app.message')</h6>
                    <p>{{ $feedback->message }}</p>
                </div>
                <div class="col-md-6">
                    <br>
                    <h6 class="text-uppercase">Feedback Created On:</h6>
                    <p>{{ $feedback->created_at }}</p>
                </div>

                <div class="col-md-12">
                    <h6 class="text-uppercase"></h6>
                    <p></p>
                </div>

            </div>
    </div>
</div>
