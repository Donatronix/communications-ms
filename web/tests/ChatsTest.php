<?php

namespace Tests;

use App\Models\Chat;
use App\Models\Conversation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ChatsTest extends TestCase
{
    /**
     * Test get chats.
     *
     * @return void
     */
    public function test_get_chats()
    {
        $conversation_id = Conversation::inRandomOrder()->first()->id;

        $this->get("/v1/chats/{$conversation_id}", TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }

    /**
     * Test reply to a chat.
     *
     * @return void
     */
    public function test_reply_chat()
    {
        $conversation_id = Conversation::inRandomOrder()->first()->id;

        $this->post("/v1/chats/{$conversation_id}", [
            "message" => "Hey, how are you doing?",
            "receiver_id" => "96541e14-45be-4df8-ba8c-c742d1ac1c2c"
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
     * Test update a chat.
     *
     * @return void
     */
    public function test_update_chat()
    {
        $chat_id = Chat::inRandomOrder()->first()->id;

        $this->put("/v1/chats/{$chat_id}", [
            "status" => "delivered",
        ], TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }
}
