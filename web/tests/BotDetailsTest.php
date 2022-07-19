<?php

namespace Tests;

use App\Models\BotDetail;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BotDetailsTest extends TestCase
{
    /**
     * Test get bot details.
     *
     * @return void
     */
    public function test_get_bot_details()
    {
        $this->get("/v1/bot-details", TestHeaders::testHeader());

        $this->seeStatusCode(200);
    }

    /**
     * Test save bot details.
     *
     * @return void
     */
    public function test_save_bot_details()
    {
        $this->post("/v1/bot-details", [
            "name" => "MyBot",
            "username" => "my_bot1",
            "token" => "5172219635:AAHoK7H50GhA5TPT9puKrAd6TKBFFgdw6Ks",
            "type" => "telegram"
        ], TestHeaders::testHeader());

        $this->seeStatusCode(200);
    }

    /**
     * Test get details of a bot.
     *
     * @return void
     */
    public function test_get_one_bot_details()
    {
        $botdetail_id = BotDetail::inRandomOrder()->first()->id;

        $this->get("/v1/bot-details/{$botdetail_id}", TestHeaders::testHeader());

        $this->seeStatusCode(200);
    }

    /**
     * Test update details of a bot.
     *
     * @return void
     */
    public function test_update_bot_details()
    {
        $botdetail_id = BotDetail::inRandomOrder()->first()->id;

        $this->put("/v1/bot-details/{$botdetail_id}", [
            "name" => "MyBot",
            "username" => "my_bot",
            "token" => "5172219635:AAHoK7H50GhA5TPT9puKrAd6TKBFFgdw6Ks"
        ], TestHeaders::testHeader());

        $this->seeStatusCode(200);
    }

    /**
     * Test setwebhook url details.
     *
     * @return void
     */
    public function test_set_webhook_url()
    {
        $this->post("/v1/bot-details/setwebhookurl", [
            "type" => "telegram"
        ], TestHeaders::testHeader());

        $this->seeStatusCode(200);
    }

    /**
     * Test delete a bot detail.
     *
     * @return void
     */
    public function test_delete_bot_detail()
    {
        $botdetail_id = BotDetail::inRandomOrder()->first()->id;

        $this->delete("/v1/bot-details/{$botdetail_id}", [], TestHeaders::testHeader());
        
        $this->seeStatusCode(200);
    }
}
