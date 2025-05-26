<div class="modal-header">
    <h4 class="modal-title">@lang('app.outlet') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-6">
                <h6 class="text-uppercase">@lang('app.outlet') @lang('app.image')</h6>
                <img src="{{ asset('user-uploads/outlet_images/' . $outlet->image) }}" class="img img-responsive img-thumbnail" width="100%">
            </div>

            <div class="col-md-6">
                <br>
                <h6 class="text-uppercase">@lang('app.outlet') @lang('app.outletName')</h6>
                <p>{{ $outlet->outlet_name }}</p>

                <br>
                <h6 class="text-uppercase">@lang('app.outlet') @lang('app.outletDescription')</h6>
                <p>{!! $outlet->outlet_description !!}</p>

                <br>
                <h6 class="text-uppercase">@lang('app.outlet') @lang('app.address')</h6>
                <p>{{ $outlet->address }}</p>

                <br>
                <h6 class="text-uppercase">Latitude</h6>
                <p>{{ $outlet->latitude }}</p>

                <br>
                <h6 class="text-uppercase">Longitude</h6>
                <p>{{ $outlet->longitude }}</p>

                <br>
                <h6 class="text-uppercase">@lang('app.outlet') @lang('app.phone')</h6>
                <p>{{ $outlet->phone }}</p>

                <br>
                <h6 class="text-uppercase">@lang('app.outlet') Whatsapp Number</h6>
                <p>{{ $outlet->whatsapp_no }}</p>

                <br>
                <h6 class="text-uppercase">@lang('app.outlet') @lang('modules.settings.openTime')</h6>
                <p>{{ $outlet->open_time }}</p>

                <br>
                <h6 class="text-uppercase">@lang('app.outlet') @lang('modules.settings.closeTime')</h6>
                <p>{{ $outlet->close_time }}</p>

                <br>
                <h6 class="text-uppercase">@lang('app.outlet') Services</h6>
                <p>{{ $outlet->outlet_services_name }}</p>
            </div>            
        </div>
    </div>
</div>