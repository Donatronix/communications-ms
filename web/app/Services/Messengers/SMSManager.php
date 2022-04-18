<?php


namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use Illuminate\Http\Request;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;
use function env;

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
        $this->twilioSid = env('TWILIO_ACCOUNT_SID');
        $this->twilioAuthToken = env('TWILIO_AUTH_TOKEN');
        $this->twilioNumber = env('TWILIO_NUMBER');

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
     * @param string|null  $recipient
     *
     * @return MessageInstance
     * @throws TwilioException
     */
    public function sendMessage(string|array $message, string $recipient = null): MessageInstance
    {
        return $this->client->messages->create($recipient, ['from' => $this->twilioNumber, 'body' => $message]);
    }
}
