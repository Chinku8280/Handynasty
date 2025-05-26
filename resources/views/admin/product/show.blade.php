<div class="modal-header">
    <h4 class="modal-title">Product @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-12">
                <img src="{{$product->product_image_url}}" class="img img-responsive img-thumbnail" width="100%">
            </div>

            <div class="col-md-12">
                <h6 class="text-uppercase">Product @lang('app.name')</h6>
                <p>{{ $product->product_name }}</p>
            </div>
          
            <div class="col-md-6">
                <h6 class="text-uppercase">@lang('app.price')</h6>
                <p>
                    @if (!is_null($product->price))
                        {{ $settings->currency->currency_symbol }}{{ $product->price }}
                    @else
                        -
                    @endif
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="text-uppercase">Loyalty Coin</h6>
                <p>{{$product->loyalty_point}}</p>
            </div>

            @if (!is_null($product->short_description))
                <div class="col-md-12">
                    <h6 class="text-uppercase">Short @lang('app.description')</h6>
                    <p>{!! $product->short_description !!} </p>
                </div>
            @endif
            
            @if (!is_null($product->description))
                <div class="col-md-12">
                    <h6 class="text-uppercase">@lang('app.description')</h6>
                    <p>{!! $product->description !!} </p>
                </div>
            @endif
        </div>
    </div>
</div>
