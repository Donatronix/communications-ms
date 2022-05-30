<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const STATUS_PROCESSING = 1;
    const STATUS_QUEUE_FAIL = 2;
    const STATUS_SENT = 3;

    protected $fillable = [
        'sender_user_id',
        'subject',
        'body',
        'recipient_email',
        'status',
        'note'
    ];
 }
