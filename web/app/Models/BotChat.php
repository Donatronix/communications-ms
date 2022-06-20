<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;

class BotChat extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    protected $fillable = [
        'message_id', 'date', 'text', 'replied_to_message_id', 'bot_conversation_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * One Chat belongs to one Conversation
     *
     * @return BelongsTo
     */
    public function botconversation(): BelongsTo
    {
        return $this->belongsTo(BotConversation::class);
    }
}
