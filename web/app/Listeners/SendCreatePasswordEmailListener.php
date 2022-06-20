<?php

namespace App\Listeners;

use App\Mails\SendCreatePasswordEmailMail;
use Illuminate\Support\Facades\Mail;

class SendCreatePasswordEmailListener
{
    public function __construct($data = null)
    {
        //
    }

    public function handle($data)
    {
        Mail::to($data['email'])->send(new SendCreatePasswordEmailMail($data));
    }
}
