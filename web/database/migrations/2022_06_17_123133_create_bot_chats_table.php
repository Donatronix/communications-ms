<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_chats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('message_id');
            $table->string('date');
            $table->string('text');
            $table->string('replied_to_message_id')->nullable();
            $table->foreignUuid('bot_conversation_id')->references('id')->on('bot_conversations')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_chats');
    }
}
