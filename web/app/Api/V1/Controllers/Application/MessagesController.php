<?php

namespace App\Api\V1\Controllers\Application;

use App\Api\V1\Controllers\Controller;
use App\Services\Messenger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionException;

class MessagesController extends Controller
{
    /**
     * Send message.
     *
     * @OA\Post(
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
     * @OA\Post(
     *     path="/channels/{messengerInstance}/webhook",
     *     summary="Handle webhook from messenger",
     *     description="Handle webhook from messenger",
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
