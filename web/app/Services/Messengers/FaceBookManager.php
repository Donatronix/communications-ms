<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use App\Models\Channel;
use Carbon\Carbon;
use Illuminate\Http\Request;


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
        $type = "facebook";
        $this->verify_token = Channel::getChannelSettings($type)->token;

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
     * Verify the token from Messenger. This helps verify your bot.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function verify_token($request)
    {
        $mode  = $request->get('hub_mode');
        $token = $request->get('hub_verify_token');

        if ($mode === "subscribe" && $token === $this->verify_token) {
            return ($request->get('hub_challenge'));
        }
        // if ($request->input('hub_mode') == 'subscribe' &&
        //         $request->input('hub_verify_token') == $this->verify_token) {
                    //return $request->input('hub_challenge');
        //
        //     }//  return response('You are not authorized', 403);


        return false;
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

        $url = env('FACEBOOK_MESSENEGR_URL', '');

        $data = json_encode([
            'message'   => ['text' => $message],
            'recipient' => ['id' => $recipient],
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;

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
