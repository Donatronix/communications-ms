<?php

/**
 * @var Laravel\Lumen\Routing\Router $router
 */
$router->group([
    'prefix' => env('APP_API_VERSION', ''),
    'namespace' => '\App\Api\V1\Controllers',
    //'middleware' => 'checkUser'
], function ($router) {
    /**
     * Channels Auth
     */
    $router->group([
        'prefix' => 'channels',
    ], function ($router) {
        $router->get('/auth/{platform}', 'ChannelController');
    });


    $router->get('/bot/{messengerInstance}/send-message', 'MessengerController@sendMessage')->name('send-message');
    $router->get('/bot/{messengerInstance}/webhook', 'MessengerController@handleWebhook')->name('webhook');


    /**
     * ADMIN PANEL
     */
    $router->group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => 'checkAdmin',
    ], function ($router) {
        /**
         * Channels (Bots)
         */
        $router->group([
            'prefix' => 'bots',
        ], function ($router) {
            $router->get('/', 'BotController@index');
            $router->post('/', 'BotController@store');
            $router->get('/{id:[a-fA-F0-9\-]{36}}', 'BotController@show');
            $router->put('/{id:[a-fA-F0-9\-]{36}}', 'BotController@update');
            $router->delete('/{id:[a-fA-F0-9\-]{36}}', 'BotController@destroy');
            $router->post('/{id:[a-fA-F0-9\-]{36}}/update-status', 'BotController@updateStatus');

        });
    });
});
