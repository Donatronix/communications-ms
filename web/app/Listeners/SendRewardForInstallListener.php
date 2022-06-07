<?php

namespace App\Listeners;

use App\Mails\SendRewardForInstallMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRewardForInstallListener
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
     * @param $data
     * @return void
     */
    public function handle($data)
    {
        // Resolve recipient email from user id
        $recipientEmail = 'test@test.com';

        $data['display_name'] = 'Jhon Smith';

        Mail::to($recipientEmail)->send(new SendRewardForInstallMail($data));

        Log::info($data);
    }
}
