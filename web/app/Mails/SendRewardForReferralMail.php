<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRewardForReferralMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this
            ->subject($this->data['subject'])
            ->markdown('mails.reward-for-referral', [
                'display_name' => $this->data['display_name'],
                'points' => $this->data['points']
            ]);
    }
}
