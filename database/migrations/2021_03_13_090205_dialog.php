<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Dialog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dialog');
        Schema::create('dialog', function (Blueprint $table) {
            $table->increments('dialog_id')->unsigned(false);
            $table->integer('send')->comment('Кто');
            $table->integer('recive')->comment('С кем');
            $table->timestamp('date_create')->comment('Дата создания диалога')->useCurrent = true;

            $table->unique(['send', 'recive']);
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
        Schema::dropIfExists('dialog');
    }
}
