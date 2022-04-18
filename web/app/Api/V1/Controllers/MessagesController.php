<?php

namespace App\Api\V1\Controllers;

use App\Services\Messenger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionException;

class MessagesController extends Controller
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

        $messenger = Messenger::getInstance(strtolower($messengerInstance));
        $response = $messenger->sendMessage();

        $messageId = $response->getMessageId();
        return response()->json([
            'data' => $messageId,
        ], 200);
    }


    /**
     * Send message.
     *
     * @OA\Get(
     *     path="/messages/{messengerInstance}/send-message",
     *     summary="Send message using messenger",
     *     description="Send message using messenger",
     *     tags={"Messenger"},
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
     *         name="from",
     *         in="query",
     *         description="Sender's Id or number",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Receiver's ID or number",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="message",
     *         in="query",
     *         description="Message to be sent",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="messengerInstance",
     *         in="path",
     *         description="Messenger instance to use for sending message",
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
     *     ),
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

        $response = $messenger->sendMessage($request->message, $request->to ?? null);

        $messageId = $response->getMessageId();
        return response()->json([
            'data' => $messageId,
        ], 200);
    }

    /**
     * Handle webhook.
     *
     * @OA\Get(
     *     path="/messages/{messengerInstance}/webhook",
     *     summary="Handle webhook from messenger",
     *     description="Handle webhook from messenger",
     *     tags={"Messenger"},
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
     *         name="from",
     *         in="query",
     *         description="Sender's Id or number",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Receiver's ID or number",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="message",
     *         in="query",
     *         description="Message to be sent",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="messengerInstance",
     *         in="path",
     *         description="Messenger instance to use for sending message",
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
     *     ),
     * )
     *
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
