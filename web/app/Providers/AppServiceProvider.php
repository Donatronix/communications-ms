<?php

namespace App\Providers;

use App\Contracts\MessengerContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

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
        Http::post('https://api.telegram.org/bot[' . env('TELEGRAM_BOT_TOKEN') . ']/setwebhook?url=' . env('APP_URL') . 'api/V1/messages/telegram/webhook');

    }
}
