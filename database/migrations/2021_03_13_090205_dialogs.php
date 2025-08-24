<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Dialogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialogs', function (Blueprint $table) {
            $table->id('dialog_id');

            $table->string('title', 100)
                ->comment('Dialog title');

            $table->enum('type', [
                'private', 'group'
            ])->default('private');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('The user who created the dialog');

            $table->timestamp('date_create')->comment('Date the dialog was created')->useCurrent = true;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dialogs');
    }
}
