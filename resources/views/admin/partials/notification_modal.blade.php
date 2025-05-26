{{-- loyalty program modal --}}
<div class="modal fade bs-modal-lg in" id="send_notification_modal" role="dialog" aria-labelledby="send_notification_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-lg-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Notification</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>        
            </div>
            <div class="modal-body">

                <form id="send_notification_form" method="post">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="notif_title">Notifications Title:</label>
                        <input type="text" class="form-control" name="notif_title" id="notif_title" placeholder="Enter Notification Title" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="notif_body">Notifications body message:</label>
                        <textarea class="form-control" name="notif_body" id="notif_body" cols="30" rows="5" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="upload_date">Date Upload:</label>
                        <input type="date" class="form-control" name="upload_date" id="upload_date" value="{{date('Y-m-d')}}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="taget_page">Target Pages (Link):</label>
                        <input type="text" class="form-control" name="taget_page" id="taget_page">
                    </div>

                    <div class="form-group" style="text-align: end;">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Send Notifications</button>
                    </div>
                </form>
             
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>
            </div> --}}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>