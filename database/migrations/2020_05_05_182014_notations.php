<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->increments('notation_id')->unsigned(false);
            $table->integer('id_user')->comment('id пользователя, который добавил тему');
            $table->smallInteger('category')->comment('Категория темы')->default(0);
            $table->string('name_notation', 150);
            $table->text('text_notation')->comment('Текст нотации');
            $table->integer('rating')->default(0)->comment('Рейтинг');
            $table->timestamp('notation_add_date')->nullable();
            $table->timestamp('notation_edit_date')->nullable();

            //$table->primary('notation_id');
        });

        DB::statement("ALTER TABLE `notations` ADD FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    }

   



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
