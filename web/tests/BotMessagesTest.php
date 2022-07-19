<?php

namespace Tests;

use App\Models\BotConversation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BotMessagesTest extends TestCase
{
    /**
     * Test get bot conversation.
     *
     * @return void
     */
    public function test_get_bot_conversations()
    {
        $this->get("/v1/bot-messages/conversations?type=telegram", TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }

    /**
     * Test send bot messages.
     *
     * @return void
     */
    public function test_send_bot_messages()
    {
        $this->post("/v1/bot-messages/send", [
            "text" => "Hey, how are you doing?",
            "chat_id" => "498898710",
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
     * Test get bot chats.
     *
     * @return void
     */
    public function test_get_bot_chats()
    {
        $bot_conversation_id = BotConversation::inRandomOrder()->first()->id;

        $this->get("/v1/bot-messages/chats/{$bot_conversation_id}", TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }
}
