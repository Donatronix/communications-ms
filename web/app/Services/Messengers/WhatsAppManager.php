<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\User;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;

class WhatsAppManager implements MessengerContract
{
    const STATUS_CHAT_STARTED = 1;

    private mixed $twilioSid;

    private mixed $twilioAuthToken;

    private mixed $twilioWhatsappNumber;

    private Client $client;

    /**
     * @throws ConfigurationException
     */
    public function __construct()
    {
        $type = "twilio";
        $this->twilioSid = env('TWILIO_ACCOUNT_SID', User::getChannelSid($type)->sid);
        $this->twilioAuthToken = env('TWILIO_AUTH_TOKEN', User::getChannelAccessToken($type)->token);
        $this->twilioWhatsappNumber = env('TWILIO_WHATSAPP_NUMBER', User::getChannelNumber($type)->number);

        $this->client = new Client($this->twilioSid, $this->twilioAuthToken);
    }

    /**
     * @return string
     */
    public static function gateway(): string
    {
        return 'whatsapp';
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'WhatsApp';
    }

    /**
     * @return string
     */
    public static function description(): string
    {
        return 'WhatsApp is...';
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
     * @return MessagingResponse|void
     * @throws TwilioException
     */
    public function handlerWebhookInvoice(Request $request): ?MessagingResponse
    {
        $from = $request->input('from', $this->twilioWhatsappNumber);

        try {
            // Get number of images in the request
            $numMedia = (int)$request->input("NumMedia");

            Log::debug("Media files received: {$numMedia}");

            $response = new MessagingResponse();
            if ($numMedia != 0) {
                $message = $response->message("Thanks for the image!");
            } else {
                $message = $response->message("Thanks for the message!");
            }

            return $response;
        } catch (RequestException $th) {
            $response = json_decode($th->getResponse()->getBody());
            $this->sendMessage($response->message, $from);
        } catch (TwilioException $e) {
            $this->sendMessage($e->getMessage(), $from);
        }

        return;
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
        return $this->client->messages->create("whatsapp:$recipient", ['from' => "whatsapp:$this->twilioWhatsappNumber", 'body' => $message]);
    }
}
