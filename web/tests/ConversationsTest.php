<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ConversationsTest extends TestCase
{
    /**
     * Test get conversations.
     *
     * @return void
     */
    public function test_get_conversations()
    {
        $this->get("/v1/conversations", TestHeaders::testHeader());

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "type",
            "title",
            "message",
            "data"
        ]);
    }

    /**
     * Test start a conversation.
     *
     * @return void
     */
    public function test_start_conversation()
    {
        $this->post("/v1/conversations/start", [
            "message" => "Hey, how are you doing?",
            "second_user_id" => "96541e14-45be-4df8-ba8c-c742d1ac1c2c"
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
     * Test delete a conversation.
     *
     * @return void
     */
    // public function test_delete_conversation()
    // {
    //     $conversation_id = Conversation::inRandomOrder()->first()->id;

    //     $this->delete("/v1/conversations/{$conversation_id}", [], TestHeaders::testHeader());
        
    //     $this->seeStatusCode(200);
        // $this->seeJsonStructure([
        //     "type",
        //     "title",
        //     "message",
        //     "data"
        // ]);
    // }
}
