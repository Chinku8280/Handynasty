<div class="modal-header">
    <h4 class="modal-title">@lang('app.package') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.package') @lang('app.name')</h6>
                <p>{{ $package->title }}</p>
            </div>
          
         
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.amount')</h6>
                <p>
                    @if (!is_null($package->amount))
                        {{ $settings->currency->currency_symbol }}{{ $package->amount }}
                    @else
                        -
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.coin')</h6>
                <p>
                    @if (!is_null($package->coin))
                        {{ $package->coin }} 
                    @else
                        -
                    @endif
                </p>
            </div>           
            @if (!is_null($package->description))
                <div class="col-md-12">
                    <h6 class="text-uppercase">@lang('app.description')</h6>
                    <p>{!! $package->description !!} </p>
                </div>
            @endif
        </div>
    </div>
</div>
