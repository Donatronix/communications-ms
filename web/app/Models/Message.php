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

    protected $fillable = [
        'sender_user_id', 'subject', 'body', 'recipient_email',
    ];
 }
