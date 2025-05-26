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
                
                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.happening') @lang('app.StartTime')</h6>
                    <p>{{ !empty($happening->start_date_time) ? (date('d-m-Y h:i A', strtotime($happening->start_date_time))) : '' }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.happening') @lang('app.endTime')</h6>
                    <p>{{ !empty($happening->end_date_time) ? (date('d-m-Y h:i A', strtotime($happening->end_date_time))) : '' }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.happening') @lang('app.age')</h6>
                    <p>{{ $happening->min_age }} - {{ $happening->max_age }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.happening') @lang('app.gender')</h6>
                    <p>{{ $happening->gender }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-uppercase">@lang('app.happening') @lang('app.outlet')</h6>
                    <p>{{ $happening->outlet_name ?? '' }}</p>
                </div>

                @if(!is_null($happening->description))
                    <div class="col-md-12">
                        <h6 class="text-uppercase">@lang('app.happening') @lang('app.description')</h6>
                        <p>{!! $happening->description !!} </p>
                    </div>
                @endif

            </div>
    </div>
</div>