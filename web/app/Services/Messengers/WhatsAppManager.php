<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class WhatsAppManager implements MessengerContract
{
    const STATUS_CHAT_STARTED = 1;

    private mixed $twilioSid;

    private mixed $twilioAuthToken;

    private mixed $twilioWhatsappNumber;

    private Client $client;

    public function __construct()
    {
        $this->twilioSid = env('TWILIO_SID');
        $this->twilioAuthToken = env('TWILIO_AUTH_TOKEN');
        $this->twilioWhatsappNumber = env('TWILIO_WHATSAPP_NUMBER');

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
     * @return mixed
     * @throws GuzzleException
     */
    public function handlerWebhookInvoice(Request $request): mixed
    {
        $from = $request->input('From');
        $body = $request->input('Body');

        $client = new Client();
        try {
            $response = $client->request('GET', "https://api.github.com/users/$body");
            $githubResponse = json_decode($response->getBody());
            if ($response->getStatusCode() == 200) {
                $message = "*Name:* $githubResponse->name\n";
                $message .= "*Bio:* $githubResponse->bio\n";
                $message .= "*Lives in:* $githubResponse->location\n";
                $message .= "*Number of Repos:* $githubResponse->public_repos\n";
                $message .= "*Followers:* $githubResponse->followers devs\n";
                $message .= "*Following:* $githubResponse->following devs\n";
                $message .= "*URL:* $githubResponse->html_url\n";
                $this->sendMessage($message, $from);
            } else {
                $this->sendMessage($githubResponse->message, $from);
            }
        } catch (RequestException $th) {
            $response = json_decode($th->getResponse()->getBody());
            $this->sendMessage($response->message, $from);
        }
        return;
    }

    /**
     * @param string $message
     * @param string $recipient
     *
     * @return mixed
     */
    public function sendMessage(string $message, string $recipient): mixed
    {
        $twilio_whatsapp_number = $this->twilioWhatsappNumber;

        return $this->client->messages->create($recipient, ['from' => "whatsapp:$twilio_whatsapp_number", 'body' => $message]);
    }


}
