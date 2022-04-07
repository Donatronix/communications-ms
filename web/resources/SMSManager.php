<?php


use App\Contracts\MessengerContract;
use Illuminate\Http\Request;
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

    public function __construct()
    {
        $this->twilioSid = env('TWILIO_SID');
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
        // TODO: Implement handlerWebhookInvoice() method.
    }

    /**
     * @param string      $message
     * @param string|null $recipient
     *
     * @return mixed
     */
    public function sendMessage(string $message, string $recipient = null): mixed
    {
        return $this->client->messages->create($recipient, ['from' => $this->twilioNumber, 'body' => $message]);
    }
}
