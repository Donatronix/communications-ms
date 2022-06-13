<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Conversation;
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

    /**
     * ConversationController constructor.
     *
     * @param Conversation $model
     */
    public function __construct(Conversation $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/possible-loan-amounts",
     *     summary="Load possible loan amounts list",
     *     description="Load possible loan amounts list",
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
     *         description="Limit possible loan amounts",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Count possible loan amounts",
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
            // Get possible loan amounts list
            $possible_loan_amounts = $this->model
            ->orderBy($request->get('sort-by', 'created_at'), $request->get('sort-order', 'desc'))
            ->paginate($request->get('limit', 20));

            // Return response
            return response()->jsonApi([
                'type' => 'success',
                'title' => "Possible Loan Amounts list",
                'message' => 'List of possible loan amounts successfully received',
                'data' => $possible_loan_amounts->toArray()
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "Possible Loan Amounts list",
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }
}