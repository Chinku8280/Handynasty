<h4>Service Banner Image</h4>
<br>
<form class="form-horizontal ajax-form" id="service_banner_form_1" method="POST" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">              
        <div class="col-sm-8">
            <div class="form-group">
                <label for="image">@lang('app.update') @lang('app.image')</label>
                <input type="file" id="input-file-now" name="service_banner_image" accept=".png,.jpg,.jpeg" data-default-file="{{ asset('img/no-image.jpg') }}" class="form-control" />
            </div>

            <h6 class="text-danger">** Recommended image resolution: 1621 * 1081</h6>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <button id="service_banner_btn_1" type="button" class="btn btn-success" style="margin-top: 25px;"><i class="fa fa-check"></i> @lang('app.save')</button>
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
            @foreach ($bannerImage_1 as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>
                        <img src="{{ asset("user-uploads/service-banner-1/".$item->image) }}" class="img-thumbnail" alt="" width="100" height="100">
                    </td>
                    <td>
                        <a href="#" class="btn btn-danger btn-circle delete_service_banner_1_btn" data-toggle="tooltip" data-row_id="{{$item->id}}" data-original-title="{{__('app.delete')}}" style="margin-top: 20px;"><i class="fa fa-times" aria-hidden="true"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>