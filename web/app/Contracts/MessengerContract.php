<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface MessengerContract
{
    /**
     * @return mixed
     */
    public static function gateway(): mixed;

    /**
     * @return mixed
     */
    public static function name(): mixed;

    /**
     * @return mixed
     */
    public static function description(): mixed;

    /**
     * @return integer
     */
    public static function getNewStatusId(): int;

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function handlerWebhookInvoice(Request $request): mixed;

    /**
     * @param string      $message
     * @param string|null $recipient
     *
     * @return mixed
     */
    public function sendMessage(string $message, string $recipient = null): mixed;
}
