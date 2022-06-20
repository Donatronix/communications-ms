<?php

namespace App\Listeners;

use App\Mails\MailerMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailerListener
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
    public function handle($data = null)
    {
        Log::info($data);

        Mail::to($data['recipient_email'])->send(new MailerMail($data));

        // check for failed ones
        if (Mail::failures()) {
            // return failed mails
            return new \Error(Mail::failures());
        }
    }
}
