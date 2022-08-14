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
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
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
            "token" => env("TELEGRAM_BOT_TOKEN"),
            "type" => "telegram"
        ], TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
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
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
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
            "token" => env("TELEGRAM_BOT_TOKEN")
        ], TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }

    /**
     * Test setwebhook url details.
     *
     * @return void
     */
    // public function test_set_webhook_url()
    // {
    //     $this->post("/v1/bot-details/setwebhookurl", [
    //         "type" => "telegram"
    //     ], TestHeaders::testHeader());

    //     $this->seeStatusCode(200);
        // $this->seeJsonStructure([
        //     "type",
        //     "title",
        //     "message",
        //     "data"
        // ]);
    // }

    /**
     * Test delete a bot detail.
     *
     * @return void
     */
    // public function test_delete_bot_detail()
    // {
    //     $botdetail_id = BotDetail::inRandomOrder()->first()->id;

    //     $this->delete("/v1/bot-details/{$botdetail_id}", [], TestHeaders::testHeader());
        
    //     $this->seeStatusCode(200);
        // $this->seeJsonStructure([
        //     "type",
        //     "title",
        //     "message",
        //     "data"
        // ]);
    // }
}
