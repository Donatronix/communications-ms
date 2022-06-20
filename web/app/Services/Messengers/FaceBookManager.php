<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\Channel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHTTP\Client;


class FaceBookManager implements MessengerContract
{
    const STATUS_CHAT_STARTED = 1;

    /**
     * The verification token for Facebook
     *
     * @var string
     */
    protected $access_token;

    /**
     * @var Api
     */
    //public Api $object;

    /**
     * @var mixed
     */
    private mixed $chatId;

    /**
     * @throws FacebookSDKException
     */
    public function __construct()
    {
        $settings = Channel::getChannelSettings('facebook');

//        $this->webHookUrl   = env('FACEBOOK_MESSENEGR_URL');
//        $this->accessToken  = env('FACEBOOK_MESSENGER_ACCESS_TOKEN');

        $this->verify_token = $settings->token;
    }

    /**
     * @return string
     */
    public static function gateway(): string
    {
        return 'facebook';
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'Facebook';
    }

    /**
     * @return string
     */
    public static function description(): string
    {
        return 'Facebook is...';
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
     * @return Message
     * @throws FacebookSDKException
     */
    public function sendMessage(string|array $message, string $recipient = null): Message
    {
        $url = $this->webHookUrl . $this->accessToken;

        $data = json_encode([
            'message'   => ['text' => $message],
            'recipient' => ['id' => $recipient],
        ]);

        $client = new Client(['base_uri'=>$url, 'timeout' => 5.0]);
        $response = $client->request('POST', $data);
        //$request  = $client->post($url,  $data);
        //$response = $request->send();

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws FacebookSDKException
     */
    public function handlerWebhookInvoice(Request $request): mixed
    {
        try{
            $messageObject = $request->all();

            $entry = $request->get('entry');

            if ($messageObject['object'] != 'page') {
                return response('Message must be sent from page', 403);
            }
            // maybe you want to log the response to debug
            // app('log')->debug(json_encode($messageObject));
            $sender  = $messageObject['entry'][0]['messaging'][0]['sender']['id'];
            $message = $messageObject['entry'][0]['messaging'][0]['message'];
            $text    = isset($message['text']) ? $message['text'] : 'what ?';
            $data = [
                'message' => [
                    'text' => $text
                ],
                'recipient' => [
                    'id' => $sender
                ]

            ];

            return response($this->sendMessage($data), 200);
        }catch(\Throwable $e){
            return response($e, 500);
        }
    }
}
