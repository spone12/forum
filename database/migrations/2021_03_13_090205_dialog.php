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
            $table->integer('send')->comment('Who send');
            $table->integer('recive')->comment('With whom');
            $table->timestamp('date_create')->comment('Date the dialog was created ')->useCurrent = true;

            $table->unique(['send', 'recive']);
            $table->foreign('send')->references('id')
                ->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('recive')->references('id')
                ->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
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
