<?php

$app = require __DIR__.'/../bootstrap/app.php';
use Viber\Client;

$apiKey = env('VIBER_BOT_CHANNEL_ACCESS_TOKEN'); // <- PLACE-YOU-API-KEY-HERE

$webhookUrl = 'https://927b-117-96-42-93.ngrok.io/communications-ms/web/public/v1/viber'; // for exmaple https://my.com/bot.php
		//dd($this->apiKey);
try {
	$client = new Client(['token' => $apiKey]);
	$result = $client->setWebhook($webhookUrl);
	echo "Success!\n"; // print_r($result);
} catch (Exception $e) {
	echo 'Error: ' . $e->getMessage() . "\n";
}