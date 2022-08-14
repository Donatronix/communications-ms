<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\SendSMSListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'SendSMS' => [
            SendSMSListener::class,
        ],
        'sendVerificationEmail' => [
            'App\Listeners\SendVerificationEmailListener'
        ],
        'saveWhatsappUpdates' => [
            'App\Listeners\SaveWhatsappUpdatesListener'
        ],
        'sendForgotMailEmail' => [
            'App\Listeners\SendForgotPasswordEmailListener'
        ],
        'sendCreatePasswordEmail' => [
            'App\Listeners\SendCreatePasswordEmailListener'
        ],
        'sendRewardForInstallEmail' => [
            'App\Listeners\SendRewardForInstallListener'
        ],
        'sendRewardForReferralEmail' => [
            'App\Listeners\SendRewardForReferralListener'
        ],
        'sendEmail' => [
            'App\Listeners\SendMailerListener'
        ],
        'Illuminate\Mail\Events\MessageSent' => [
            'App\Listeners\MailerLogSentListener',
        ],
        'alarmWarehouseEmail' => [
            'App\Listeners\AlarmWarehouseEmailListener'
        ],
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
