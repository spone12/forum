<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VoteNotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_notation', function (Blueprint $table) {
            $table->increments('vote_notation_id')->unsigned(false);
            $table->integer('id_user')->comment('id пользователя, который проголосовал');
            $table->integer('notation_id')->comment('id новости');
            $table->smallInteger('vote')->comment('1 - like, 0 - дизлайк')->default(1);
            $table->timestamp('vote_date')->nullable();

            //$table->primary('vote_notation_id');
            $table->foreign('id_user', 'vote_notation_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('notation_id')->references('notation_id')->on('notations')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_notation');
    }
}
