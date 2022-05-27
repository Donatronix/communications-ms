<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\Channel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;

class TelegramManager implements MessengerContract
{
    const STATUS_CHAT_STARTED = 1;

    /**
     * @var Api
     */
    public Api $object;

    /**
     * @var mixed
     */
    private mixed $chatId;

    /**
     * @throws TelegramSDKException
     */
    public function __construct()
    {
        $type = "telegram";
        $this->chatId = env('TELEGRAM_CHAT_ID', Channel::getChannelSettings($type)->sid);
        $this->object = new Api(env('TELEGRAM_BOT_TOKEN', Channel::getChannelSettings($type)->token), true);
    }

    /**
     * @return string
     */
    public static function gateway(): string
    {
        return 'telegram';
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'Telegram';
    }

    /**
     * @return string
     */
    public static function description(): string
    {
        return 'Telegram is...';
    }

    /**
     * @return integer
     */
    public static function getNewStatusId(): int
    {
        return self::STATUS_CHAT_STARTED;
    }

    /**
     * @param string|array $message
     * @param string|null  $recipient
     *
     * @return Message
     * @throws TelegramSDKException
     */
    public function sendMessage(string|array $message, string $recipient = null): Message
    {

        if (request()->hasFile('file')) {
            $file = request()->file('file');
            $path = $file->getPath();

            $mime = $file->getMimeType();
            if (str_contains($mime, "video/")) {
                return $this->object->sendVideo(['chat_id' => $this->chatId, 'video' => $path]);
            } elseif (str_contains($mime, "image/")) {
                return $this->object->sendPhoto(['chat_id' => $this->chatId, 'photo' => $path]);
            } elseif (str_contains($mime, "audio/")) {
                return $this->object->sendAudio(['chat_id' => $this->chatId, 'photo' => $path]);
            } else {
                return $this->object->sendDocument(['chat_id' => $this->chatId, 'photo' => $path]);
            }
        }

        return $this->object->sendMessage([
            'chat_id' => $this->chatId,
            'text' => $message,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws TelegramSDKException
     */
    public function handlerWebhookInvoice(Request $request): mixed
    {
        $updates = $this->object->getUpdates();
        if ($updates) {
            return $updates;
        }

        return $this->object->chatJoinRequest([
            'chat' => $this->object->getChat($this->chatId),
            'from' => $request->user(),
            'date' => Carbon::now(),
            'invite_link' => $request->invite_link,
        ]);
    }
}
