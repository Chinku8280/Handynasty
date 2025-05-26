<h4>@lang('menu.whoWeAre')</h4>
<br>
<form class="form-horizontal ajax-form" id="who-we-are-update-form" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">            
            <div class="col-sm-8">
                <div class="form-group mt-3">
                    <label for="image">@lang('app.image')</label>
                    <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" data-default-file="{{ asset('img/no-image.jpg') }}" class="form-control" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mt-3">
                    <img src="{{ asset('/user-uploads/who-we-are/' . $whowearecontent->image ?? '') }}" class="img-thumbnail" alt="">
                </div>
            </div>

            <div class="col-md-12 mt-3">
                <div class="form-group">
                    <label>@lang('app.title') </label>
                    <input type="text" class="form-control" name="title" id="title" value="{{ $whowearecontent->title ?? '' }}"
                        autocomplete="off">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">@lang('app.description')</label>
                    <textarea name="description" cols="30" class="form-control-lg form-control" id="who_are_we_decsription" rows="4">{{ $whowearecontent->description ?? '' }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <button id="update-who-we-are-content" type="button" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
        </div>
    </div>
</form>