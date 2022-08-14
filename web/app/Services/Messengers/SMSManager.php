<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class SMSManager implements MessengerContract
{
    const STATUS_CHAT_STARTED = 'sms_started';

    /**
     * @var mixed
     */
    private mixed $twilioSid;

    /**
     * @var mixed
     */
    private mixed $twilioAuthToken;

    /**
     * @var mixed
     */
    private mixed $twilioNumber;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $settings = Channel::getChannelSettings('twilio');

        $this->twilioSid = $settings->sid;
        $this->twilioAuthToken = $settings->token;
        $this->twilioNumber = $settings->number;

        try {
            $this->client = new Client($this->twilioSid, $this->twilioAuthToken);
        } catch (ConfigurationException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * @return string
     */
    public static function gateway(): string
    {
        return 'sms';
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'SMS';
    }

    /**
     * @return string
     */
    public static function description(): string
    {
        return 'SMS is...';
    }

    /**
     * @return integer
     */
    public static function getNewStatusId(): int
    {
        return self::STATUS_CHAT_STARTED;
    }

    /**
     * @inheritDoc
     */
    public function handlerWebhookInvoice(Request $request): mixed
    {
        //
    }

    /**
     * @param string|array $message
     * @param string|null $recipient
     *
     * @return object|string
     * @throws TwilioException
     * @throws \Exception
     */
    public function sendMessage(string|array $message, string $recipient = null): object|string
    {
        // Check if not exist '+'
        if (!Str::startsWith($recipient, '+')) {
            $recipient = '+' . $recipient;
        }

        try {
            // Send message
            $result = $this->client->messages->create(
                $recipient,
                [
                    'from' => $this->twilioNumber,
                    'body' => $message
                ]
            );

            $response = $this->client->getHttpClient()->lastResponse;

            return (object)[
                'content' => $response->getContent(),
                'headers' => $response->getHeaders(),
                'code' => $response->getStatusCode()
            ];
        } catch (TwilioException $e) {
            throw new \Exception($e);
        }
    }
}
