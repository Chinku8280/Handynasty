@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">

<style>
    .collapse.in{
        display: block;
    }
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

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button
    {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number]
    {
        -moz-appearance: textfield;
    }

</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.add') @lang('menu.deal')</h3>
                </div>
                <div class="card-body">
                    <form role="form" id="createForm"  class="ajax-form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.title') </label>
                                    <input type="text" class="form-control" name="title" id="title" value="" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.slug') </label>
                                    <input type="text" class="form-control" name="slug" id="slug" value="" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>@lang('app.applyDealOn')</label>
                                    <select name="choice" id="choice" class="form-control form-control-lg select2" style="width: 100%">
                                        <option value="service">@lang('app.service')</option>
                                        <option value="location">@lang('app.location')</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4" id="service_div">
                                <div class="form-group">
                                    <label>@lang('app.service')</label>
                                    <select name="services[]" id="services" class="form-control form-control-lg select2" multiple="multiple" style="width: 100%">
                                        <option value="">@lang('app.selectServices')</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->name }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4" id="location_div">
                                <div class="form-group">
                                    <label>@lang('app.location')</label>
                                    <select name="locations" id="locations" class="form-control form-control-lg select2" style="width: 100%">
                                        <option value="">@lang('app.selectLocation')</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2" style="margin-top: 2em">
                                <button type="button" class="btn btn-default" id="make_deal">
                                    <i class="fa fa-plus"></i>  @lang('app.makeDeal')
                                </button>
                                <button id="reset-btn" type="button" class="btn btn-default">
                                    <i class="fa fa-refresh"></i>  @lang('app.reset')
                                </button>
                            </div>

                            <div class="offset-md-1 col-md-10 offset-md-1">
                                <div class="table table-responsive" id="result_div"></div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">@lang('app.discount') @lang('app.type')</label>
                                    <select name="discount_type" id="discount_type" class="form-control">
                                        <option value="percentage"> @lang('app.percentage') </option>
                                        <option value="{{ $settings->currency->currency_name }}"> {{ $settings->currency->currency_name }} </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.discount') @lang('app.percentage')</label>
                                    <input onkeypress="return isNumberKey(event)" type="number" class="form-control checkAmount" name="discount" id="discount" value="0" min="0">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.dealPrice')</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">{{$settings->currency->currency_symbol}}</span>
                                        </div>
                                        <input onkeypress="return isNumberKey(event)" readonly type="number" class="form-control checkAmount" name="discount_amount" id="discount_amount" value="0" min="0">
                                        <input type="hidden" class="form-control" name="original_amt" id="original_amt" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.dealLimit')</label>
                                    <input type="number" class="form-control" name="uses_time" min="0" onkeypress="return isNumberKey(event)">
                                    <span class="help-block">@lang('messages.dealLimit')</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.customerUseLimit')</label>
                                    <input onkeypress="return isNumberKey(event)" type="number" class="form-control" name="customer_uses_time" value="1" min="0">
                                    <span class="help-block">@lang('messages.howManyTimeCustomerCanUse')</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('app.status')</label>
                                    <select name="status" class="form-control">
                                        <option value="active"> @lang('app.active') </option>
                                        <option value="inactive"> @lang('app.inactive') </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.appliedBetweenDateTime')</label>
                                    <input type="text" class="form-control" id="daterange" name="applied_between_dates" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('modules.settings.openTime')</label>
                                    <input type="text" class="form-control" id="open_time" name="open_time" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('modules.settings.closeTime')</label>
                                    <input type="text" class="form-control" id="close_time"  name="close_time" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label style="">@lang('app.dayForApply') </label>
                                <div class="row" style="margin-top: 5px">
                                    @forelse($days as $day)
                                        <div class="form-group" style="margin-left: 1em">
                                            <label class="">
                                                <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false" style="position: relative; margin-right: 5px;">
                                                    <input type="checkbox" checked value="{{$day}}" name="days[]" class="flat-red columnCheck"  style="position: absolute; opacity: 0; margin-left: 15px;">
                                                    <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                </div>
                                                @lang('app.'. strtolower($day))
                                            </label>
                                        </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="feature_image" accept=".png,.jpg,.jpeg" data-default-file="{{ asset('img/no-image.jpg')  }}" class="form-control"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="button" id="save-form" class="btn btn-success btn-light-round">
                                    <i class="fa fa-check"></i> @lang('app.save')
                                </button>
                            </div>

                        </div>
                        <input type="hidden" name="deal_startDate" id="deal_startDate">
                        <input type="hidden" name="deal_endDate" id="deal_endDate">
                        <input type="hidden" name="deal_startTime" id="deal_startTime">
                        <input type="hidden" name="deal_endTime" id="deal_endTime">
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection

