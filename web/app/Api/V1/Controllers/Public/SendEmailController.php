<?php

namespace App\Api\V1\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PubSub;

class SendEmailController extends Controller
{
    /**
     * Send message to one or group recipients
     *
     * @OA\Post(
     *     path="/mail/sender",
     *     summary="Send message to one or group recipients",
     *     description="Send message to one or group recipients",
     *     tags={"Mailer"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
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
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found"
     *     )
     * )
     *
     * @param Request $request
     *
     * @return mixed
     * @throws ValidationException
     */
    public function __invoke(Request $request)
    {
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
        foreach ($requestData['emails'] as $email) {
            $mailData = [
                'subject' => $requestData['subject'],
                'body' => $requestData['body'],
                'recipient_email' => $email
            ];

            // Save message for log
            $message = Message::create(array_merge([
                'sender_user_id' => Auth::user()->getAuthIdentifier(),
                'status' => Message::STATUS_PROCESSING
            ], $mailData));
            $mailData['message_id'] = $message->id;

            // Add job to queue
            try {
                PubSub::publish('sendEmail', $mailData, config('pubsub.queue.communications'));
            } catch (Exception $e) {
                $message->status = Message::STATUS_QUEUE_FAIL;
                $message->note = $e->getMessage();
                $message->save();

                // Return error response
                return response()->jsonApi($e, 200);
            }
        }

        // Return success response
        return response()->jsonApi([
            'title' => 'Success',
            'message' => 'Message was been successful added to queue'
        ]);
    }
}
