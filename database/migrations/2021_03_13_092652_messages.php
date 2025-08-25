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

            $table->foreignId('dialog_id')
                ->constrained('dialogs', 'dialog_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('User who created a message');

            $table->text('text')
                ->comment('Message text')
                ->nullable(false);

            $table->boolean('read')
                ->comment('The message has been read')
                ->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['dialog_id', 'created_at']);
            $table->index('user_id');

            $table->fullText('text');
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
