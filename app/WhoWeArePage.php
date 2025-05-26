<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhoWeArePage extends Model
{
    protected $table = 'who_we_are_pages';

    protected $fillable = [
        'title',
        'image',
        'description',
    ];
}
