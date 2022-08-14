<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;

class BotConversation extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bot_name',
        'bot_username',
        'chat_id',
        'first_name',
        'last_name',
        'bot_type'
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
     * One Conversation has many Chats
     *
     * @return HasMany
     */
    public function botchats(): HasMany
    {
        return $this->hasMany(BotChat::class);
    }
}
