<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sumra\SDK\Traits\UuidTrait;

class Message extends Model
{
    use HasFactory;
    use UuidTrait;

    /**
     * Message statuses
     */
    const STATUS_PROCESSING = 10;
    const STATUS_FAILURE = 20;
    const STATUS_SENT = 30;

    /**
     * @var string[]
     */
    protected $fillable = [
        'subject',
        'body',
        'recipient',
        'sender_id',
        'status',
        'note'
    ];
 }
