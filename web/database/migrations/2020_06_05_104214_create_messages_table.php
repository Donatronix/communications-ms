<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('subject');
            $table->longText('body');
            $table->string('recipient');
            $table->uuid('sender_id')->default(config('settings.empty_uuid'));
            $table->unsignedTinyInteger('status')->default(0);
            $table->mediumText('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
