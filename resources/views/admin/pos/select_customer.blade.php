<div class="modal-header">
    <h4 class="modal-title">@lang('modules.booking.customerDetails')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form id="createProjectCategory" class="ajax-form" method="POST" autocomplete="off">
        @csrf
        <div class="form-body">
            <div class="row">
                <div class="col-sm-12">

                    <div class="form-group">
                        <label>@lang('app.name')</label>

                        <input type="text" class="form-control form-control-lg" id="username" name="name">
                    </div>

                    <div class="form-group">
                        <label>@lang('app.mobile')</label>
                        <div class="form-row">
                            <div class="col-md-4 mb-2">
                                <select name="calling_code" id="calling_code" class="form-control select2">
                                    @foreach ($calling_codes as $code => $value)
                                        <option value="{{ $value['dial_code'] }}">
                                            {{ $value['dial_code'] . ' - ' . $value['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="mobile">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>@lang('app.email')</label>

                        <input type="text" class="form-control form-control-lg" name="email" >
                    </div>

                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-category" class="btn btn-success">@lang('app.continue') <i class="fa fa-arrow-right"></i></button>
        </div>
    </form></div>


<script>
    $('#calling_code.select2').select2();

    $('#save-category').click(function () {
        let username = $('#username').val();
        $.easyAjax({
            url: '{{route('admin.customers.store')}}',
            container: '#createProjectCategory',
            type: "POST",
            data: $('#createProjectCategory').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    var newOption = new Option(response.user.text, response.user.id, true, true);
                    $('#user_id').append(newOption).trigger('change');
                    $('#user-error').text('');
                    $('#application-modal').modal('hide');

                        // manually trigger the `select2:select` event
                    $('#user_id').trigger({
                        type: 'select2:select',
                        params: {
                            data: response.user
                        }
                    });
                    customerDetails(response.user.id);
                }
            }
        })
    });
</script>
