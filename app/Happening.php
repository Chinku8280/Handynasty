<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Happening extends Model
{
    protected $table = "happenings";

    protected $fillable = [
        'title',
        'image',
        'description',
        'status',
        'start_date_time',
        'end_date_time',
    ];
}
