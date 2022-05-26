<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\Channel;
use App\Models\User;
use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DiscordManager implements MessengerContract
{
    const STATUS_CHAT_STARTED = 1;

    private mixed $botToken;

    private mixed $webhookUrl;
    

    // public function __construct()
    // {
    //     $this->botToken = env('DISCORD_BOT_TOKEN');
    //     $this->webhookUrl = env('DISCORD_WEBHOOK_URL');
    // }"

     public function __construct()
    {
        $type = "discord";
        $this->botToken = Channel::getChannelAccessToken($type)->token;
        $this->webhookUrl = Channel::getChannelUri($type)->uri;
    }

    /**
     * @return string
     */
    public static function gateway(): string
    {
        return 'Discord';
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'Discord';
    }

    /**
     * @return string
     */
    public static function description(): string
    {
        return 'Discord is...';
    }

    /**
     * @return integer
     */
    public static function getNewStatusId(): int
    {
        return self::STATUS_CHAT_STARTED;
    }

    /**
     * @param Request $request
     *
     */
    public function handlerWebhookInvoice(Request $request): mixed
    {
        try {
            $discord = new Discord([
                'token' => $this->botToken,
            ]);
        } catch (IntentException $e) {
        }

        $discord->on('ready', function (Discord $discord) {
            // Listen for messages.
            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
                return "{$message->author->username}: {$message->content}";
            });
        });

        $discord->run();

        return;
    }

    /**
     * @param string|array $message
     * @param string|null  $recipient
     *
     * @return Response
     */
    public function sendMessage(string|array $message, string $recipient = null): Response
    {
        return Http::post($this->webhookUrl, [
            'content' => $message['content'],
            'embeds' => [
                [
                    'title' => $message['title'],
                    'description' => $message['description'],
                    'color' => '7506394',
                ],
            ],
        ]);
    }
}
