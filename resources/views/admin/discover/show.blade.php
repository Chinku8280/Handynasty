<div class="modal-header">
    <h4 class="modal-title">@lang('app.happening') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
                <div class="col-12">
                    <h6 class="text-uppercase">@lang('app.happening') @lang('app.image')</h6>
                    <img src="{{ asset('user-uploads/happenings/' . $happening->image) }}" class="img img-responsive img-thumbnail" width="100%">
                </div>

                <div class="col-md-12">
                    <br>
                    <h6 class="text-uppercase">@lang('app.happening') @lang('app.title')</h6>
                    <p>{{ $happening->title }}</p>
                </div>                

                @if(!is_null($happening->description))
                    <div class="col-md-12">
                        <h6 class="text-uppercase">@lang('app.happening') @lang('app.description')</h6>
                        <p>{!! $happening->description !!} </p>
                    </div>
                @endif

                <div class="col-md-5">
                    <h6 class="text-uppercase">@lang('app.happening') @lang('app.percentOff')</h6>
                    <p>{{ $happening->off_percentage }}% OFF</p>
                </div>

            </div>
    </div>
</div>
