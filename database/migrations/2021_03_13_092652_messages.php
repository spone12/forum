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
        Schema::dropIfExists('messages');
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('message_id')->unsigned(false);
            $table->integer('dialog')->comment('Dialog id');
            $table->integer('send')->comment('Sender');
            $table->integer('recive')->comment('Recipient');
            $table->text('text')->comment('Message text')->nullable(false);
            $table->boolean('read')->comment('Message read')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dialog')->references('dialog_id')->on('dialog')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('send')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('recive')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
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
