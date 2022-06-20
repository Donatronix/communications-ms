<?php

namespace App\Providers;

use App\Contracts\MessengerContract;
use App\Models\Channel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MessengerContract::class);

//        $type = "telegram";
//        Http::post('https://api.telegram.org/bot[' . Channel::getChannelSettings($type)->token . ']/setwebhook?url=' . Channel::getChannelSettings($type)->uri . 'api/V1/messages/telegram/webhook');
    }
}
