<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailerMail extends Mailable
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
     * @return MailerMail
     */
    public function build()
    {
        return $this
            ->subject($this->data['subject'])
            ->markdown('mails.mailer', [
                'body' => $this->data['body']
            ]);
    }
}
