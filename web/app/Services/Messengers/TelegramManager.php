<?php
namespace App\Services\Messengers;
use App\Contracts\MessengerContract;
use Telegram\Bot\Api;
use Illuminate\Http\Request;
class TelegramManager implements MessengerContract
{
	public $object;
	/*public function __construct(){
		
			$object = new API();
			$telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
			//dd($telegram);
			
	}*/
	public function __construct()
	{
		$this->object= new Api(env('TELEGRAM_BOT_TOKEN'));			
	}
	public static function gateway()
	{
		return [];
	}
	 public static function name()
	 {
		return [];
	}
	public function sendMessage(){

        return  $this->object->sendMessage([
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => 'Hello World Bugfix'
        ]);
        
    }
    /**
     * @return mixed
     */
    public static function description()
	{
		return [];
	}

    /**
     * @return integer
     */
    public static function getNewStatusId()
	{
		return [];
	}

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handlerWebhookInvoice(Request $request): mixed
	{
		return[];
	}
	
	
}