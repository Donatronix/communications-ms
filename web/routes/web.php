<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Mails\MailerMail;
use Illuminate\Support\Facades\Mail;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group([
    'prefix' => env('APP_API_PREFIX', '')
], function ($router) {
    include base_path('app/Api/V1/routes.php');
});

/*-------------------------
   T E S T S  Routes
-------------------------- */
$router->group([
    'prefix' => env('APP_API_PREFIX', '') . '/tests'
], function ($router) {
    $router->get('db-test', function () {
        if (DB::connection()->getDatabaseName()) {
            echo "Connected successfully to database: " . DB::connection()->getDatabaseName();
        }
    });

    $router->get('channel-test', function () {
        $type = "line";

        $channel = [
            "Channels Settings" => App\Models\Channel::getChannelSettings($type),
        ];

        echo json_encode($channel);
    });
});

Route::get(env('API_PREFIX') . '/mail-test', function () {
    $data = [
        'recipient_email' => env('MAIL_FROM_ADDRESS'),
        'subject' => 'Test send by amazon',
        'body' => 'This body of mail about test send by amazon'
    ];

    Mail::to($data['recipient_email'])->send(new MailerMail($data));

    // check for failed ones
    if (Mail::failures()) {
        // return failed mails
        return new \Error(Mail::failures());
    }
});
