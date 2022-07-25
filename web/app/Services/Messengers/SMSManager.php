<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
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
     * @throws ConfigurationException
     */
    public function __construct()
    {
        $settings = Channel::getChannelSettings('twilio');

        $this->twilioSid = $settings->sid;
        $this->twilioAuthToken = $settings->token;
        $this->twilioNumber = $settings->number;

        $this->client = new Client($this->twilioSid, $this->twilioAuthToken);
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
     * @return MessageInstance
     * @throws TwilioException
     */
    public function sendMessage(string|array $message, string $recipient = null): MessageInstance
    {
        // Check if not exist '+'
        if(Str::startsWith($recipient, '+')){
            $recipient = '+' . $recipient;
        }

        // Send message
        return $this->client->messages->create($recipient, [
            'from' => $this->twilioNumber,
            'body' => $message
        ]);
    }
}
