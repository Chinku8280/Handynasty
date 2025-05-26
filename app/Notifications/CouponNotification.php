<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CouponNotification extends Notification
{
    use Queueable;

    protected $coupon_notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($coupon_notification)
    {
        $this->coupon_notification = $coupon_notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // return ['mail'];
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'coupon_id' => $this->coupon_notification['coupon_id'],
            'title' => $this->coupon_notification['title'],
            'body' => $this->coupon_notification['body'],
            'upload_date' => $this->coupon_notification['upload_date'],
            'target_page' => $this->coupon_notification['target_page'],
        ];
    }
}
