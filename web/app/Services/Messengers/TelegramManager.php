<?php
namespace App\Services\Messengers;
use App\Contracts\MessengerContract;
use Telegram\Bot\Api;

class TelegramManager implements MessengerContract
{
	public function __construct(){
		
			$telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
	}
}