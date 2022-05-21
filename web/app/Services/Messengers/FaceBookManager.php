<?php

namespace App\Services\Messengers;

use App\Contracts\MessengerContract;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use Telegram\Bot\Api;
// use Telegram\Bot\Exceptions\TelegramSDKException;
// use Telegram\Bot\Objects\Message;

class FaceBookManager implements MessengerContract
{

    const STATUS_CHAT_STARTED = 1;

    /**
     * The verification token for Facebook
     *
     * @var string
     */
    protected $token;


    public function __construct()
    {
        $this->token = env('FACEBOOK_BOT_VERIFY_TOKEN');
    }

    /**
     * @return integer
     */
    public static function getNewStatusId(): int
    {
        return self::STATUS_CHAT_STARTED;
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
     * Verify the token from Messenger. This helps verify your bot.
     *
     * @param  Request $request
     * @return Response
     */
    public function verifyToken(Request $request)
    {
        $mode  = $request->get('hub_mode');
        $token = $request->get('hub_verify_token');

        if ($mode === "subscribe" && $this->token and $token === $this->token)
        {
            return response($request->get('hub_challenge'));
        }

        return response("Invalid token!", 400);
    }


    /**
     * Post a message to the Facebook messenger API.
     *
     * @param  integer $id
     * @param  string  $response
     * @return bool
     */
    protected function dispatchResponse($id, $response)
    {
        $result = array();
        $access_token = env('FACEBOOK_BOT_PAGE_ACCESS_TOKEN');


        return $result;
    }


    /**
     * Handle the query sent to the bot.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function handlerWebhookInvoice(Request $request): mixed
    {
        $entry = $request->get('entry');

        $sender  = array_get($entry, '0.messaging.0.sender.id');
        // $message = array_get($entry, '0.messaging.0.message.text');

        $this->dispatchResponse($sender, 'Hello world. You can customise my response.');

        return response('', 200);
    }


    /**
     * @param string|array $message
     * @param string|null  $recipient
     *
     * @return Message
     * @throws TelegramSDKException
     */
    public function sendMessage(string|array $message, string $recipient = null): Message
    {


    }

}
