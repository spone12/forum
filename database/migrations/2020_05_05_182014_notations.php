<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Notations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notations', function (Blueprint $table) {
            $table->Increments('notation_id');
            $table->integer('id_user')->comment('id пользователя, который добавил тему');
            $table->smallInteger('category')->comment('Категория темы');
            $table->string('name_notation', 150)->unique();
            $table->text('text_notation')->comment('Текст нотации');
            $table->timestamp('notation_add_date')->nullable();
            $table->timestamp('notation_edit_date')->nullable();

        });
    }

    //DB::statement("ALTER TABLE `notations` ADD FOREIGN KEY (`notation_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;");



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notations');
    }
}
