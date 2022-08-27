<?php

namespace App\Listeners;

use App\Mails\WelcomeMail;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendEmailMessageRequestListener
{
    /**
     * Handle the event.
     *
     * @param array $inputData
     */
    public function handle(array $inputData)
    {
        // Logging income data
        if(env('APP_DEBUG')){
            Log::info('SendEmailMessageRequestListener');
            Log::info($inputData);
        }

        // Do validate input data
        $validation = Validator::make($inputData, [
            'subject' => 'required|string',
            'body' => 'required|string',
            'recipient' => 'required|string',
            'template' => 'required|string',
            'message_id' => 'sometimes|string',
            'sender_id' => 'sometimes|string|min:36|max:36',
        ]);

        // If validation error, the stop
        if ($validation->fails()) {
            Log::error('Validation error: ' . $validation->errors());
            exit();
        }

        // Get message or create new
        if(!isset($inputData['message_id'])){
            $message = Message::create(array_merge([
                'sender_id' => $inputData['sender_id'],
                'status' => Message::STATUS_PROCESSING
            ], $inputData));
        }else{
            $message = Message::find($inputData['message_id']);
        }

        // Do send mail
        try {
            Mail::to($inputData['recipient'])->send(new WelcomeMail($inputData));

            // Update status
            $message->status = Message::STATUS_SENT;
            $message->save();
        }catch (\Exception $e){
            // Update status and reason
            $message->status = Message::STATUS_FAILURE;
            $message->note = $e->getMessage();
            $message->save();
        }
    }
}
