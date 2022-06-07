<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;

class Message extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;
    
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
