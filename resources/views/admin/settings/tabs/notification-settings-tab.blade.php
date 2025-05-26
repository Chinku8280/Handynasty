<h4>Notification Settings</h4>
<br>
<form class="form-horizontal" id="notification_settings_form" method="POST">
    @csrf

    <div class="row">
        <div class="col-md-12">

            <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="toggle-switch">Categories</label>
                    </div>
                    <div class="col-6">
                        <input type="checkbox" {{(isset($notification_settings) && $notification_settings->category_notif_status == 1) ? 'checked' : '' }} data-toggle="toggle" name="category_notif_status" id="category_notif_status" value="1">
                    </div>
                </div>              
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="toggle-switch">Services</label>
                    </div>
                    <div class="col-6">
                        <input type="checkbox" {{(isset($notification_settings) && $notification_settings->service_notif_status == 1) ? 'checked' : '' }} data-toggle="toggle" name="service_notif_status" id="service_notif_status" value="1">
                    </div>
                </div>              
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="toggle-switch">Coupons</label>
                    </div>
                    <div class="col-6">
                        <input type="checkbox" {{(isset($notification_settings) && $notification_settings->coupon_notif_status == 1) ? 'checked' : '' }} data-toggle="toggle" name="coupon_notif_status" id="coupon_notif_status" value="1">
                    </div>
                </div>              
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="toggle-switch">Vouchers</label>
                    </div>
                    <div class="col-6">
                        <input type="checkbox" {{(isset($notification_settings) && $notification_settings->voucher_notif_status == 1) ? 'checked' : '' }} data-toggle="toggle" name="voucher_notif_status" id="voucher_notif_status" value="1">
                    </div>
                </div>              
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="toggle-switch">Loyalty Shop</label>
                    </div>
                    <div class="col-6">
                        <input type="checkbox" {{(isset($notification_settings) && $notification_settings->loyalty_shop_notif_status == 1) ? 'checked' : '' }} data-toggle="toggle" name="loyalty_shop_notif_status" id="loyalty_shop_notif_status" value="1">
                    </div>
                </div>              
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="toggle-switch">Promotions</label>
                    </div>
                    <div class="col-6">
                        <input type="checkbox" {{(isset($notification_settings) && $notification_settings->promotion_notif_status == 1) ? 'checked' : '' }} data-toggle="toggle" name="promotion_notif_status" id="promotion_notif_status" value="1">
                    </div>
                </div>              
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="toggle-switch">Happenings</label>
                    </div>
                    <div class="col-6">
                        <input type="checkbox" {{(isset($notification_settings) && $notification_settings->happening_notif_status == 1) ? 'checked' : '' }} data-toggle="toggle" name="happening_notif_status" id="happening_notif_status" value="1">
                    </div>
                </div>              
            </div>

            <div class="form-group">
                <button id="save_loyalty_settings" type="submit" class="btn btn-success"><i
                        class="fa fa-check"></i> @lang('app.save')</button>
            </div>

        </div>

    </div>

</form>