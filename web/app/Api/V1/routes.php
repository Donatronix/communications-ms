<?php

/**
 * @var Laravel\Lumen\Routing\Router $router
 */
$router->group([
    'prefix' => env('APP_API_VERSION', ''),
    'namespace' => '\App\Api\V1\Controllers'
], function ($router) {
    /**
     * PUBLIC ACCESS
     *
     * level with free access to the endpoint
     */
    $router->group([
        'namespace' => 'Public'
    ], function ($router) {
        /**
         * Channels OneStep 1.0
         */
        $router->get('channels/auth/{platform}', 'ChannelController');

        /**
         * Send mails
         */
        $router->post('mail/sender', 'SendEmailController');
    });

    /**
     * USER APPLICATION PRIVATE ACCESS
     *
     * Application level for users
     */
    $router->group([
        'namespace' => 'Application',
        'middleware' => 'checkUser'
    ], function ($router) {
        /**
         * Channels Auth
         */
        $router->group([
            'prefix' => 'channels',
        ], function ($router) {
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

        /**
         * Conversations
         */
        $router->group([
            'prefix' => 'conversations',
        ], function ($router) {
            $router->get('/', 'ConversationController@index');
            $router->post('/start', 'ConversationController@store');
            $router->delete('/{id:[a-fA-F0-9\-]{36}}', 'ConversationController@destroy');
        });

        /**
         * Chats
         */
        $router->group([
            'prefix' => 'chats',
        ], function ($router) {
            $router->get('/{conversation_id:[a-fA-F0-9\-]{36}}', 'ChatController@index');
            $router->post('/{conversation_id:[a-fA-F0-9\-]{36}}', 'ChatController@store');
            $router->put('/{id:[a-fA-F0-9\-]{36}}', 'ChatController@update');
        });

        /**
         * Bots
         */
        $router->group([
            'prefix' => 'bot-details',
        ], function ($router) {
            $router->get('/', 'BotDetailController@index');
            $router->post('/', 'BotDetailController@store');
            $router->post('/setwebhookurl', 'BotDetailController@setBotWebHookUrl');
            $router->get('/{id:[a-fA-F0-9\-]{36}}', 'BotDetailController@show');
            $router->put('/{id:[a-fA-F0-9\-]{36}}', 'BotDetailController@update');
            $router->delete('/{id:[a-fA-F0-9\-]{36}}', 'BotDetailController@destroy');
        });

        /**
         * Bot Messages
         */
        $router->group([
            'prefix' => 'bot-messages',
        ], function ($router) {
            $router->post('/send', 'BotMessageController@sendMessage');
            $router->get('/chats/{bot_conversation_id:[a-fA-F0-9\-]{36}}', 'BotMessageController@getBotChats');
            $router->get('/conversations', 'BotMessageController@getBotConversations');
        });
    });

    /**
     * ADMIN PANEL ACCESS
     *
     * Admin / super admin access level (E.g CEO company)
     */
    $router->group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => [
            'checkUser',
            'checkAdmin'
        ]
    ], function ($router) {
        /**
         * Channels (Bots)
         */
        $router->group([
            'prefix' => 'channels',
        ], function ($router) {
            $router->get('/', 'ChannelController@index');
            $router->post('/', 'ChannelController@store');
            $router->get('/{id:[a-fA-F0-9\-]{36}}', 'ChannelController@show');
            $router->put('/{id:[a-fA-F0-9\-]{36}}', 'ChannelController@update');
            $router->delete('/{id:[a-fA-F0-9\-]{36}}', 'ChannelController@destroy');
            $router->post('/{id:[a-fA-F0-9\-]{36}}/update-status', 'ChannelController@updateStatus');
        });
    });

    /**
     * WEBHOOKS
     *
     * Access level of external / internal software services
     */
    $router->group([
        'prefix' => 'webhooks',
        'namespace' => 'Webhooks'
    ], function ($router) {
        $router->group([
            'prefix' => 'messages',
        ], function ($router) {
            $router->post('/{messengerInstance}/webhook', 'MessagesController@handleWebhook');
        });

        /**
         * Save updates coming from the bot
         * The bot will make calls to this route
         */
        $router->post('/saveUpdates/{type}/{token}', 'BotMessageController@saveUpdates');
        $router->get('/whatsapp/webhook', 'BotMessageController@verifyWhatsappWebhook');
        $router->post('/whatsapp/webhook', 'BotMessageController@saveWhatsappUpdates');
    });
});
