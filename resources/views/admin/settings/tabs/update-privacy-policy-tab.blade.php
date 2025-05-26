<h4>@lang('menu.updatePrivacy')</h4>
<br>
<form class="form-horizontal ajax-form" id="privacy-policy-update-form" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="privacy_policy">@lang('menu.updatePrivacy')</label>
                <textarea type="text" id="privacy-policy-textarea" class="form-control" name="privacy_policy">{{ $privacypolicy->privacy_policy }}</textarea>
            </div>

            <div class="form-group">
                <button id="update-privacy-policy" type="button" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
        </div>
    </div>
</form>