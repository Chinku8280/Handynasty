<div class="modal-header">
    <h4 class="modal-title">@lang('app.branch') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-uppercase">@lang('app.branch') @lang('app.name')</h6>
                <p>{{ $branch->name }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.email')</h6>
                <p>{{ $branch->email }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.url')</h6>
                <p> {{ $branch->url }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.postalCode')</h6>
                <p> {{ $branch->postalCode }}
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.address')</h6>
                <p>{{ $branch->address }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.mobile')</h6>
                <p>
                    {{ $branch->mobile }}
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.openingtime')</h6>
                <p>    {{ $branch->openingTime }}     </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.closingtime')</h6>
                <p>    {{ $branch->closingTime }}     </p>
            </div>
            
        </div>
    </div>
</div>
