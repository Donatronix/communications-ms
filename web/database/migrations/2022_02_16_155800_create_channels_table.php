<?php

use App\Models\Channel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title', 100);
            $table->enum('messenger', Channel::$messengers);
            $table->string('uri', 100);
            $table->string('token', 200); //->unique();

            $table->string('number', 100)->nullable();

            $table->enum('platform', Channel::$platforms);
            $table->enum('type', Channel::$types);

            $table->boolean('status')->default(true);

            // 'webhook_url': f'url.{request.param[1]}',

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
        Schema::dropIfExists('channels');
    }
}
