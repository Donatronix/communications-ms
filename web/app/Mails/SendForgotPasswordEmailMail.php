<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendForgotPasswordEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $url = env('FRONT_HOST') . '/password/new/?token=' . $this->data['token'] . '&email=' . $this->data['email'];

        $reset_link = env('FRONT_HOST') . '/password/reset';

        return $this
            ->subject('Welcome to ' . env('APP_NAME') . '! Reset password')
            ->markdown('mails.forgot-password', [
                'url' => $url,
                'reset_link' => $reset_link,
                'display_name' => $this->data['display_name'],
            ]);
    }
}
