<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotationComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notation_comments', function (Blueprint $table) {
            $table->increments('comment_id')->unsigned(false);
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Id of the user who added the comment');

            $table->integer('notation_id')->comment('id news');
            $table->text('text')->comment('Comment text');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('notation_comments');
    }
}
