<?php

namespace App\Observers;

use App\Booking;
use App\CompanySetting;
use App\Notifications\BookingStatusChange;
use Carbon\Carbon;

class BookingObserver
{
    public function updating(Booking $booking)
    {
        if ($booking->isDirty('status'))
        {
            if(!is_null($booking->deal_id) && $booking->date_time==''){
                $booking->date_time = Carbon::now()->setTimezone(CompanySetting::first()->timezone)->format('Y-m-d H:i:s');
            }
        }
    }

    public function updated(Booking $booking)
    {
        if ($booking->isDirty('status')){
            $booking->user->notify(new BookingStatusChange($booking));
        }
    }

}
