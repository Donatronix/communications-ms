<?php

use App\Models\Channel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->enum('bot_type', Channel::$messengers);
            $table->uuid('user_id');
            $table->string('bot_name');
            $table->string('bot_username');
            $table->string('chat_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

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
        Schema::dropIfExists('bot_conversations');
    }
}
