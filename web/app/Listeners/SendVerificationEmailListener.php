<?php

namespace App\Listeners;

use App\Mails\SendVerificationEmailMail;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmailListener
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
        Mail::to($data['email'])->send(new SendVerificationEmailMail($data));
    }
}
