<div class="modal-header">
    <h4 class="modal-title">@lang('app.faq') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">            
                <div class="col-md-12">
                    <br>
                    <h6 class="text-uppercase">@lang('app.faq') @lang('app.faqQuestion')</h6>
                    <p>{{ $faq->question }}</p>
                </div>                

                @if(!is_null($faq->answer))
                    <div class="col-md-12">
                        <h6 class="text-uppercase">@lang('app.faqAnswer')</h6>
                        <p>{!! $faq->answer !!} </p>
                    </div>
                @endif

            </div>
    </div>
</div>
