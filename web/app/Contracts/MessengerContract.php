<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface MessengerContract
{
    /**
     * @return mixed
     */
    public static function gateway();

    /**
     * @return mixed
     */
    public static function name();

    /**
     * @return mixed
     */
    public static function description();

    /**
     * @return integer
     */
    public static function getNewStatusId();

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function handlerWebhookInvoice(Request $request): mixed;

    /**
     * @param string|array $message
     * @param string|null  $recipient
     *
     * @return mixed
     */
    public function sendMessage(string|array $message, string $recipient = null): mixed;

}
