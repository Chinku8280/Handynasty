<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CategoryNotification extends Notification
{
    use Queueable;

    protected $category_notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($category_notification)
    {
        $this->category_notification = $category_notification;
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
            'category_id' => $this->category_notification['category_id'],
            'title' => $this->category_notification['title'],
            'body' => $this->category_notification['body'],
            'upload_date' => $this->category_notification['upload_date'],
            'target_page' => $this->category_notification['target_page'],
        ];
    }
}
