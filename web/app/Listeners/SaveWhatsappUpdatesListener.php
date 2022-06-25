<?php

namespace App\Listeners;

use App\Models\BotConversation;
use App\Models\BotChat;
use Illuminate\Support\Facades\Log;
use Sumra\SDK\Facades\PubSub;

class SaveWhatsappUpdatesListener
{
    private BotConversation $botconversation;
    private BotChat $botchat;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(BotConversation $botconversation, BotChat $botchat)
    {
        $this->botconversation = $botconversation;
        $this->botchat = $botchat;
    }

    /**
     * Handle the event.
     *
     * @param array $event
     *
     * @return void
     */
    public function handle(array $inputData): void
    {
        try {

            $this->saveBotChats($inputData);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    private function saveBotChats($data)
    {
        $bot_username = $data['bot_username'];
        $chat_id = $data['chat_id'];

        $botconversation = $this->botconversation->where(['bot_username' => $bot_username, 'chat_id' => $chat_id])->first();

        // if botconversation does not exist, create it
        if (!$botconversation) {

            $botconversation = $this->botconversation->create([
                'user_id' => $bot_username,
                'bot_name' => $data['bot_name'],
                'bot_username' => $bot_username,
                'chat_id' => $chat_id,
                'first_name' => $data['first_name'],
                'bot_type' => $data['bot_type'],
                'last_name' => $data['last_name']
            ]);
        }

        // save bot chat
        $this->botchat->create([
            'message_id' => $data['message_id'],
            'date' => $data['date'],
            'text' => $data['text'],
            'sender' => $data['sender'],
            'receiver' => $data['receiver'],
            'replied_to_message_id' => $data['replied_to_message_id'],
            'bot_conversation_id' => $botconversation->id
        ]);
    }
}
