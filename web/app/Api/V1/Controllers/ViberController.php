<?php

namespace App\Api\V1\Controllers;
use Viber\Bot;
use Viber\Api\Sender;
class ViberController extends Controller
{
	 /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/linebot",
     *     summary="Load contributors list",
     *     description="Load contributors list",
     *     tags={"Telegram"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *     x={
     *         "auth-type": "Application & Application User",
     *         "throttling-tier": "Unlimited",
     *         "wso2-application-security": {
     *             "security-types": {"oauth2"},
     *             "optional": "false"
     *         }
     *     },
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit contributors of page",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Count contributors of page",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keywords",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[by]",
     *         in="query",
     *         description="Sort by field ()",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[order]",
     *         in="query",
     *         description="Sort order (asc, desc)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Success send data"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     )
     * )
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
		$apiKey = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');

		// this is how our bot will look like (name and avatar can be changed)
		$botSender = new Sender([
			'name' => 'Whois bot',
			'avatar' => 'https://developers.viber.com/img/favicon.ico',
		]);

		try {
			$bot = new Bot(['token' => $apiKey]);
			$bot
			->onConversation(function ($event) use ($bot, $botSender) {
				// this event will be called as soon as the user enters the chat
				// you can send "hello", but you can't send more messages
				return (new \Viber\Api\Message\Text())
					->setSender($botSender)
					->setText("Can i help you?");
			})
			->onText('|whois .*|si', function ($event) use ($bot, $botSender) {
				// this event will be fired if the user sends a message
				// which matches the regular expression
				$bot->getClient()->sendMessage(
					(new \Viber\Api\Message\Text())
					->setSender($botSender)
					->setReceiver($event->getSender()->getId())
					->setText("I do not know )")
				);
			})
			->run();
		} catch (Exception $e) {
			// todo - log exceptions
		}
	}
	
}
