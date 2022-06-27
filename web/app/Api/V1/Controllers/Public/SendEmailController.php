<?php

namespace App\Api\V1\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use PubSub;

class SendEmailController extends Controller
{
    /**
     * Send message to one or group recipients
     *
     * @OA\Post(
     *     path="/mail",
     *     summary="Send message to one or group recipients",
     *     description="Send message to one or group recipients",
     *     tags={"Mailer"},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="subject",
     *                 type="text",
     *                 description="Message body",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="body",
     *                 type="text",
     *                 description="Message body",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="emails",
     *                 description="Clients emails",
     *                 type="array",
     *                 @OA\Items(
     *                     type="string",
     *                     example="client1@client.com"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success sended message"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     )
     * )
     *
     * @param Request $request
     */
    public function __invoke(Request $request)
    {
        // Check sender user id
        $userId = $request->header('user-id');

        if ($userId === null) {
            abort(401, 'Unauthorized');
        }

        // Validate data
        $requestData = $this->validate(
            $request,
            [
                'subject' => 'string|nullable',
                'body' => 'string|required',
                'emails' => 'array|required'
            ]
        );

        // Send mail for each recipients
        foreach($requestData['emails'] as $email){
            $mailData = [
                'subject' => $requestData['subject'],
                'body' => $requestData['body'],
                'recipient_email' => $email
            ];

            // Save message for log
            $message = Message::create(array_merge([
                'sender_user_id' => $userId,
                'status' => Message::STATUS_PROCESSING
            ], $mailData));
            $mailData['message_id'] = $message->id;

            // Add job to queue
            try {
                PubSub::publish('mailer', $mailData, 'CommunicationsMS');
            } catch (Exception $e) {
                $message->status = Message::STATUS_QUEUE_FAIL;
                $message->note = $e->getMessage();
                $message->save();

                // Return response
                //return response()->jsonApi($e, 200);
            }
        }

        // Return response
        return response()->jsonApi('Success sent', 200);
    }
}
