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
     *     path="/messengers",
     *     summary="Load contributors list",
     *     description="Load contributors list",
     *     tags={"Messages"},
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
        $messenger = Messenger::getInstance(strtolower($messengerInstance));
        $response = $messenger->sendMessage();

        $messageId = $response->getMessageId();

        return response()->json([
            'data' => $messageId,
        ], 200);
    }

    /**
     * Create a new bot
     *
     * @OA\Post(
     *     path="/messengers/{}/send-message",
     *     summary="Create a new bot",
     *     description="NOTICE. Bot type can be only: 'telegram', 'viber', 'line', 'discord', 'signal', 'whatsapp', 'twilio', 'nexmo'. Bot platform can be only: 'sumra', 'ultainfinity'.",
     *     tags={"Admin / Bots"},
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BotSchema")
     *     ),
     *
     *     @OA\Response(
     *         response="201",
     *         description="New bot was successfully created"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request syntax or unsupported method"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server got itself in trouble"
     *     )
     * )
     *
     * @param Request $request
     * @param         $messengerInstance
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function sendMessage(Request $request, $messengerInstance): JsonResponse
    {
        $messenger = Messenger::getInstance(strtolower($messengerInstance));

        $response = $messenger->sendMessage($request->message, $request->receiver ?? null);

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
        $messenger = Messenger::getInstance(strtolower($messengerInstance));
        $response = $messenger->handlerWebhookInvoice($request);

        $messageId = $response->getMessageId();

        return response()->json([
            'data' => $messageId,
        ], 200);
    }
}
