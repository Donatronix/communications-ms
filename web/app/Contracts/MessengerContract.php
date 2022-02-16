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
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handlerWebhookInvoice(Request $request): mixed;

	
}
