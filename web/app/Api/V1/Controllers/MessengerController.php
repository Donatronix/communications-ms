<?php

namespace App\Api\V1\Controllers;

use App\Services\Messenger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionException;

class MessengerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/telegram",
     *     summary="Load contributors list",
     *     description="Load contributors list",
     *     tags={"Telegram"},
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
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit contributors of page",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Count contributors of page",
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
     *         name="sort[by]",
     *         in="query",
     *         description="Sort by field ()",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[order]",
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
     *         response="404",
     *         description="Not found"
     *     )
     * )
     */
    public function index($messengerInstance)
    {

        $telegram = Messenger::getInstance(strtolower($messengerInstance));
        //dd($telegram);
        $response = $telegram->sendMessage();

        $messageId = $response->getMessageId();
        return response()->json([
            'data' => $messageId,
        ], 200);
    }


    /**
     * @param Request $request
     * @param         $messengerInstance
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function sendMessage(Request $request, $messengerInstance): JsonResponse
    {
        $telegram = Messenger::getInstance(strtolower($messengerInstance));
        //dd($telegram);
        $response = $telegram->sendMessage($request);

        $messageId = $response->getMessageId();
        return response()->json([
            'data' => $messageId,
        ], 200);
    }

    /**
     * @param Request $request
     * @param         $messengerInstance
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function handleWebhook(Request $request, $messengerInstance): JsonResponse
    {
        $telegram = Messenger::getInstance(strtolower($messengerInstance));
        //dd($telegram);
        $response = $telegram->handlerWebhookInvoice($request);

        $messageId = $response->getMessageId();
        return response()->json([
            'data' => $messageId,
        ], 200);
    }
}
