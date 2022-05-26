<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => env('APP_API_PREFIX', '')], function ($router) {
    include base_path('app/Api/V1/routes.php');
});

/*-------------------------
   T E S T S  Routes
-------------------------- */
$router->group([
    'prefix' => 'tests'
], function () use ($router) {
    $router->get('db-test', function () {
        if (DB::connection()->getDatabaseName()) {
            echo "Connected successfully to database: " . DB::connection()->getDatabaseName();

        }
    });

    $router->get('platform-channel-test', function () {
        $type = "line";
        $channel = [
            "Uri" => App\Models\Channel::getChannelUri($type)->uri,
            "Access Token" => App\Models\Channel::getChannelAccessToken($type)->token,
            "Secret" => App\Models\Channel::getChannelSecret($type)->secret,
        ];
        echo json_encode($channel);  
    });
});