<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationDetail extends Model
{
    protected $table = "notification_details";

    protected $fillable = [
        'title',
        'body',
        'scheduled_at',
        'send_push_notification',
    ];
}
