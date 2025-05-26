<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermsAndCondition extends Model
{
    protected $table = "terms_and_conditions";

    protected $fillable = [
        'terms_condition',
    ];

}
