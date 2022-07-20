<?php

namespace Tests;

use App\Models\Channel;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ChannelsTest extends TestCase
{
    /**
     * Test get channels.
     *
     * @return void
     */
    public function test_admin_get_channels()
    {
        $this->get("/v1/admin/channels", TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }

    /**
     * Test save channels.
     *
     * @return void
     */
    public function test_admin_save_channels()
    {
        $this->post("/v1/admin/channels", [
            "title" => "UltainfinityBot By OneStep",
            "uri" => "telegram",
            "token" => "OTAyOTE3MTAyMTg3NDUf6gDU4.YXlZFA.2Vtm5-rhiia6TyPqS_f1Er6nTVY",
            "type" => "auth",
            "platform" => "ultainfinity",
            "number" => "+8056788888"
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
     * Test get details of a channel.
     *
     * @return void
     */
    public function test_admin_get_one_channels()
    {
        $channel_id = Channel::inRandomOrder()->first()->id;

        $this->get("/v1/admin/channels/{$channel_id}", TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }

    /**
     * Test update details of a channel.
     *
     * @return void
     */
    public function test_admin_update_channels()
    {
        $channel_id = Channel::inRandomOrder()->first()->id;

        $this->put("/v1/admin/channels/{$channel_id}", [
            "title" => "UltainfinityBot By OneStep",
            "uri" => "telegram",
            "token" => "GTAyOTE3MTAyMTg3NDUwNDU4.YXlZFA.2Vtm5-rhyt76TyPqS_f1Er6nTVY",
            "type" => "auth",
            "platform" => "ultainfinity",
            "number" => "+8056788888"
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
     * Test update status of a channel.
     *
     * @return void
     */
    public function test_admin_update_channels_status()
    {
        $channel_id = Channel::inRandomOrder()->first()->id;

        $this->post("/v1/admin/channels/{$channel_id}/update-status", [
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
     * Test delete a channel detail.
     *
     * @return void
     */
    public function test_admin_delete_channel()
    {
        $channel_id = Channel::inRandomOrder()->first()->id;

        $this->delete("/v1/admin/channels/{$channel_id}", [], TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }
}
