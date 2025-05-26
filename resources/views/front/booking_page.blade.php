@extends('layouts.front')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.css') }}">
@endpush

@section('content')
    <section class="section">
        <section class="booking-time sp-80 bg-w">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="all-title">
                        <h3 class="sec-title">
                            @lang('front.selectBookingTime')
                        </h3>
                    </div>
                </div>
            </div>
            <div class="booking-slots w-100">
                <div class="w-100">
                    <div class="date-picker w-100">
                        <div id="datepicker"></div>
                        <input type="hidden" id="booking_date" name="booking_date">
                    </div>
                    <div class="slots-wrapper">
                    </div>
                </div>
            </div>
            <div class="row mt-30">
                <div class="col-12">
                    <center>
                        <h5 style="color: crimson;" id="msg_div"></h5>
                    </center>
                </div>
            </div>
            <div class="row mt-30">
                <div class="col-12">
                    <div class="navigation">
                        <a href="{{ route('front.index') }}" class="btn btn-custom btn-dark"><i class="fa fa-angle-left mr-2"></i>@lang('front.navigation.goBack')</a>
                        <a id="nextBtn" href="javascript:;" onclick="addBookingDetails()" class="btn btn-custom btn-dark">@lang('front.next') <i class="fa fa-angle-right ml-1"></i> </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </section>
@endsection

@push('footer-script')
    <script src="{{ asset('front-assets/js/date.format.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}"></script>
    @if ($locale !== 'en')
        <script src="{{ 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.'.$locale.'.min.js' }}" charset="UTF-8"></script>
    @endif
    <script>
        $(function () {
            @if (sizeof($bookingDetails) > 0)
                getBookingSlots({ bookingDate:  '{{ $bookingDetails['bookingDate'] }}', _token: "{{ csrf_token() }}"});

                var bookingDate = '{{ $bookingDetails['bookingDate'] }}';

                bookingDetails.bookingDate = bookingDate;
                $('#datepicker').datepicker('update', dateFormat(new Date(bookingDate), 'yyyy-mm-dd', true));
            @endif
        });

        $('#datepicker').datepicker({
            // todayHighlight: true,
            templates: {
                leftArrow: '<i class="fa fa-chevron-left"></i>',
                rightArrow: '<i class="fa fa-chevron-right"></i>'
            },
            startDate: '-0d',
            language: '{{ $locale }}',
            weekStart: 0,
            format: "yyyy-mm-dd"
        });

        var bookingDetails = {_token: $("meta[name='csrf-token']").attr('content')};

        function getBookingSlots(data) {
            $('#msg_div').hide();
            $.easyAjax({
                url: "{{ route('front.bookingSlots') }}",
                type: "POST",
                data: data,
                success: function (response) {
                    if(response.status == 'success'){
                        $('.slots-wrapper').html(response.view);
                        // check for cookie
                        @if (sizeof($bookingDetails) > 0)
                            $('.slots-wrapper').css('display', 'flex');

                            var bookingTime = '{{ $bookingDetails['bookingTime'] }}';
                            var bookingDate = '{{ $bookingDetails['bookingDate'] }}';
                            var emp_name    = '{{ $bookingDetails['emp_name'] }}';

                            if (bookingDate == bookingDetails.bookingDate) {
                                bookingDetails.bookingTime = bookingTime;
                                $(`input[value='${bookingTime}']`).attr('checked', true);
                                if(emp_name == ''){ emp_name = 'No Employee';  }
                                $('#show_emp_name_div').show();
                                $('#show_emp_name_div').html(emp_name+' is selected for this booking..!');
                            }
                            else {
                                bookingDetails.bookingTime = '';
                            }
                        @else
                            bookingDetails.bookingTime = '';
                        @endif
                    }
                    else{
                        $('.slots-wrapper').html('');
                        $('.slots-wrapper').css('display', 'none');
                        $('#msg_div').show();
                        $('#msg_div').html(response.msg);
                    }
                }
            })
        }

        $('#datepicker').on('changeDate', function() {
            $('.slots-wrapper').css({'display': 'flex', 'align-items': 'center'});
            var initialHeight = $('.slots-wrapper').css('height');
            var html = '<div class="loading text-white d-flex align-items-center" style="height: '+initialHeight+';">Loading... </div>';
            $('.slots-wrapper').html(html);

            $('html, body').animate({
                scrollTop: $(".slots-wrapper").offset().top
            }, 1000);

            var formattedDate = $('#datepicker').datepicker('getFormattedDate');

            $('#booking_date').val(formattedDate);
            bookingDetails.bookingDate = dateFormat((new Date(formattedDate)), "yyyy-mm-dd", true);

            getBookingSlots({ bookingDate:  bookingDetails.bookingDate, _token: "{{ csrf_token() }}"})
        });

        $(document).on('change', $('input[name="booking_time"]'), function (e) {
            bookingDetails.bookingTime = $(this).find('input[name="booking_time"]:checked').val();
        });

        function addBookingDetails()
        {
            bookingDetails.selected_user = $('#selected_user').val();
            // console.log(bookingDetails);
            $.easyAjax({
                url: '{{ route('front.addBookingDetails') }}',
                type: 'POST',
                container: 'section.section',
                data: bookingDetails,
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = '{{ route('front.cartPage') }}'
                    }
                },
                error: function (err) {
                   var errors = err.responseJSON.errors;
                    for (var error in errors) {
                       $.showToastr(errors[error][0], 'error')
                    }
                }
            });
        }

        function checkUserAvailability(date, radioID, time)
        {
            $('#select_user_div').hide();
            $('#no_emp_avl_msg').hide();
            $('#show_emp_name_div').hide();
            $.easyAjax({
                url: '{{ route('front.checkUserAvailability') }}',
                type: 'POST',
                container: 'section.section',
                data: {date:date, _token: "{{ csrf_token() }}" },
                success: function (response) {
                    if (response.continue_booking == 'no') {
                        $('#no_emp_avl_msg').show();
                        $('#timeSpan').html(time);
                        $('#radio'+radioID).prop("checked", false);
                    }
                    else{
                        $('#no_emp_avl_msg').hide();
                        if(typeof response.select_user !== 'undefined'){
                            $('#select_user_div').show();
                            $('#select_user').html(response.select_user);
                        }
                    }
                }
            });
        }
    </script>
@endpush
