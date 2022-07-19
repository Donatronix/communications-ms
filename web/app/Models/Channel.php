<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;

/**
 * Channel Scheme
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     schema="ChannelSchema",
 *
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of Channel",
 *         minLength=2,
 *         maxLength=100,
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="uri",
 *         type="string",
 *         description="URI of channel (username)",
 *         example="@channelname"
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         description="Acces token of Channel",
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
 *     ),
 *    @OA\Property(
 *         property="number",
 *         type="string",
 *         description="Channel number",
 *         example="+8056788888"
 *     ),
 * )
 */
class Channel extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    /**
     * Channel status
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * Messengers
     */
    const MESSENGER_TELEGRAM     = 'telegram';
    const MESSENGER_VIBER        = 'viber';
    const MESSENGER_LINE         = 'line';
    const MESSENGER_DISCORD      = 'discord';
    const MESSENGER_SIGNAL       = 'signal';
    const MESSENGER_WHATSAPP     = 'whatsapp';
    const MESSENGER_TWILIO       = 'twilio';
    const MESSENGER_NEXMO        = 'nexmo';
    const MESSENGER_FACEBOOK     = 'facebook';

    /**
     * Platforms
     */
    const PLATFORM_ULTAINFINITY = 'ultainfinity';
    const PLATFORM_SUMRA = 'sumra';

    /**
     * Type of channel
     */
    const TYPE_AUTH = 'auth';
    const TYPE_INFO = 'info';
    const TYPE_CHAT = 'chat';

    /**
     * @var array|string[]
     */
    public static array $types = [
        0 => self::TYPE_AUTH,
        1 => self::TYPE_INFO,
        2 => self::TYPE_CHAT
    ];

    /**
     * Currency statuses array
     *
     * @var int[]
     */
    public static array $statuses = [
        0 => self::STATUS_INACTIVE,
        1 => self::STATUS_ACTIVE,
    ];

    /**
     * @var array|string[]
     */
    public static array $platforms = [
        0 => self::PLATFORM_ULTAINFINITY,
        1 => self::PLATFORM_SUMRA,
    ];

    /**
     * @var array|string[]
     */
    public static array $messengers = [
        0 => self::MESSENGER_TELEGRAM,
        1 => self::MESSENGER_VIBER,
        2 => self::MESSENGER_LINE,
        3 => self::MESSENGER_DISCORD,
        4 => self::MESSENGER_SIGNAL,
        5 => self::MESSENGER_WHATSAPP,
        6 => self::MESSENGER_TWILIO,
        7 => self::MESSENGER_NEXMO,
        8 => self::MESSENGER_FACEBOOK,
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
     * @param $messenger
     * @return mixed
     */
    public static function getChannelSettings($messenger){
        return Channel::where('messenger', $messenger)
            ->where("platform", env('APP_PLATFORM'))
            ->get()->last();
    }

    public static function validationRules(): array
    {
        return [
            'name' => 'required|string|min:4',
            'token' => 'required|string|min:30',
            'uri' => 'required|string|min:4',
            'type' => 'string|min:4',
            'platform' => 'string|min:4',
            'webhook_url' => 'string',
        ];
    }
}
