<?php

namespace App\Api\V1\Controllers\Webhooks;

use App\Api\V1\Controllers\Controller;
use App\Models\BotChat;
use App\Models\BotConversation;
use App\Models\BotDetail;
use Exception;
use Illuminate\Http\Request;
use Log;
use Sumra\SDK\Facades\PubSub;

/**
 * Class BotMessageController
 *
 * @package App\Api\V1\Controllers\Application
 */
class BotMessageController extends Controller
{
    private const RECEIVER_LISTENER = "getOwnerByPhone";
    private BotDetail $botdetail;
    private BotConversation $botconversation;
    private BotChat $botchat;

    /**
     * BotMessageController constructor.
     *
     * @param BotDetail $botdetail
     * @param BotConversation $botconversation
     * @param BotChat $botchat
     */
    public function __construct(BotDetail $botdetail, BotConversation $botconversation, BotChat $botchat)
    {
        $this->botdetail = $botdetail;
        $this->botconversation = $botconversation;
        $this->botchat = $botchat;
        $this->user_id = auth()->user()->getAuthIdentifier();
    }

    /**
     * Save updates from bot webhook
     *
     * @param Request $request , $type, $token
     * @return mixed
     *
     * @OA\Post(
     *     path="/saveUpdates/{bot_type}/{token}",
     *     summary="Save updates from bot webhook",
     *     description="Save updates from bot webhook",
     *     tags={"Webhook"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *
     *     @OA\Parameter(
     *         name="bot_type",
     *         in="path",
     *         description="type of bot messenger",
     *         example="telegram",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         description="token by the bot for authentication",
     *         example="36374819605:GSF4oK7H50GFSg4*uTPT9puKrAd6TKBFF6Ks",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success send data"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function saveUpdates(Request $request, $type, $token)
    {
        // Try to save updates sent from bot
        try {
            // get bot details using token
            $botdetail = $this->botdetail->where('token', $token)->first();

            if ($type == "telegram") {
                $data = $this->saveTelegramUpdates($request, $botdetail, $type, $token);
            }

            if ($type == "viber") {
                $data = $this->saveViberUpdates($request, $botdetail, $type, $token);
            }

            Log::info("Update has been saved");

            // Return response
            return response()->jsonApi([
                'title' => "send message",
                'message' => 'Your message has been sent',
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::info($e->getMessage());

            return response()->jsonApi([
                'title' => "send message",
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Private method to save Telegram Updates
     *
     * @param Request $request , $botdetail, $type, $token
     * @return mixed
     */
    private function saveTelegramUpdates($request, $botdetail, $type, $token)
    {
        // call telegram bot api
        if ($request->has('update_id')) {
            // save bot chat and conversation
            $data = $request->get('message');


            // check whether message is replying to another message
            if (array_key_exists("reply_to_message", $data)) {
                $replied_to_message_id = $data['reply_to_message']['message_id'];
            } else {
                $replied_to_message_id = null;
            }

            $inputData = [
                'bot_name' => $botdetail->name,
                'bot_username' => $botdetail->username,
                'chat_id' => $data['chat']['id'],
                'first_name' => $data['chat']['first_name'],
                'bot_type' => $type,
                'last_name' => $data['chat']['last_name'],
                'replied_to_message_id' => $replied_to_message_id,
                'message_id' => $data['message_id'],
                'sender' => $data['chat']['first_name'],
                'receiver' => $botdetail->name,
                'date' => $data['date'],
                'text' => $data['text'],
                'token' => $token,
            ];

            // save bot chat and conversation
            $this->saveBotChats($inputData, $botdetail->user_id);

            return $data;
        }
    }

