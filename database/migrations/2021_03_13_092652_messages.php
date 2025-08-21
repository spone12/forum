<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Messages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id');

            $table->foreignId('dialog')
                ->constrained('dialog', 'dialog_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('User who created a message');

            $table->foreignId('send')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('DELETE AFTER');

            $table->foreignId('recive')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('DELETE AFTER');

            $table->text('text')->comment('Message text')->nullable(false);
            $table->boolean('read')->comment('Message read')->default(0);
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
        Schema::dropIfExists('messages');
    }
}
