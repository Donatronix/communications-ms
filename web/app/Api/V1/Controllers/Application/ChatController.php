<?php

namespace App\Api\V1\Controllers\Application;

use App\Api\V1\Controllers\Controller;
use App\Events\MessageSent;
use App\Models\Chat;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Sumra\SDK\JsonApiResponse;

/**
 * Class ChatController
 *
 * @package App\Api\V1\Controllers\Application
 */
class ChatController extends Controller
{
    /**
     * @param Chat $model
     */
    private Chat $model;

    /**
     * ChatController constructor.
     *
     * @param Chat $model
     */
    public function __construct(Chat $model)
    {
        $this->model = $model;
        $this->user_id = auth()->user()->getAuthIdentifier();
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/chats/{conversation_id}",
     *     summary="Load chats list",
     *     description="Load chats list",
     *     tags={"Chats"},
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
     *         name="conversation_id",
     *         in="path",
     *         description="conversation Id",
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
    public function index(Request $request, $conversation_id)
    {
        try {
            // Get chats list
            $chats = $this->model
                ->where('conversation_id', $conversation_id)
                ->orderBy($request->get('sort-by', 'created_at'), $request->get('sort-order', 'desc'))
                ->paginate($request->get('limit', 20));

            // Return response
            return response()->jsonApi([
                'title' => "chats list",
                'message' => 'List of chats successfully received',
                'data' => $chats->toArray()
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => "chats list",
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * Save a new chat data
     *
     * @OA\Post(
     *     path="/chats/{conversation_id}",
     *     summary="Reply to a chat",
     *     description="Reply to a chat",
     *     tags={"Chats"},
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
     *         name="conversation_id",
     *         in="path",
     *         description="conversation Id",
     *         example="0aa06e6b-35de-3235-b925-b0c43f8f7c75",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="receiver_id",
     *                  type="string",
     *                  default="96541e14-45be-4df8-ba8c-c742d1ac1c2c"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  default="Hello"
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully save"
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Chat created"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
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
     *         response="422",
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(Request $request, $conversation_id)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        // Try to add new chat
        try {

            // create new chat
            $chat = $this->model->create([
                'user_id' => $this->user_id,
                'conversation_id' => $conversation_id,
                'receiver_id' => $request->get('receiver_id'),
                'message' => $request->get('message')
            ]);

            broadcast(new MessageSent($chat))->toOthers();

            // Return response to client
            return response()->jsonApi([
                'title' => 'New conversation registration',
                'message' => "Chat successfully added",
                'data' => $chat->toArray()
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'New chat registration',
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * Update a chat
     *
     * @OA\Put(
     *     path="/chats/{id}",
     *     summary="Update a chat",
     *     description="Note: status can only be seen, delivered or deleted",
     *     tags={"Chats"},
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
     *         name="chat_id",
     *         in="path",
     *         description="chat Id",
     *         example="0aa06e6b-35de-3235-b925-b0c43f8f7c75",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  default="delivered",
     *                  description="Could be delivered/seen/deleted"
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully save"
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Chat created"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
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
     *         response="422",
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:delivered,seen,deleted',
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        // Try to update chat
        try {

            // find chat with id
            $chat = $this->getObject($id);

            if ($chat instanceof JsonApiResponse) {
                return $chat;
            }

            $status = $request->get('status');
            if ($status == "delivered") {
                $chat->update([
                    'is_delivered' => 1,
                ]);
            } else if ($status == "seen") {
                $chat->update([
                    'is_seen' => 1,
                ]);
            } else if ($status == "deleted") {
                if ($chat->user_id == $this->user_id) {
                    $chat->update([
                        'deleted_from_sender' => 1
                    ]);
                } else {
                    $chat->update([
                        'deleted_from_receiver' => 1
                    ]);
                }
            }

            // Return response to client
            return response()->jsonApi([
                'title' => 'Chat updation',
                'message' => "Chat successfully updated",
                'data' => $chat->toArray()
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'Chat updation',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get chat object
     *
     * @param $id
     * @return mixed
     */
    private function getObject($id): mixed
    {
        try {
            return $this->model::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->jsonApi([
                'title' => "Get chat",
                'message' => "Chat with id #{$id} not found: {$e->getMessage()}"
            ], 404);
        }
    }
}