    /**
     * Private method to save chats with bots
     *
     * @param Array $id , $user_id
     * @return mixed
     */
    private function saveBotChats($data, $user_id)
    {
        $bot_username = $data['bot_username'];
        $chat_id = $data['chat_id'];

        $botconversation = $this->botconversation->where(['bot_username' => $bot_username, 'chat_id' => $chat_id])->first();

        // if botconversation does not exist, create it
        if (!$botconversation) {

            $botconversation = $this->botconversation->create([
                'user_id' => $user_id,
                'bot_name' => $data['bot_name'],
                'bot_username' => $bot_username,
                'chat_id' => $chat_id,
                'first_name' => $data['first_name'],
                'bot_type' => $data['bot_type'],
                'last_name' => $data['last_name']
            ]);
        }

        // save bot chat
        $this->botchat->create([
            'message_id' => $data['message_id'],
            'date' => $data['date'],
            'text' => $data['text'],
            'sender' => $data['sender'],
            'receiver' => $data['receiver'],
            'replied_to_message_id' => $data['replied_to_message_id'],
            'bot_conversation_id' => $botconversation->id
        ]);
    }

    /**
     * Private method to save Viber Updates
     *
     * @param Request $request , $botdetail, $type, $token
     * @return mixed
     */
    private function saveViberUpdates($request, $botdetail, $type, $token)
    {
        // call viber bot api
        if ($request->event == "message") {
            // save bot chat and conversation
            $data = $request->toArray();

            if (sizeof($data)) {
                $inputData = [
                    'bot_name' => $botdetail->name,
                    'bot_username' => $botdetail->username,
                    'chat_id' => $data['sender']['id'],
                    'first_name' => explode(' ', $data['sender']['name'])['0'],
                    'bot_type' => $type,
                    'last_name' => explode(' ', $data['sender']['name'])['1'],
                    'replied_to_message_id' => null,
                    'message_id' => $data['message_token'],
                    'sender' => $botdetail->name,
                    'receiver' => explode(' ', $data['sender']['name'])['0'],
                    'date' => $data['timestamp'],
                    'text' => $data['message']['text'],
                ];

                // save bot chat and conversation
                $this->saveBotChats($inputData, $botdetail->user_id);
            }

            return $data;
        } else {
            Log::info($request);
        }
    }

    /**
     * Private method to save whatsapp Updates
     *
     * @param Array $request , $type, $token
     * @return mixed
     */
    public function saveWhatsappUpdates(Request $request)
    {
        // call whatsapp bot api
        $newdata = $request->toArray();
        // save bot chat and conversation

        if (sizeof($newdata["entry"])) {
            $data = $newdata["entry"][0]["changes"][0]['value'];
            $name = explode(' ', $data['contacts'][0]['profile']['name']);
            // get firstname and lastname of the sender
            if (sizeof($name) > 1) {
                $firstname = $name[0];
                $lastname = $name[1];
            } else {
                $firstname = $name[0];
                $lastname = "";
            }

            // create input data
            $inputData = [
                'bot_name' => "",
                'bot_username' => "",
                'chat_id' => $data['contacts'][0]['wa_id'],
                'first_name' => $firstname,
                'bot_type' => "whatsapp",
                'last_name' => $lastname,
                'replied_to_message_id' => null,
                'message_id' => $data['messages'][0]['id'],
                'sender' => $data['contacts'][0]['wa_id'],
                'receiver' => "",
                'date' => $data['messages'][0]['timestamp'],
                'text' => $data['messages'][0]['text']['body'],
            ];

            // connect contact books ms to get user-id of the partner
            PubSub::publish(self::RECEIVER_LISTENER, $inputData, 'ContactsBooksMS');

            return;
        }
    }

    public function verifyWhatsappWebhook(Request $request)
    {
        if ($request->has(["hub_mode", "hub_challenge", "hub_verify_token"])) {
            if ($request->get("hub_mode") == "subscribe" && $request->get("hub_verify_token") == env("WHATSAPP_CLOUD_VERIFY_TOKEN")) {
                return $request->get("hub_challenge");
            } else {
                return null;
            }
        }
    }
}
