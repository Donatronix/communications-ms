<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    protected $data;

    /**
     * MailerMail constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return WelcomeMail
     */
    public function build(): WelcomeMail
    {
        return $this
            ->subject($this->data['subject'])
            ->markdown('mails.welcome', [
                'body' => $this->data['body']
            ]);
    }
}
