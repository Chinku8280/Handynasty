@extends('layouts.master')

@push('head-css')
    <style>
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #999;
        }
        .select2-dropdown .select2-search__field:focus, .select2-search--inline .select2-search__field:focus {
            border: 0px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            margin: 0 13px;
        }
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #cfd1da;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__clear {
            cursor: pointer;
            float: right;
            font-weight: bold;
            margin-top: 8px;
            margin-right: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') Product</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" id="id" value="{{ $product->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Product @lang('app.name')</label>
                                    <input type="text" name="product_name" id="product_name" class="form-control form-control-lg" autocomplete="off" value="{{ $product->product_name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Product @lang('app.slug')</label>
                                    <input type="text" name="slug" id="slug" class="form-control form-control-lg" autocomplete="off" value="{{ $product->slug }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Product Short @lang('app.description')</label>
                                    <textarea name="short_description" id="short_description" cols="30" class="form-control-lg form-control" rows="1">{{ $product->short_description ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Product @lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{{ $product->description ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.price')</label>
                                    <input onkeypress="return isNumberKey(event)" type="number" step="0.01" min="0" name="price" id="price" class="form-control form-control-lg" value="{{ $product->price }}"/>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Loyalty Coin</label>
                                    <input type="number" min="0" name="loyalty_point" id="loyalty_point" class="form-control form-control-lg" value="{{ $product->loyalty_point }}"/>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.select') @lang('app.outlet')</label>                               
                                    <select name="outlet_id[]" id="outlet_id" class="form-control form-control-lg select2"
                                        style="width: 100%" multiple="multiple" required>
                                        <option value="0">All Outlets</option>
                                        @foreach ($outlets as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, $selectedOutlets) ? 'selected' : '' }}>
                                                {{ $item->outlet_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>   

                            <div class="col-md-12">
                                <button type="button" class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button" style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i> File Select Or Upload</button>
                                <div id="file-upload-box" >
                                    <div class="row" id="file-dropzone">
                                        <div class="col-md-12">
                                            <div class="dropzone" id="file-upload-dropzone">
                                                {{ csrf_field() }}
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="product_id" id="product_id">

                                {{-- <h6 class="text-danger">** Recommended image resolution: 160 * 160</h6> --}}
                                {{-- <h6 class="text-danger">@lang('modules.theme.recommendedResolutionNote')</h6> --}}
                                <h6 class="text-danger">@lang('modules.businessServices.defaultImageNotice')</h6>                      
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('app.status')</label>
                                    <select name="status" id="status" class="form-control form-control-lg">
                                        <option
                                                @if($product->status == 'active') selected @endif
                                        value="active">@lang('app.active')</option>
                                        <option
                                                @if($product->status == 'deactive') selected @endif
                                        value="deactive">@lang('app.deactive')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                                class="fa fa-check"></i> @lang('app.save')</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection

@push('footer-js')
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    <script>

        var mockFile = {!! $images !!};
        var defaultImage = '';
        var lastIndex = 0;

        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("#file-upload-dropzone", {
            url: "{{ route('admin.products.update-images') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            init: function () {
                myDropzone = this;
            },
            dictDefaultMessage: "@lang('app.dropzone.defaultMessage')",
            dictRemoveFile: "@lang('app.dropzone.removeFile')"
        });

        myDropzone.on('sending', function(file, xhr, formData) {
            var id = $('#product_id').val();

            formData.append('product_id', id);
            if (mockFile.length > 0) {
                formData.append('uploaded_files', JSON.stringify(mockFile));
            }
            formData.append('default_image', defaultImage);
        });

        myDropzone.on('addedfile', function (file) {
            var index = mockFile.findIndex(x => x.name == file.name);

            if (index === -1) {
                index = lastIndex + 1;
            }
            lastIndex = index;

            var div = document.createElement('div');
            div.className = 'form-check form-check-inline';
            var input = document.createElement('input');
            input.className = 'form-check-input';
            input.type = 'radio';
            input.name = 'default_image';
            input.id = 'default-image-'+index;
            input.value = file.name;
            if ('{{ $product->default_image }}' == file.name) {
                input.checked = true;
            }
            div.appendChild(input);
            var label = document.createElement('label');
            label.className = 'form-check-label';
            label.innerHTML = "@lang('app.dropzone.makeDefaultImage')";
            label.htmlFor = 'default-image-'+index;
            div.appendChild(label);
            file.previewTemplate.appendChild(div);
        })

        myDropzone.on('removedfile', function (file) {
            var index = mockFile.findIndex(x => x.name == file.name);
            mockFile.splice(index, 1);
        })

        // Create the mock file:
        mockFile.forEach(file => {
            var path = "{{ asset_url('product/'.$product->id.'/:file_name') }}";
            path = path.replace(':file_name', file.name);

            myDropzone.emit('addedfile', file);

            myDropzone.emit('thumbnail', file, path);

            // myDropzone.createThumbnailFromUrl(file, path);

            myDropzone.files.push(file);
            myDropzone.emit("complete", file);
        });

        myDropzone.options.maxFiles = myDropzone.options.maxFiles - mockFile.length;

        myDropzone.on("maxfilesexceeded", function(file) { this.removeFile(file); });

        function createSlug(value) {
            value = value.replace(/\s\s+/g, ' ');
            let slug = value.split(' ').join('-').toLowerCase();
            slug = slug.replace(/--+/g, '-');
            $('#slug').val(slug);
        }

        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
            return true;
        }

        $(function () {
            // $('#description').summernote({
            //     dialogsInBody: true,
            //     height: 300,
            //     toolbar: [
            //         // [groupName, [list of button]]
            //         ['style', ['bold', 'italic', 'underline', 'clear']],
            //         ['font', ['strikethrough']],
            //         ['fontsize', ['fontsize']],
            //         ['para', ['ul', 'ol', 'paragraph']],
            //         ["view", ["fullscreen"]]
            //     ]
            // });

            // ck editor start

            var ckeditorInstance;

            ClassicEditor.create(document.querySelector('#description'), {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'bulletedList', 'numberedList', '|',
                        'undo', 'redo'
                    ]
                },
                removePlugins: [
                    'Image',
                    'ImageToolbar',
                    'ImageCaption',
                    'ImageStyle',
                    'ImageResize',
                    'ImageUpload',
                    'CKFinder',
                    'MediaEmbed',
                    'Table',
                    'BlockQuote',
                    'EasyImage'
                ]
            })
            .then(editor => {
                ckeditorInstance = editor;
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });

            // ck editor end
        
            $('#product_name').keyup(function(e) {
                createSlug($(this).val());
            });

            $('#slug').keyup(function(e) {
                createSlug($(this).val());
            });

            $('#save-form').click(function () {
                // Manually update the textarea with CKEditor content
                $('#description').val(ckeditorInstance.getData());

                $.easyAjax({
                    url: '{{route('admin.products.update', $product->id)}}',
                    container: '#createForm',
                    type: "POST",
                    redirect: true,
                    file:true,
                    data: $('#createForm').serialize(),
                    success: function (response) {
                        product_id = response.product_id;
                        $('#product_id').val(response.product_id);
                        defaultImage = response.defaultImage;

                        if (myDropzone.getQueuedFiles().length > 0) {
                            myDropzone.processQueue();

                            myDropzone.on("success", function(file, responseText) {
                                // console.log(responseText);

                                if(responseText.status == "success")
                                {
                                    var msgs = "@lang('messages.updatedSuccessfully')";
                                    $.showToastr(msgs, 'success');
                                    window.location.href = '{{ route('admin.products.index') }}';
                                }
                            });
                        }
                        else{
                            var blob = new Blob();
                            blob.upload = { 'chunked': myDropzone.defaultOptions.chunking };
                            myDropzone.uploadFile(blob);                    

                            var msgs = "@lang('messages.updatedSuccessfully')";
                            $.showToastr(msgs, 'success');
                            window.location.href = '{{ route('admin.products.index') }}';
                        }
                    }
                });

            });

        });

    </script>

@endpush
