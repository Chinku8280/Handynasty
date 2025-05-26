<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discover extends Model
{
    protected $table = "discovers";

    protected $fillable = [
        'title',
        'image',
        'description',
        'status',
        'off_percentage',
    ];
}
