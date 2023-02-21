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
        Schema::create('notation_photos', function (Blueprint $table) {
            $table->increments('notation_photo_id')->unsigned(false);
            $table->integer('id_user')->comment('id of the user who added the photo ');
            $table->integer('notation_id')->comment('id news');
            $table->text('path_photo')->comment('Photo path');
            $table->timestamp('photo_add_date')->useCurrent(); //CURRENTTIMESTAMP
            $table->timestamp('photo_edit_date')->nullable();

            $table->foreign('id_user')->references('id')
                ->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('notation_photos');
    }
}
