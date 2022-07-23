<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = [
            [
                'id' => 'e3276487-349a-4959-a616-e3276487f478',
                'title' => 'WhatsApp Bot',
                'messenger' => 'whatsapp',
                'uri' => '+447787572622',
                'token' => 'ACf3a58e9402a2833cea9a69115f66d2ec:8ce1f68bbbc6dc0725310d633b1ca498',
                'number' => '+447787572622',
                'type' => 'auth',
                'platform' => 'ultainfinity',
                'status' => true,
            ],
            [
                'id' => '3ac69b47-349a-4959-a616-3ac69b45f478',
                'title' => 'WhatsApp Bot (Testing)',
                'messenger' => 'whatsapp',
                'uri' => '+14155238886',
                'token' => 'ACd41c7867838093709600bb6542863769:4a2ead7900e376b2e12c3a46dca6d0db',
                'number' => '+14155238886',
                'type' => 'auth',
                'platform' => 'ultainfinity',
                'status' => false,
            ],
            [
                'id' => 'e3276487-349a-4959-a616-3ac69b45f253',
                'title' => 'OneStepID by Sumra',
                'messenger' => 'telegram',
                'uri' => '@OneStepID_Sumra_Bot',
                'token' => '2078755563:AAHtPzW2xyqngQxbyZSh0U821oRdMeankn8',
                'platform' => 'sumra',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => '373458be-3f01-40ca-b6f3-245239c7889f',
                'title' => 'OneStepID by Ultainfinity',
                'messenger' => 'telegram',
                'uri' => '@OneStepID_Ultainfinity_Bot',
                'token' => '2088982449:AAHJ7d16HCpFI9j9B9JqOX1yDEgSb5piKmc',
                'platform' => 'ultainfinity',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => 'a126ae9d-f55a-443e-ad3f-b1cb20d5e1f1',
                'title' => 'Sumra by OneStep',
                'messenger' => 'viber',
                'uri' => 'sumrabyonestep',
                'token' => '4e2f25788667df4c-5ba2bdeaf5991ff4-b4956e9dba79982',
                'platform' => 'sumra',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => '456c0391-9502-4081-b215-d070c2f803f2',
                'title' => 'Ultainfinity by OneStep',
                'messenger' => 'viber',
                'uri' => 'ultainfinitybyonestep',
                'token' => '4e2f1b3446e7d9c2-ed5513c52400990a-9306a458fd9fef70',
                'platform' => 'ultainfinity',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => '498f8236-568d-4446-b0f6-693dfbb6915c',
                'title' => 'SumraBot by OneStep',
                'messenger' => 'discord',
                'uri' => 'https://discord.gg/75xbhgmbvP',
                'token' => 'OTAwNzczNzQwMTYwMzc2ODQz.YXGM6w.B9B9uaFnz86dwZE5LiCb3ctORrk',
                'platform' => 'sumra',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => '37f1ec65-3a26-407a-9b8a-ac98673d00c0',
                'title' => 'UltainfinityBot By OneStep',
                'messenger' => 'discord',
                'uri' => 'DUMwfyckKy',
                'token' => 'OTAyOTE3MTAyMTg3NDUwNDU4.YXlZFA.2Vtm5-rhiia6TyPqS_f1Er6nTVY',
                'platform' => 'ultainfinity',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => '9bdcdcee-4673-452d-a4f9-bc3c3cd7b2b3',
                'title' => 'Ultainfinity OneStep',
                'messenger' => 'line',
                'uri' => '@410jqinx',
                'token' => 'aVKv550IUP4dU\/HVyQsYVGZelAyLnp1+LlSnK6MQN7RKKzlCaSMiyI40dGL7fv5aRm3LyEeNlQ2XMwuIa9b45+SdM0XLQsZEO2qjj9HcYIyHOOff4LfGGIkCU4UXAypTRb8G0L\/Zzh1+dNBrNz5m8gdB04t89\/1O\/w1cDnyilFU=',
                'platform' => 'ultainfinity',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => 'a886ec79-9254-426c-b4a5-80b8a1d77d1e',
                'title' => 'Sumra by OneStep',
                'messenger' => 'line',
                'uri' => '@587eedqw',
                'token' => 'iJzFgGgCWhhF6MuJKCr9nJzmrLadgvvYtnND\/1L5PU52qNfndGFNNrBtpySwTZVcSYrp54SSFcrUiwxS2CtmGiuHKzYzoeojizpQJhHyH2z98L8K3fIYRhmRTVCueDnbCsWImEp8JixgWN+wZ7ZutQdB04t89\/1O\/w1cDnyilFU=',
                'platform' => 'sumra',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => 'fd9b3d51-04b2-4854-ae71-2da84b482ec6',
                'title' => 'SMS by Twilio',
                'messenger' => 'twilio',
                'uri' => '',
                'sid' => 'ACf3a58e9402a2833cea9a69115f66d2ec',
                'token' => '8ce1f68bbbc6dc0725310d633b1ca498',
                'number' => '+17859758117',
                'platform' => 'ultainfinity',
                'type' => 'auth',
                'status' => true,
            ],
            [
                'id' => 'e3276487-349a-4959-a616-e3276487aadd',
                'title' => 'SMS by Twilio (Testing)',
                'messenger' => 'twilio',
                'uri' => '+19207827608',
                'sid' => 'ACd41c7867838093709600bb6542863769',
                'token' => '4a2ead7900e376b2e12c3a46dca6d0db',
                'number' => '+19207827608',
                'type' => 'auth',
                'platform' => 'ultainfinity',
                'status' => false,
            ]
        ];

        foreach ($list as $item) {
            Channel::create($item);
        }
    }
}
