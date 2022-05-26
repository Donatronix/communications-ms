<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\Services\Messenger;

class SendSMSListener
{

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
     * @param  SendSMSEvent  $event
     * @return void
     */
    public function handle(array $inputData)
    {
        try {

            $data = (object) $inputData;

            $messenger = Messenger::getInstance(strtolower($data->instance));

            $response = $messenger->sendMessage($data->message, $data->to ?? null);

            Log::info($response);
            exit;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            exit;
        }
    }
}
