<?php
use \LINE\LINEBot\SignatureValidator as SignatureValidator;
/**
 * @var Laravel\Lumen\Routing\Router $router
 */
$router->group([
    'prefix' => env('APP_API_VERSION', ''),
    'namespace' => '\App\Api\V1\Controllers',
   //'middleware' => 'checkUser'
], function ($router) {

	 $router->get('/telegram', 'TelegramController@index');
	 $router->get('/viber', 'ViberController@index');
	 $router->get('/linebot', 'LineController@index');


    /**
     * ADMIN PANEL
     */
    $router->group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => 'checkAdmin'
    ], function ($router) {
    });
});

