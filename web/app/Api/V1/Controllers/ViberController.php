<?php

namespace App\Api\V1\Controllers;
use Viber\Bot;
use Viber\Api\Sender;
use Viber\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class ViberController extends Controller
{
	public $apikey;
	public function __construct()
	{
		//dd('hi');
		$this->apiKey = env('VIBER_BOT_CHANNEL_ACCESS_TOKEN'); // from PA "Edit Details" page
		//dd($this->apiKey);
		$webhookUrl = 'https://927b-117-96-42-93.ngrok.io/communications-ms/web/public/v1/viber/setup.php'; // for exmaple https://my.com/bot.php
		//dd($this->apiKey);
		try {
			$client = new Client(['token' => $this->apiKey]);
			$result = $client->setWebhook($webhookUrl);
			echo "Success!\n"; // print_r($result);
		} catch (Exception $e) {
			echo 'Error: ' . $e->getMessage() . "\n";
		}
		dd($this->apiKey);
	}
	 /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/viberbot",
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
		$this->apiKey = env('VIBER_BOT_CHANNEL_ACCESS_TOKEN');
		// reply name
		$botSender = new Sender([
			'name' => 'Whois bot',
			'avatar' => 'https://developers.viber.com/img/favicon.ico',
		]);

		// log bot interaction
		$log = new Logger('bot');
		$log->pushHandler(new StreamHandler('/tmp/bot.log'));

		try {
			// create bot instance
			$bot = new Bot(['token' => $this->apiKey]);
			$bot
				->onConversation(function ($event) use ($bot, $botSender, $log) {
					$log->info('onConversation ' . var_export($event, true));
					// this event fires if user open chat, you can return "welcome message"
					// to user, but you can't send more messages!
					return (new \Viber\Api\Message\Text())
						->setSender($botSender)
						->setText('Can i help you?');
				})
				->onText('|whois .*|si', function ($event) use ($bot, $botSender, $log) {
					$log->info('onText whois ' . var_export($event, true));
					// match by template, for example "whois Bogdaan"
					$bot->getClient()->sendMessage(
						(new \Viber\Api\Message\Text())
							->setSender($botSender)
							->setReceiver($event->getSender()->getId())
							->setText('I do not know )')
					);
				})
				->onText('|.*|s', function ($event) use ($bot, $botSender, $log) {
					$log->info('onText ' . var_export($event, true));
					// .* - match any symbols
					$bot->getClient()->sendMessage(
						(new \Viber\Api\Message\Text())
							->setSender($botSender)
							->setReceiver($event->getSender()->getId())
							->setText('HI!')
					);
				})
				->onPicture(function ($event) use ($bot, $botSender, $log) {
					$log->info('onPicture ' . var_export($event, true));
					$bot->getClient()->sendMessage(
						(new \Viber\Api\Message\Text())
							->setSender($botSender)
							->setReceiver($event->getSender()->getId())
							->setText('Nice picture ;-)')
					);
				})
				->on(function ($event) {
					return true; // match all
				}, function ($event) use ($log) {
					$log->info('Other event: ' . var_export($event, true));
				})
				->run();
		} catch (Exception $e) {
			$log->warning('Exception: ', $e->getMessage());
			if ($bot) {
				$log->warning('Actual sign: ' . $bot->getSignHeaderValue());
				$log->warning('Actual body: ' . $bot->getInputBody());
			}
		}
	}
}
