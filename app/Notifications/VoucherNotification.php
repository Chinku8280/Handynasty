<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VoucherNotification extends Notification
{
    use Queueable;

    protected $voucher_notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($voucher_notification)
    {
        $this->voucher_notification = $voucher_notification;
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
            'voucher_id' => $this->voucher_notification['voucher_id'],
            'title' => $this->voucher_notification['title'],
            'body' => $this->voucher_notification['body'],
            'upload_date' => $this->voucher_notification['upload_date'],
            'target_page' => $this->voucher_notification['target_page'],
        ];
    }
}
