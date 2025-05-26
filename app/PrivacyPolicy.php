<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    protected $table = "privacy_policies";

    protected $fillable = [
        'privacy_policy',
    ];
}
