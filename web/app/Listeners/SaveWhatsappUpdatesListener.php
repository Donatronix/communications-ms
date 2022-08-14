<?php

namespace App\Listeners;

use App\Models\BotConversation;
use App\Models\BotChat;
use Illuminate\Support\Facades\Log;

class SaveWhatsappUpdatesListener
{
    private BotConversation $botconversation;
    private BotChat $botchat;

    /**
     * Create the event listener.
     *
     * @param BotConversation $botconversation
     * @param BotChat $botchat
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
            $bot_username = $inputData['bot_username'];
            $chat_id = $inputData['chat_id'];

            $botconversation = $this->botconversation->where([
                'bot_username' => $bot_username,
                'chat_id' => $chat_id
            ])->first();

            // if botconversation does not exist, create it
            if (!$botconversation) {
                $botconversation = $this->botconversation->create([
                    'user_id' => $bot_username,
                    'bot_name' => $inputData['bot_name'],
                    'bot_username' => $bot_username,
                    'chat_id' => $chat_id,
                    'first_name' => $inputData['first_name'],
                    'bot_type' => $inputData['bot_type'],
                    'last_name' => $inputData['last_name']
                ]);
            }

            // save bot chat
            $this->botchat->create([
                'message_id' => $inputData['message_id'],
                'date' => $inputData['date'],
                'text' => $inputData['text'],
                'sender' => $inputData['sender'],
                'receiver' => $inputData['receiver'],
                'replied_to_message_id' => $inputData['replied_to_message_id'],
                'bot_conversation_id' => $botconversation->id
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
