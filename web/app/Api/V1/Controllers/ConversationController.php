<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Chat;
use Illuminate\Support\Facades\Validator;
use Exception;

/**
 * Class ConversationController
 *
 * @package App\Api\V1\Controllers
 */
class ConversationController extends Controller
{
    /**
     * @param Conversation $model
     */
    private Conversation $model;
    private Chat $chat;

    /**
     * ConversationController constructor.
     *
     * @param Conversation $model
     */
    public function __construct(Conversation $model, Chat $chat)
    {
        $this->model = $model;
        $this->chat = $chat;
        $this->user_id = auth()->user()->getAuthIdentifier();
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/conversations",
     *     summary="Load conversations list",
     *     description="Load conversations list",
     *     tags={"Conversations"},
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
    public function index(Request $request)
    {
        try {
            // Get conversations list
            $conversations = $this->model
            ->where('first_user_id', $this->user_id)
            ->orWhere('second_user_id', $this->user_id)
            ->orderBy($request->get('sort-by', 'created_at'), $request->get('sort-order', 'desc'))
            ->paginate($request->get('limit', 20));

            // Return response
            return response()->jsonApi([
                'type' => 'success',
                'title' => "conversations list",
                'message' => 'List of conversations successfully received',
                'data' => $conversations->toArray()
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
     * Save a new conversation data
     *
     * @OA\Post(
     *     path="/start-conversation",
     *     summary="Save a new conversation data",
     *     description="Save a new conversation data",
     *     tags={"Conversations"},
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
     *              @OA\Property(
     *                  property="second_user_id",
     *                  type="string",
     *                  default="96541e14-45be-4df8-ba8c-c742d1ac1c2c"
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully save"
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Conversation created"
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
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'second_user_id' => 'required|string',
            'message' => 'required|string',
        ]);
        if ($validator->fails()){
            throw new Exception($validator->errors()->first());
        }

        // Try to add new conversation
        try {

            // transform the request object to include first user id
                $request->merge([
                    'first_user_id' => $this->user_id
                ]);

            // Create new
            $conversation = $this->model->create([$request->all()]);

            // create chat 
            $chat = $this->chat->create([
                'user_id' => $this->user_id,
                'conversation_id' => $conversation->id,
                'message' => $request->get('message')
            ]);

            // Return response to client
            return response()->jsonApi([
                'type' => 'success',
                'title' => 'New conversation registration',
                'message' => "Conversation successfully added",
                'data' => $conversation->toArray()
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'New conversation registration',
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }
}