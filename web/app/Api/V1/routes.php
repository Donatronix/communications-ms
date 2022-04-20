<?php

/**
 * @var Laravel\Lumen\Routing\Router $router
 */
$router->group([
    'prefix' => env('APP_API_VERSION', ''),
    'namespace' => '\App\Api\V1\Controllers',
], function ($router) {
    /**
     * PUBLIC ACCESS
     */

    /**
     * Messenger instance
     */
    $router->group([
        'prefix' => 'messages',
    ], function ($router) {
        $router->post('/{messengerInstance}/webhook', 'MessagesController@handleWebhook');
    });


    /**
     * Internal access
     */
    $router->group([
        'middleware' => 'checkUser',
    ], function ($router) {
        /**
         * Channels Auth
         */
        $router->group([
            'prefix' => 'channels',
        ], function ($router) {
//            $router->get('/auth/{platform}', 'ChannelController');

            $router->post('/{messengerInstance}/send-message', 'MessagesController@sendMessage');
            $router->post('/{messengerInstance}/webhook', 'MessagesController@handleWebhook');
        });


        /**
         * Messenger instance
         */
        $router->group([
            'prefix' => 'messages',
        ], function ($router) {
            $router->post('/{messengerInstance}/send-message', 'MessagesController@sendMessage');
        });
    });


    /**
     * ADMIN PANEL
     */
    $router->group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => [
            'checkUser',
            'checkAdmin',
        ],
    ], function ($router) {
        /**
         * Channels (Bots)
         */
        $router->group([
            'prefix' => 'bots',
        ], function ($router) {
            $router->get('/', 'ChannelController@index');
            $router->post('/', 'ChannelController@store');
            $router->get('/{id:[a-fA-F0-9\-]{36}}', 'ChannelController@show');
            $router->put('/{id:[a-fA-F0-9\-]{36}}', 'ChannelController@update');
            $router->delete('/{id:[a-fA-F0-9\-]{36}}', 'ChannelController@destroy');
            $router->post('/{id:[a-fA-F0-9\-]{36}}/update-status', 'ChannelController@updateStatus');
        });
    });
});
