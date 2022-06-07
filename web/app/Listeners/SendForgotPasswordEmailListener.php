<?php

namespace App\Listeners;

use App\Mails\SendForgotPasswordEmailMail;
use Illuminate\Support\Facades\Mail;

class SendForgotPasswordEmailListener
{
    public function __construct($data = null)
    {
        //
    }

    public function handle($data)
    {
        Mail::to($data['email'])->send(new SendForgotPasswordEmailMail($data));
    }
}
