<?php

namespace App\Api\V1\Controllers;

use App\Models\BotConversation;
use App\Models\BotChat;
use Sumra\SDK\JsonApiResponse;
use Illuminate\Http\Request;
use App\Models\BotDetail;
use App\Models\Channel;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

/**
 * Class BotMessageController
 *
 * @package App\Api\V1\Controllers 
 */
class BotMessageController extends Controller
{

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
     * Send Message to external user.
     *
     * @OA\Post(
     *     path="/send",
     *     summary="Send Message to external user",
     *     description="Send Message to external user",
     *     tags={"Bot Messages"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *     x={
     *         "auth-type": "Application & Application User",
     *         "throttling-tier": "Unlimited",
     *         "wso2-application-security": {
     *             "security-types": {"oauth2"},
     *             "optional": "false"
     *         }
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *     @OA\Property(
     *         property="text",
     *         type="string",
     *         description="message to be sent",
     *         example="Hey, how are you doing?"
     *     ),
     *     @OA\Property(
     *         property="chat_id",
     *         type="string",
     *         description="Chat Id of the user to send a message",
     *         example="2063523844"
     *     ),
     *          )
     *     ),
     *
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
    public function sendMessage(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'type' => ["required", "string", Rule::in(Channel::$types)],
            'text' => 'required|string',
            'chat_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        // Try to add new botdetail
        try {
            // check if same bot detail has already been created
            $botdetail = $this->botdetail->where(['user_id' => $this->user_id, 'type' => $request->get('type')])->first();

            if (!$botdetail) {
                return response()->jsonApi([
                    'type' => 'danger',
                    'title' => 'Send Message',
                    'message' => "User has not created a bot for {$request->get('type')}",
                    'data' => null
                ], 400);
            }

            if ($request->get('type') == "telegram") {
                $data = $this->sendTelegramMessage($request, $botdetail);
            }

            if ($request->get('type') == "viber") {
                // call viber bot api 
                $client = new \GuzzleHttp\Client();
                $response = $client->request('POST', "https://chatapi.viber.com/pa/send_message", [
                    'headers' => [
                        'X-Viber-Auth-Token' => $botdetail->token
                    ],
                    'json' => [
                        'receiver' => $request->get('chat_id'),
                        'type' => 'text',
                        'text' => $request->get('text'),
                        'sender' => [
                            "name" => $botdetail->name
                        ]
                    ]
                ]);

                $data = json_decode($response->getBody(), true);
                if (sizeof($data)) {
                    if ($data['status'] == 0) {
                        $inputData = [
                            'bot_name' => $botdetail->name,
                            'bot_username' => $botdetail->username,
                            'chat_id' => $data['sender']['id'],
                            'first_name' => explode(' ', $data['sender']['name'])['0'],
                            'bot_type' => $request->get('type'),
                            'last_name' => explode(' ', $data['sender']['name'])['1'],
                            'replied_to_message_id' => null,
                            'message_id' => $data['message_token'],
                            'date' => $data['timestamp'],
                            'text' => $data['message']['text'],
                        ];

                        // save bot chat and conversation
                        $this->saveBotChats($inputData, $this->user_id);
                    } else {
                        return $data;
                    }
                }
            }

            // Return response
            return response()->jsonApi([
                'type' => 'success',
                'title' => "send message",
                'message' => 'Your message has been sent',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "send message",
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    /**
     * Save updates from bot webhook
     *
     * @param Request $request, $type, $token
     * @return mixed
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
            }

            \Log::info("Update has been saved");

            // Return response
            return response()->jsonApi([
                'type' => 'success',
                'title' => "send message",
                'message' => 'Your message has been sent',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "send message",
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    /**
     * Private method to save chats with bots
     *
     * @param Array $id, $user_id
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
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/bot-messages/conversations",
     *     summary="Load bot conversations list",
     *     description="Load bot conversations list",
     *     tags={"Bot Messages"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *     x={
     *         "auth-type": "Application & Application User",
     *         "throttling-tier": "Unlimited",
     *         "wso2-application-security": {
     *             "security-types": {"oauth2"},
     *             "optional": "false"
     *         }
     *     },
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Messenger Type",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         example="telegram"
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit conversations",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Count conversations",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keywords",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort-by",
     *         in="query",
     *         description="Sort by field ()",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort-order",
     *         in="query",
     *         description="Sort order (asc, desc)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
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
    public function getBotConversations(Request $request)
    {
        try {
            // Get conversations list
            $botconversations = $this->botconversation
                ->where('bot_type', $request->get('type', null))
                ->orderBy($request->get('sort-by', 'created_at'), $request->get('sort-order', 'desc'))
                ->paginate($request->get('limit', 20));

            // Return response
            return response()->jsonApi([
                'type' => 'success',
                'title' => "conversations list",
                'message' => 'List of botconversations successfully received',
                'data' => $botconversations->toArray()
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "conversations list",
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/bot-messages/chats/{bot_conversation_id}",
     *     summary="Load bot chats list",
     *     description="Load bot chats list",
     *     tags={"Bot Messages"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *     x={
     *         "auth-type": "Application & Application User",
     *         "throttling-tier": "Unlimited",
     *         "wso2-application-security": {
     *             "security-types": {"oauth2"},
     *             "optional": "false"
     *         }
     *     },
     *     @OA\Parameter(
     *         name="bot_conversation_id",
     *         in="path",
     *         description="bot_conversation Id",
     *         example="0aa06e6b-35de-3235-b925-b0c43f8f7c75",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit chats",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Count chats",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keywords",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort-by",
     *         in="query",
     *         description="Sort by field ()",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort-order",
     *         in="query",
     *         description="Sort order (asc, desc)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
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
    public function getBotChats(Request $request, $bot_conversation_id)
    {
        try {
            // Get chats list
            $botchats = $this->botchat
                ->where('bot_conversation_id', $bot_conversation_id)
                ->orderBy($request->get('sort-by', 'created_at'), $request->get('sort-order', 'desc'))
                ->paginate($request->get('limit', 20));

            // Return response
            return response()->jsonApi([
                'type' => 'success',
                'title' => "chats list",
                'message' => 'List of chats successfully received',
                'data' => $botchats->toArray()
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "chats list",
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }

    /**
     * Private method to send Telegram Message
     *
     * @param Array $request, $botdetail
     * @return mixed
     */
    private function sendTelegramMessage($request, $botdetail)
    {
        // call telegram bot api 
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', "https://api.telegram.org/bot{$botdetail->token}/sendMessage", [
            'json' => [
                'chat_id' => $request->get('chat_id'),
                'text' => $request->get('text'),
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        if ($result['ok'] == true) {
            $data = $result['result'];

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
                'bot_type' => $request->get('type'),
                'last_name' => $data['chat']['last_name'],
                'replied_to_message_id' => $replied_to_message_id,
                'message_id' => $data['message_id'],
                'sender' => $botdetail->name,
                'receiver' => $data['chat']['first_name'],
                'date' => $data['date'],
                'text' => $data['text'],
            ];

            // save bot chat and conversation
            $this->saveBotChats($inputData, $this->user_id);

            return $data;
        }
    }

    /**
     * Private method to save Telegram Updates
     *
     * @param Array $request, $botdetail, $type, $token
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
}
