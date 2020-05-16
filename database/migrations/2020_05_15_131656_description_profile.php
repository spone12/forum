<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DescriptionProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('description_profile', function (Blueprint $table) {
            $table->Increments('description_profile_id');
            $table->integer('id_user')->comment('id пользователя, с таблицы users');
            $table->string('real_name', 100);
            $table->date('date_born');
            $table->string('town', 100);
            $table->text('about')->nullable();
        });
        
        DB::statement("ALTER TABLE `description_profile` ADD FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('description_profile');
    }
}
