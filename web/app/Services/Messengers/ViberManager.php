<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
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
        $this->apiKey = env('ViBER_BOT_TOKEN');
        $this->webhookUrl = env('VIBER_WEBHOOK_URL');

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

        try {
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
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
        }

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

    /**
     * @param string $message
     *
     * @return Response
     */
    public function sendMessage(string $message): Response
    {
        return $this->bot->getClient()->sendMessage(
            (new Text())
                ->setSender($this->botSender)
                ->setText($message)
        );
    }

    public function index()
    {
        $this->apiKey = env('VIBER_BOT_CHANNEL_ACCESS_TOKEN');
        // reply name
        $botSender = new Sender([
            'name' => 'Whois bot',
            'avatar' => 'https://developers.viber.com/img/favicon.ico',
        ]);

        // log bot interaction
        $log = new Logger('bot');
        $log->pushHandler(new StreamHandler('/tmp/bot.log'));

        try {
            // create bot instance
            $bot = new Bot(['token' => $this->apiKey]);
            $bot
                ->onConversation(function ($event) use ($bot, $botSender, $log) {
                    $log->info('onConversation ' . var_export($event, true));
                    // this event fires if user open chat, you can return "welcome message"
                    // to user, but you can't send more messages!
                    return (new Text())
                        ->setSender($botSender)
                        ->setText('Can i help you?');
                })
                ->onText('|whois .*|si', function ($event) use ($bot, $botSender, $log) {
                    $log->info('onText whois ' . var_export($event, true));
                    // match by template, for example "whois Bogdaan"
                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setReceiver($event->getSender()->getId())
                            ->setText('I do not know )')
                    );
                })
                ->onText('|.*|s', function ($event) use ($bot, $botSender, $log) {
                    $log->info('onText ' . var_export($event, true));
                    // .* - match any symbols
                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setReceiver($event->getSender()->getId())
                            ->setText('HI!')
                    );
                })
                ->onPicture(function ($event) use ($bot, $botSender, $log) {
                    $log->info('onPicture ' . var_export($event, true));
                    $bot->getClient()->sendMessage(
                        (new Text())
                            ->setSender($botSender)
                            ->setReceiver($event->getSender()->getId())
                            ->setText('Nice picture ;-)')
                    );
                })
                ->on(function ($event) {
                    return true; // match all
                }, function ($event) use ($log) {
                    $log->info('Other event: ' . var_export($event, true));
                })
                ->run();
        } catch (Exception $e) {
            $log->warning('Exception: ', $e->getMessage());
            if ($bot) {
                $log->warning('Actual sign: ' . $bot->getSignHeaderValue());
                $log->warning('Actual body: ' . $bot->getInputBody());
            }
        }
    }
}
