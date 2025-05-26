<h4>Loyalty Coin Settings</h4>
<br>
<form class="form-horizontal" id="loyalty_point_settings_form" method="POST">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="loyalty_points_expired_days" class="control-label">Loyalty Coin Expired days</label>

                <input type="number" class="form-control form-control-lg"
                       id="loyalty_points_expired_days" name="loyalty_points_expired_days"
                       value="{{$loyalty_point_settings->loyalty_points_expired_days ?? ''}}" min="0" required>
            </div>         
            <div class="form-group">
                <button id="save_loyalty_settings" type="submit" class="btn btn-success"><i
                        class="fa fa-check"></i> @lang('app.save')</button>
            </div>

        </div>

    </div>

</form>