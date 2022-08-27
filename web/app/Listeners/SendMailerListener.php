<?php

namespace App\Listeners;

use App\Mails\WelcomeMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailerListener
{
    /**
     * Handle the event.
     *
     * @param $data
     * @return void
     */
    public function handle($data = null)
    {
        Log::info($data);

        Mail::to($data['recipient'])->send(new WelcomeMail($data));

        // check for failed ones
        if (Mail::failures()) {
            // return failed mails
            return new \Error(Mail::failures());
        }
    }
}
