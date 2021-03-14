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
            $table->integer('dialog')->comment('id Диалога');
            $table->integer('send')->comment('Отправитель');
            $table->integer('recive')->comment('Получатель');
            $table->text('text')->comment('Текст сообщения')->nullable(false);
            $table->boolean('read')->comment('Прочитано сообщение')->default(0);
            $table->timestamps();

            $table->unique('dialog');
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
