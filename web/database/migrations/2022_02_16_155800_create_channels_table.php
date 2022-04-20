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
            $table->string('name', 100);
            $table->string('uri', 100);
            $table->string('token', 200)->unique();
            $table->enum('type', Channel::$types);
            $table->enum('platform', Channel::$platforms);
            $table->boolean('status')->default(true);

            // 'webhook_url': f'url.{request.param[1]}',
            // 'status': 'testing',
            // 'is_active': bool(random() < 0.5)  # random true or false

            $table->timestamps();
            $table->softDeletes();
        });
    }

// auth_code = relationship("AuthCode", back_populates="bot", cascade="all, delete-orphan")

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
