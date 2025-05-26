<h4>Service Banner Image</h4>
<br>
<form class="form-horizontal ajax-form" id="service_banner_form_2" method="POST" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">              
        <div class="col-md-4">
            <div class="form-group">
                <label for="image">@lang('app.update') @lang('app.image')</label>
                <input type="file" id="input-file-now" name="service_banner_image" accept=".png,.jpg,.jpeg" data-default-file="{{ asset('img/no-image.jpg') }}" class="form-control" />
            </div>

            <h6 class="text-danger">** Recommended image resolution: 1621 * 1081</h6>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <button id="service_banner_btn_2" type="button" class="btn btn-success" style="margin-top: 25px;"><i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="toggle-switch">@lang('app.status')</label>
                <br>
                <input type="checkbox" {{(isset($service_banner_settings) && $service_banner_settings->service_banner_two_status == 1) ? 'checked' : '' }} data-toggle="toggle" name="service_banner_two_status" id="service_banner_two_status" value="1" data-banner_type="2">
            </div>
        </div>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bannerImage_2 as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>
                        <img src="{{ asset("user-uploads/service-banner-2/".$item->image) }}" class="img-thumbnail" alt="" width="100" height="100">
                    </td>
                    <td>
                        <a href="#" class="btn btn-danger btn-circle delete_service_banner_2_btn" data-toggle="tooltip" data-row_id="{{$item->id}}" data-original-title="{{__('app.delete')}}" style="margin-top: 20px;"><i class="fa fa-times" aria-hidden="true"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>