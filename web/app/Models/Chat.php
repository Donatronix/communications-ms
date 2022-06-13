<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;

class Chat extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    protected $fillable = [
        'message', 'is_delivered', 'is_seen', 'deleted_from_sender', 'deleted_from_receiver', 'user_id', 'conversation_id',
    ];
}
