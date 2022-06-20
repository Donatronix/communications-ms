<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;


/**
 * BotDetail Scheme
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     schema="BotDetailSchema",
 *
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of bot",
 *         example="MyBot"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Username of bot",
 *         example="my_bot"
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         description="Access token for bot",
 *         example="36374819605:GSF4oK7H50GFSg4*uTPT9puKrAd6TKBFF6Ks"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="enum",
 *         description="Type of bot",
 *         example="telegram"
 *     ),
 * )
 */
class BotDetail extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'type', 'name', 'token', 'username',
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
}
