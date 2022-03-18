<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
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
        $this->chatId = env('TELEGRAM_CHAT_ID');
        $this->object = new Api(env('TELEGRAM_BOT_TOKEN'), true);
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
     * @return Message
     * @throws TelegramSDKException
     */
    public function sendMessage(): Message
    {

        if (request()->hasFile('file')) {
            $file = request()->file('file');
            $path = $file->getPath();

            $mime = $file->getMimeType();
            if (str_contains($mime, "video/")) {
                return $this->object->sendVideo(['chat_id' => $this->chatId, 'video' => $path]);
            } else if (str_contains($mime, "image/")) {
                return $this->object->sendPhoto(['chat_id' => $this->chatId, 'photo' => $path]);
            } else if (str_contains($mime, "audio/")) {
                return $this->object->sendAudio(['chat_id' => $this->chatId, 'photo' => $path]);
            } else {
                return $this->object->sendDocument(['chat_id' => $this->chatId, 'photo' => $path]);
            }
        }

        return $this->object->sendMessage([
            'chat_id' => $this->chatId,
            'text' => request()->message,
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
