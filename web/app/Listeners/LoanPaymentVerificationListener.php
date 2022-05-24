<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\Services\Messenger;

class LoanPaymentVerificationListener
{

    private const RECEIVER_LISTENER = 'LoanPaymentVerification';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LoanPaymentVerificationEvent  $event
     * @return void
     */
    public function handle(array $inputData)
    {
        try {

            $data = (object) $inputData;

            $messenger = Messenger::getInstance(strtolower($data->instance));

            $response = $messenger->sendMessage($data->message, $data->to ?? null);

            $message_id = $response->getMessageId();

            \PubSub::transaction(function () {
            })->publish(self::RECEIVER_LISTENER, [
                'type' => 'success',
                'title' => "Message sent",
                'message_id' => $message_id,
                'verification_id' => $data->verification_id
            ], $data->replay_to);
            exit;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            exit;
        }
    }
}
