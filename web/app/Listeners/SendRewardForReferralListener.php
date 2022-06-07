<?php

namespace App\Listeners;

use App\Mails\SendRewardForReferralMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRewardForReferralListener
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
        // @todo Need get user email
        // Resolve recipient email from user id
        $recipientEmail = 'test@test.com';

        // @todo Need get user first name and last name
        $data['display_name'] = 'Jhon Smith';

        Mail::to($recipientEmail)->send(new SendRewardForReferralMail($data));

        Log::info($data);
    }
}
