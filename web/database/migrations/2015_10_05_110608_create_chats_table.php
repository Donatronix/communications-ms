<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
Use Illuminate\Database\Schema\Blueprint;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('message');
            $table->boolean('is_delivered')->default(0);
            $table->boolean('is_seen')->default(0);
            $table->boolean('deleted_from_sender')->default(0);
            $table->boolean('deleted_from_receiver')->default(0);
            $table->uuid('receiver_id');
            $table->uuid('user_id');
            $table->foreignUuid('conversation_id')->references('id')->on('conversations')->constrained();

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
        Schema::dropIfExists('chats');
    }
}
