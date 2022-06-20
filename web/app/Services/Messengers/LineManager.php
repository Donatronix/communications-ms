<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        $settings = Channel::getChannelSettings('line');

        $this->channelAccessToken = $settings->token;
        $this->channelSecret = $settings->secret;

        $this->httpClient = new CurlHTTPClient($this->channelAccessToken);

        $this->bot = new LINEBot($this->httpClient, ['channelSecret' => $this->channelSecret]);

        /**/
        $this->apiReply = Setting::getApiReply();

        $this->apiPush = Setting::getApiPush();

        $this->webhookResponse = file_get_contents('php://input');

        $this->webhookEventObject = json_decode($this->webhookResponse);
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
     * @param string|array $message
     * @param string|null $recipient
     *
     * @return array
     */
    public function sendMessage(string|array $message, string $recipient = null): array
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

    public function index(Response $response)
    {
        // get request body and line signature header
        $body = file_get_contents('php://input');
        //$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

        // log body and signature
        file_put_contents('php://stderr', 'Body: ' . $body);

        // is LINE_SIGNATURE exists in request header?
        /*if (empty($signature)){
            return $response->withStatus(400, 'Signature not set');
        }

        // is this request comes from LINE?
        if($_ENV['PASS_SIGNATURE'] == false && ! SignatureValidator::validateSignature($body, $_ENV['CHANNEL_SECRET'], $signature)){
            return $response->withStatus(400, 'Invalid signature');
        }*/

        // init bot
        $httpClient = new CurlHTTPClient($this->channelAccessToken);
        $bot = new LINEBot($httpClient, ['channelSecret' => $this->channelSecret]);

        $data = json_decode($body, true);

        foreach ($data['events'] as $event) {
            $userMessage = $event['message']['text'];
            if (strtolower($userMessage) == 'hello') {
                $message = "Pradeep";
                $textMessageBuilder = new TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);

                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }
        }
    }
}
