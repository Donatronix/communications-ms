<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;

class Conversation extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    protected $fillable = [
        'first_user_id', 'second_user_id', 'status',
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

    public static function validationRules(): array
    {
        return [
            'second_user_id' => 'required|string',
        ];
    }

    /**
     * One Conversation has many Chats
     *
     * @return HasMany
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