@push('footer-js')
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

    <script>

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('#open_time').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
        }).on('dp.change', function(e) {
            $('#deal_startTime').val(convert(e.date));
        });

        $('#close_time').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
        }).on('dp.change', function(e) {
            $('#deal_endTime').val(convert(e.date));
        });




        $(function () {
            $('#description').summernote({
                dialogsInBody: true,
                height: 300,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ["view", ["fullscreen"]]
                ]
            });
        });

        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
        });

        $('.checkAmount').keyup(function () {
            var original_amount = $('#original_amount').val();
            var discount_amount = $('#discount_amount').val();
            if(original_amount!='' && discount_amount!='' && Number(discount_amount) > Number(original_amount)){
                $('#discount_amount').focus();
                $('#discount_amount').val('');
            }
        });

        $(function() {
            moment.locale('{{ $settings->locale }}');
            $('input[name="applied_between_dates"]').daterangepicker({
                timePicker: true,
                minDate: moment().startOf('hour'),
                autoUpdateInput: false,
            });
        });


        $('input[name="applied_between_dates"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('{{ $date_picker_format }} {{$time_picker_format}}') + '--' + picker.endDate.format('{{ $date_picker_format }} {{$time_picker_format}}'));
            $('#deal_startDate').val(picker.startDate.format('YYYY-MM-DD')+' '+convert(picker.startDate));
            $('#deal_endDate').val(picker.endDate.format('YYYY-MM-DD')+' '+convert(picker.endDate));
        });

        $('#services').change(function(){
            var services = $(this).val();
            if($('#choice').val()=='location'){
                return false;
            }
            $.ajax({
                url: "{{route('admin.deals.selectLocation')}}",
                type: "POST",
                container: '#createForm',
                data:$('#createForm').serialize(),
                success:function(data){
                    $('#locations').html(data.selected_location);
                    $('#locations').val(data.selected_location);
                }
            })
        });

        $('#locations').change(function(){
            var locations = $(this).val();
            if($('#choice').val()=='service'){
                return false;
            }
            $.ajax({
                url: "{{route('admin.deals.selectServices')}}",
                container: '#createForm',
                type: "POST",
                data: $('#createForm').serialize(),
                success:function(data){
                    $('#services').html(data.selected_service);
                    $('#services').val(data.selected_service);
                }
            })
        });

        $('#reset-btn').click(function(){
            $.ajax({
                url: "{{route('admin.deals.resetSelection')}}",
                type: "GET",
                success:function(data){
                    $('#locations').html(data.all_locations_array);
                    $('#services').html(data.all_services_array);
                    $('#result_div').html('');
                    $('#discount_amount').val(0);
                }
            })
        });

        $('#make_deal').click(function()
        {
            var locations = $('#locations').val();
            var services = $('#services').val();
            var choice = $('#choice').val();
            if(services.length==0){
                toastr.error('@lang("messages.deal.selectOneService")');
                return false;
            }
            else if(locations.length==0){
                toastr.error('@lang("messages.deal.selectOneLocation")');
                return false;
            }
            $.ajax({
                url: "{{route('admin.deals.makeDeal')}}",
                type: "POST",
                container: '#createForm',
                data:$('#createForm').serialize(),
                success:function(data){
                    $('#result_div').html(data.view);
                }
            });
        });

        $('#choice').change(function(){
            var location_div = $('#location_div').html(), service_div = $('#service_div').html(), choice = $(this).val();
            if(choice=='service'){
                $("#location_div").before($("#service_div"));
            }
            else if(choice=='location'){
                $("#service_div").before($("#location_div"));
            }
            $('#reset-btn').click();
        });

        $(document).on('click', '.quantity-minus', function () {
            let serviceId = $(this).data('service-id');
            let qty = $('.deal-service-'+serviceId).val();
            qty = parseInt(qty)-1;
            if(qty < 1){
                return false;
                // qty = 1;
            }
            $('.deal-service-'+serviceId).val(qty);
            updateCartQuantity(serviceId, qty);
        });

        $(document).on('click', '.quantity-plus', function () {
            let serviceId = $(this).data('service-id');
            let qty = $('.deal-service-'+serviceId).val();
            qty = parseInt(qty)+1;
            $('.deal-service-'+serviceId).val(qty);
            updateCartQuantity(serviceId, qty);
        });

        $(document).on('keyup', '.deal_discount', function () {
            let discount = $(this).val();
            if(discount=='' && discount>=0){
                $(this).val(0);
            }
            calculateTotal();
        });

        $(document).on('change', '#discount_type', function () {
            let discount_type = $('#discount_type').val();
            if(discount_type=='percentage'){
                $('#discount').prop("readonly", false);
            }
            else{
                $('#discount').prop("readonly", true);
                $('#discount').val(0);
            }
            $('.deal_discount').val(0);
            calculateTotal();
        });

        $(document).on('keyup', '#discount', function () {
            let percent_discount = $(this).val();
            if(percent_discount>100){
                $(this).val(0);
            }
            calculateTotal();
        });

        function deleteRow(id, name)
        {
            $("#row"+id).remove();
            $("#services option[value='"+name+"']").detach();
            calculateTotal();
        }

        function updateCartQuantity(serviceId, qty) {
            let servicePrice = $('.deal-price-'+serviceId).val();
            let subTotal = (parseFloat(servicePrice) * parseInt(qty));
            $('.deal-subtotal-'+serviceId).html("{{ $settings->currency->currency_symbol }}"+subTotal.toFixed(2));
            calculateTotal();
        }

        function calculateTotal()
        {
            let dealSubTotal = 0;
            let discount = 0;
            let dealTotal = 0;
            let total_discount=0;
            let discountPercentage = 0;
            let discount_type = $('#discount_type').val();

            if($('#discount').val()!='' || $('#discount').val()>0){
                discountPercentage = $('#discount').val();
            }

            $("input[name='deal_discount[]']").each(function(index){
                let discount_price = $(this).val();
                let sub_total_price = $("td[name='deal-subtotal[]']").eq(index).html().replace('{{ $settings->currency->currency_symbol }}','');

                if(discount_type=='percentage'){
                    $(this).prop("readonly", true);
                    discount_price = sub_total_price * (discountPercentage/100);
                    $(this).val(discount_price.toFixed(2));
                }
                else{
                    $(this).prop("readonly", false);
                    if(discount_price>0){ /* for NAN validation */
                        if(parseFloat(discount_price)>sub_total_price){
                            dealTotal=0;
                            $(this).val(0);
                            discount_price = 0;
                        }
                    }
                }
                total_discount = parseFloat(total_discount) + parseFloat(discount_price);
            });

            $("input[name='deal_unit_price[]']").each(function(index)
            {
                let servicePrice = $(this).val();
                let qty = $("input[name='deal_quantity[]']").eq(index).val();
                let sub_discount = $("input[name='deal_discount[]']").eq(index).val();
                let sub_total = parseFloat(servicePrice) * parseInt(qty);
                discount = parseFloat(sub_total) - parseFloat(sub_discount);

                if(parseFloat(sub_discount)>sub_total){
                    discount = sub_total;
                }
                if(sub_discount>0){
                    $("td[name='deal-total[]']").eq(index).html("{{ $settings->currency->currency_symbol }}"+discount.toFixed(2));
                }
                else{
                    $("td[name='deal-total[]']").eq(index).html("{{ $settings->currency->currency_symbol }}"+sub_total.toFixed(2));
                }
                dealSubTotal = (dealSubTotal + sub_total);
            });

            let deal_total_price=0;
            if(total_discount<dealSubTotal){
                deal_total_price = dealSubTotal-total_discount;
            }

            $("#deal-sub-total").html("{{ $settings->currency->currency_symbol }}"+dealSubTotal.toFixed(2));
            $("#deal-discount-total").html("{{ $settings->currency->currency_symbol }}"+total_discount.toFixed(2));
            $("#deal-total-price").html("{{ $settings->currency->currency_symbol }}"+ deal_total_price.toFixed(2));
            $('#discount_amount').val(deal_total_price.toFixed(2));
            $('#original_amt').val(dealSubTotal.toFixed(2));
        }

        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
            return true;
        }

        function createSlug(value) {
            value = value.replace(/\s\s+/g, ' ');
            let slug = value.split(' ').join('-').toLowerCase();
            slug = slug.replace(/--+/g, '-');
            $('#slug').val(slug);
        }

        $('#title').keyup(function(e) {
            createSlug($(this).val());
        });

        $('#slug').keyup(function(e) {
            createSlug($(this).val());
        });

        $('#save-form').click(function () {

            var token = $('meta[name="csrf-token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });

            $.easyAjax({
                url: '{{ route('admin.deals.store') }}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file: true,
                data: $('#createForm').serialize(),
            });

        });

        function convert(str)
        {
            var date = new Date(str);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            hours = ("0" + hours).slice(-2);
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }
    </script>

@endpush
