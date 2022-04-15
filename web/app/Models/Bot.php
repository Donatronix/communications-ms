<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;

/**
 * Bot Scheme
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     schema="BotSchema",
 *
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of Bot",
 *         minLength=2,
 *         maxLength=100,
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="uri",
 *         type="string",
 *         description="URI of bot (username)",
 *         example="@botname"
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         description="Acces token of Bot",
 *         example="1000000"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="enum",
 *         description="Period in days",
 *         example="10"
 *     ),
 *     @OA\Property(
 *         property="platform",
 *         type="enum",
 *         description="Period in days",
 *         example="10"
 *     )
 * )
 */
class Bot extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    /**
     * Bot status
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     *
     */
    const TYPE_TELEGRAM = 'telegram';
    const TYPE_VIBER = 'viber';
    const TYPE_LINE = 'line';
    const TYPE_DISCORD = 'discord';
    const TYPE_SIGNAL = 'signal';
    const TYPE_WHATSAPP = 'whatsapp';
    const TYPE_TWILIO = 'twilio';
    const TYPE_NEXMO = 'nexmo';

    /**
     *
     */
    const PLATFORM_ULTAINFINITY = 'ultainfinity';
    const PLATFORM_SUMRA = 'sumra';

    /**
     * Currency statuses array
     *
     * @var int[]
     */
    public static array $statuses = [
        0 => self::STATUS_INACTIVE,
        1 => self::STATUS_ACTIVE
    ];

    public static array $platforms = [
        0 => self::PLATFORM_ULTAINFINITY,
        1 => self::PLATFORM_SUMRA
    ];

    public static array $types = [
        0 => self::TYPE_TELEGRAM,
        1 => self::TYPE_VIBER,
        2 => self::TYPE_LINE,
        3 => self::TYPE_DISCORD,
        4 => self::TYPE_SIGNAL,
        5 => self::TYPE_WHATSAPP,
        6 => self::TYPE_TWILIO,
        7 => self::TYPE_NEXMO,
    ];

    public static function validationRules()
    {
        return [
            'name' => 'required|string|min:4',
            'token' => 'required|string|min:30',
            'uri' => 'required|string|min:4',
            'type' => 'string|min:4',
            'platform' => 'string|min:4',
            'webhook_url' => 'string'
        ];
    }
}
