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

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('id of the user who voted');

            $table->integer('notation_id')->comment('id news');
            $table->smallInteger('vote')->comment('1 - like, 0 - dislike')->default(1);
            $table->float('star')->comment('Star')->default(0);
            $table->timestamp('vote_date')->nullable();

            $table->foreign('notation_id')->references('notation_id')
                ->on('notations')->onUpdate('CASCADE')->onDelete('CASCADE');
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
