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

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Id of the user who added the topic');

            $table->smallInteger('category')->comment('Theme category')->default(0);
            $table->string('name_notation', 150);
            $table->text('text_notation')->comment('News text');
            $table->integer('rating')->default(0)->comment('Rating');
            $table->float('star_rating')->default(0)->comment('Star rating');
            $table->timestamp('notation_add_date')->useCurrent();
            $table->timestamp('notation_edit_date')->nullable();
        });
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
