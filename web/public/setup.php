<?php

$app = require __DIR__.'/../bootstrap/app.php';
use Viber\Client;

echo $apiKey = env('LINE_BOT_CHANNEL_ACCESS_TOKEN'); // <- PLACE-YOU-API-KEY-HERE

$webhookUrl = 'https://chatapi.viber.com/pa/set_webhook'; // <- PLACE-YOU-HTTPS-URL
try {
    $client = new Client([ 'token' => $apiKey ]);
    $result = $client->setWebhook($webhookUrl);
    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: ". $e->getError() ."\n";
}