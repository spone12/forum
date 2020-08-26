<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ViewsNotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('views_notation', function (Blueprint $table) {
            $table->increments('views_notation_id')->unsigned(false);
            $table->integer('notation_id')->comment('id новости')->unique();
            $table->text('counter_views')->comment('Счётчик просмотров');
            $table->date('view_date');
           
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
        Schema::dropIfExists('views_notation');
    }
}
