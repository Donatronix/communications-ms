<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Chat;
use Exception;

/**
 * Class ChatController
 *
 * @package App\Api\V1\Controllers
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
     *     path="/chats",
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
    public function index(Request $request)
    {
        try {
            // Get chats list
            $chats = $this->model
            ->where('first_user_id', $this->user_id)
            ->orWhere('second_user_id', $this->user_id)
            ->orderBy($request->get('sort-by', 'created_at'), $request->get('sort-order', 'desc'))
            ->paginate($request->get('limit', 20));

            // Return response
            return response()->jsonApi([
                'type' => 'success',
                'title' => "chats list",
                'message' => 'List of chats successfully received',
                'data' => $chats->toArray()
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
}