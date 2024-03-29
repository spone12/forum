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
        Schema::create(
            'description_profile', function (Blueprint $table) {
                $table->Increments('description_profile_id');
                $table->integer('user_id')->comment('user id, from the users table');
                $table->smallInteger('lvl')->default(1)->comment('User level');
                $table->float('exp')->default(0)->comment('Experience');
                $table->string('real_name', 100)->nullable();
                $table->date('date_born')->nullable();
                $table->string('town', 100)->nullable();
                $table->string('phone', 20)->nullable();
                $table->text('about')->nullable();

                $table->foreign('user_id')->references('id')
                    ->on('users')->onUpdate('CASCADE')->onDelete('RESTRICT');
            }
        );
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
