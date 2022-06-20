<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerificationEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        // TODO
        $url = env('FRONT_HOST') . '/email-verification?email=' . $this->data['email'] . '&verify_token=' . $this->data['verify_token'];

        return $this
            ->subject('Welcome to ' . env('APP_NAME') . '! Please confirm your email address')
            ->markdown('mails.verification-email', [
                'url' => $url,
                'display_name' => $this->data['display_name'],
            ]);
    }
}
