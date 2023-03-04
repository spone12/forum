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
        Schema::create('notation_views', function (Blueprint $table) {
            $table->increments('notation_views_id')->unsigned(false);
            $table->integer('notation_id')->comment('id news');
            $table->integer('counter_views')->comment('View counter');
            $table->date('view_date');

            $table->unique(['notation_id', 'view_date']);
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
        Schema::dropIfExists('notation_views');
    }
}
