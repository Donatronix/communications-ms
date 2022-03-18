<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Monolog\Logger;
use ReflectionException;

class LineManager implements MessengerContract
{

    const STATUS_CHAT_STARTED = 1;

    /**
     * @var mixed
     */
    private mixed $channelAccessToken;

    /**
     * @var mixed
     */
    private mixed $channelSecret;

    /**
     * @var CurlHTTPClient
     */
    private CurlHTTPClient $httpClient;

    /**
     * @var LINEBot
     */
    private LINEBot $bot;

    private Logger $logger;

    public function __construct()
    {
        $this->channelAccessToken = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $this->channelSecret = env('LINE_BOT_CHANNEL_SECRET');

        $this->httpClient = new CurlHTTPClient($this->channelAccessToken);
        $this->bot = new LINEBot($this->httpClient, ['channelSecret' => $this->channelSecret]);
    }

    /**
     * @return string
     */
    public static function gateway(): string
    {
        return 'line';
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'Line';
    }

    /**
     * @return string
     */
    public static function description(): string
    {
        return 'Line is ...';
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
     * @throws ReflectionException
     */
    public function handlerWebhookInvoice(Request $request): array
    {
        $bot = $this->bot;

        $logger = $this->logger;

        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature)) {
            return [
                'error' => true,
                'message' => 'Bad Request',
            ];
        }

        // Check request with signature and parse request
        try {
            $events = $bot->parseEventRequest($request->getContent(), $signature[0]);
        } catch (InvalidSignatureException $e) {
            return [
                'error' => true,
                'message' => 'Invalid signature',
            ];
        } catch (InvalidEventRequestException $e) {
            return [
                'error' => true,
                'message' => "Invalid event request",
            ];
        }

        foreach ($events as $event) {
            if (!($event instanceof MessageEvent)) {
                $logger->info('Non message event has come');
                continue;
            }

            if (!($event instanceof TextMessage)) {
                $logger->info('Non text message has come');
                continue;
            }

            $replyText = $event->getText();
            $logger->info('Reply text: ' . $replyText);
            $resp = $bot->replyText($event->getReplyToken(), $replyText);
            $logger->info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
        }

        return [
            'error' => false,
            'message' => "OK",
        ];


    }

    /**
     * @param string $message
     *
     * @return array
     */
    public function sendMessage(string $message): array
    {
        $textMessageBuilder = new TextMessageBuilder($message);
        $response = $this->bot->replyMessage('<reply token>', $textMessageBuilder);
        if ($response->isSucceeded()) {
            return [
                'error' => false,
                'message' => 'Succeeded!',
            ];

        }

        return [
            'error' => true,
            'message' => $response->getHTTPStatus() . ' ' . $response->getRawBody(),
        ];

    }

}
