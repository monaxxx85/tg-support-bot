<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTopic extends Model
{

    protected $fillable = [
        'user_id',
        'thread_id',
        'username',
        'first_name',
        'last_name',
        'phone_number',
    ];
}
