<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\User;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Throwable;
use Viber\Api\Event;
use Viber\Api\Event\DELIVERED;
use Viber\Api\Event\FAILED;
use Viber\Api\Event\SEEN;
use Viber\Api\Event\Type;
use Viber\Api\Keyboard;
use Viber\Api\Keyboard\Button;
use Viber\Api\Message\Text;
use Viber\Api\Response;
use Viber\Api\Sender;
use Viber\Bot;
use Viber\Client;

class ViberManager implements MessengerContract
{
    /**
     *
     */
    const STATUS_CHAT_STARTED = 1;

    /**
     * @var string|mixed
     */
    private string $apiKey;

    /**
     * @var string|mixed
     */
    private string $webhookUrl;

    /**
     * @var string
     */
    private string $url_api = "https://chatapi.viber.com/pa/";

    /**
     * @var Sender
     */
    private Sender $botSender;

    /**
     * @var Logger
     */
    private Logger $log;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var mixed
     */
    private mixed $senderId;

    /**
     * @var Bot
     */
    private Bot $bot;

    /**
     *
     */
    public function __construct()
    {
        $type = "viber";
        $this->apiKey = User::getChannelAccessToken($type)->token;
        $this->webhookUrl = User::getChannelUri($type)->uri;

        $this->client = new Client(['token' => $this->apiKey]);
        $result = $this->client->setWebhook($this->webhookUrl, [
            Type::DELIVERED,  // if message delivered to device
            Type::SEEN,       // if message is seen device
            Type::FAILED,     // if message not delivered
            Type::SUBSCRIBED,
            Type::UNSUBSCRIBED,
            Type::CONVERSATION,
            Type::MESSAGE,
        ]);

        $this->botSender = new Sender([
            'name' => 'Reply bot',
            'avatar' => 'https://developers.viber.com/img/favicon.ico',
        ]);

        // log bot interaction
        $this->log = new Logger('viberBot');
        $this->log->pushHandler(new StreamHandler('/tmp/bot.log'));

        $this->bot = new Bot(['token' => $this->apiKey]);
    }

    /**
     * @return mixed
     */
    public static function gateway(): string
    {
        return 'viber';
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'Viber';
    }

    /**
     * @return string
     */
    public static function description(): string
    {
        return 'Viber is ...';
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
     * @return Response
     */
    public function sendMessage(string|array $message, string $recipient = null): Response
    {
        return $this->bot->getClient()->sendMessage(
            (new Text())
                ->setSender($this->botSender)
                ->setText($message)
        );
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function handlerWebhookInvoice(Request $request): array
    {
        try {
            $botSender = $this->botSender;
            $log = $this->log;
            $bot = $this->bot;
            $bot
                // first interaction with bot - return "welcome message"
                ->onConversation(function ($event) use ($bot, $botSender, $log) {
                    $log->info('onConversation handler');
                    $this->senderId = $event->getSender()->getId();
                    $buttons = [];
                    for ($i = 0; $i <= 8; $i++) {
                        $buttons[] =
                            (new Button())
                                ->setColumns(1)
                                ->setActionType('reply')
                                ->setActionBody('k' . $i)
                                ->setText('k' . $i);
                    }

                    return (new Text())
                        ->setSender($botSender)
                        ->setText("Hi, welcome to our chat bot")
                        ->setKeyboard(
                            (new Keyboard())
                                ->setButtons($buttons)
                        );
                })
                // when user subscribe to PA
                ->onSubscribe(function ($event) use ($bot, $botSender, $log) {
                    $this->senderId = $event->getSender()->getId();
                    $log->info('onSubscribe handler');
                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setText('Thanks for subscription!')
                    );
                })
                ->onText('|.*|s', function ($event) use ($bot, $botSender, $log) {
                    // .* - match any symbols (see PCRE)
                    $log->info('onText handler');
                    $this->senderId = $event->getSender()->getId();
                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setReceiver($this->senderId)
                            ->setMinApiVersion(3)
                            ->setText("Hi! We need your phone number")
                            ->setKeyboard(
                                (new Keyboard())
                                    ->setButtons([
                                        (new Button())
                                            ->setActionType('share-phone')
                                            ->setActionBody('reply')
                                            ->setText('Send phone number'),
                                    ])
                            )
                    );
                })
                ->on(function (Event $event) {
                    return ($event instanceof DELIVERED);
                }, function ($event) {
                    // process delivered
                })
                ->on(function (Event $event) {
                    return ($event instanceof SEEN);
                }, function ($event) {
                    // process seen
                })
                ->on(function (Event $event) {
                    return ($event instanceof FAILED);
                }, function ($event) {
                    // process failed
                })
                ->run();
        } catch (Throwable $th) {
            $log->error($th->getMessage());
        }
        return [];

    }


}
