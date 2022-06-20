<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlarmWarehouseEmail extends Mailable
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
            ->subject('Alarm warehouse to ' . env('APP_NAME') . '!')
            ->markdown('mails.alarm-verification-email', [
                'display_name' => $this->data['display_name'],

                'status' => $this->data['status'],
                'operationId' => $this->data['operationId'],

                'warehouse_id' => $this->data['warehouse']['id'],
                'warehouse_name' => $this->data['warehouse']['name'],
                'warehouse_owner' => $this->data['warehouse']['owner'],
                'warehouse_owner_id' => $this->data['warehouse']['ownerID'],
                'warehouse_balance' => $this->data['warehouse']['balance'],

                'productId' => $this->data['productId'],
                'optionProductId' => $this->data['optionProductId'],
                'userId' => $this->data['userId'],
            ]);
    }
}
