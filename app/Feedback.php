<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = "feedback";

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'country',
        'message',
    ];
}
