<h4>@lang('menu.T&C')</h4>
<br>
<form class="form-horizontal ajax-form" id="terms-condition-update-form" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="terms_condition">@lang('menu.updatePrivacy')</label>
                <textarea type="text" class="form-control" name="terms_condition" id="terms_condition_description">{{ $terms->terms_condition }}</textarea>
            </div>

            <div class="form-group">
                <button id="update-terms-condition" type="button" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
        </div>
    </div>
</form>