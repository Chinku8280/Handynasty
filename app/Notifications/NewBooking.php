<?php

namespace App\Notifications;

use App\Booking;
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Support\HtmlString;
use PDF;

class NewBooking extends Notification
{
    use Queueable, SmtpSettings, SmsSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->smsSetting = SmsSetting::first();

        $this->setMailConfigs();
        $this->setSmsConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail'];

        if ($this->smsSetting->nexmo_status == 'active' && $notifiable->mobile_verified == 1) {
            array_push($via, 'nexmo');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $booking = $this->booking;
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.booking.receipt',compact('booking') );
        $filename = __('app.receipt').' #'.$this->booking->id;

        $mail = new MailMessage();

        $mail->subject(__('email.newBooking.subject').' '.config('app.name').'!')
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
            ->line(__('email.newBooking.text'))
            ->line(__('app.booking').' #'.$this->booking->id);

            if(is_null($this->booking->deal_id)){
                $mail->line(__('app.booking').' '.__('app.date').' - '.$this->booking->date_time->isoFormat('DD MMMM, YYYY - hh:mm A'));
            }

            $mail->action(__('email.loginAccount'), url('/login'))
                ->line(__('email.thankyouNote'));

            if(!is_null($this->booking->deal_id)){
                $mail->attachData($pdf->output(), $filename);
            }

            return $mail->salutation(new HtmlString(__('email.regards').',<br>'.config('app.name')));

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {

        if(is_null($this->booking->deal_id)){
            return (new NexmoMessage)
                ->content(
                __('email.newBooking.text')."\n".
                __('app.booking')." #".$this->booking->id."\n".
                __('app.booking')." ".__('app.date')." - ".$this->booking->date_time->isoFormat('DD MMMM, YYYY - hh:mm A'))->unicode();
        }
        else
        {
            return (new NexmoMessage)
                ->content(
                __('email.newBooking.text')."\n".
                __('app.booking')." #".$this->booking->id."\n")
                ->unicode();
        }
    }
}
