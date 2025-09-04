<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotationPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notation_photo', function (Blueprint $table) {
            $table->increments('notation_photo_id')->unsigned(false);

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Id of the user who added the photo');

            $table->integer('notation_id')->comment('News id');
            $table->text('path_photo')->comment('Photo path');
            $table->timestamp('photo_add_date')->useCurrent();
            $table->timestamp('photo_edit_date')->nullable();

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
        Schema::dropIfExists('notation_photo');
    }
}
