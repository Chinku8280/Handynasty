@if($bookingTime->status == 'enabled')
    @if ($bookingTime->multiple_booking === 'yes' && $bookingTime->max_booking != 0 && $bookings->count() >= $bookingTime->max_booking)
        <div class="alert alert-custom mt-3">
            @lang('front.maxBookingLimitReached')
        </div>
    @else
        <ul class="time-slots px-1 py-1 px-md-5 py-md-5">
            @php $slot_count = 1; $check_remaining_booking_slots = 0; @endphp
            @for($d = $startTime;$d < $endTime;$d->addMinutes($bookingTime->slot_duration))
                @php $slotAvailable = 1; @endphp
                @if($bookingTime->multiple_booking === 'no' && $bookings->count() > 0)
                    @foreach($bookings as $booking)
                        @if($booking->date_time->format($settings->time_format) == $d->format($settings->time_format))
                            @php $slotAvailable = 0; @endphp
                        @endif
                    @endforeach
                @endif

                @if($slotAvailable == 1)
                    @php $check_remaining_booking_slots++; @endphp
                    <li>
                        <label class="custom-control custom-radio">
                        <input id="radio{{$slot_count}}" onclick="checkUserAvailability('{{$d}}', {{$slot_count}}, '{{$d->format($settings->time_format)}}')" type="radio" value="{{ $d->format('H:i:s') }}" class="custom-control-input" name="booking_time">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">{{ $d->format($settings->time_format) }}</span>
                        </label>
                    </li>
                @endif
                @php $slot_count++; @endphp
            @endfor
        </ul>

        {{-- select employee div --}}
        <div class="col-12 alert alert-custom mt-3" id="select_user_div" style="display: none">
            <span>@lang('messages.booking.selectEmployeeMSG')</span>
            <span id="select_user"></span>
        </div>

        <div class="col-12 alert alert-custom mt-3 text-center" id="show_emp_name_div" style="display: none"></div>

        <div class="alert alert-custom mt-3" id="no_emp_avl_msg" style="display: none">
            @lang('front.noEmployeeAvailableAt') <span id="timeSpan"><span>.
        </div>

        @if($slot_count==1 || $check_remaining_booking_slots==0)
            <div class="alert alert-custom mt-3">
                @lang('front.bookingSlotNotAvailable')
            </div>
        @endif

    @endif
@else
    <div class="alert alert-custom mt-3">
        @lang('front.bookingSlotNotAvailable')
    </div>
@endif
