<?php

namespace App\Api\V1\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mails\WelcomeMail;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Sumra\SDK\Facades\PubSub;

class SendEmailController extends Controller
{
    /**
     * Send message to one or group recipients
     *
     * @OA\Post(
     *     path="/mail/sender",
     *     summary="Send message to one or group recipients",
     *     description="Send message to one or group recipients",
     *     tags={"Mail Sender"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="subject",
     *                 type="string",
     *                 description="Message body",
     *                 example=""
     *             ),
     *             @OA\Property(
     *                 property="body",
     *                 type="string",
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
     *             ),
     *             @OA\Property(
     *                 property="template",
     *                 type="string",
     *                 description="Template of message",
     *                 example="welcome"
     *             ),
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
    public function __invoke(Request $request): mixed
    {
        // Validate data
        $validation = Validator::make($request->all(), [
            'subject' => 'string|nullable',
            'body' => 'string|required',
            'emails' => 'array|required',
            'template' => 'required|string',
        ]);

        // If validation error, the stop
        if ($validation->fails()) {
            Log::error('Validation error: ' . $validation->errors());
            exit();
        }

        // Get validated data
        $requestData = (object)$validation->validated();

        // Send mail for each recipients
        foreach ($requestData->emails as $email) {
            $mailData = [
                'subject' => $requestData->subject,
                'body' => $requestData->body,
                'recipient' => $email
            ];

            // Save message for log
            $message = Message::create(array_merge([
                'sender_id' => Auth::user()->getAuthIdentifier(),
                'status' => Message::STATUS_PROCESSING
            ], $mailData));

            $mailData['message_id'] = $message->id;
            $mailData['template'] = 'welcome';

            // Add message to queue
            PubSub::publish('SendEmailMessageRequest', $mailData, config('pubsub.queue.communications'));
        }

        // Return success response
        return response()->jsonApi([
            'title' => 'Success',
            'message' => 'Message was been successful added to queue'
        ]);
    }
}
