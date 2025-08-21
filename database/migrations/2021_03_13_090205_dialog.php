<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Dialog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialog', function (Blueprint $table) {
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

            $table->foreignId('send')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('DELETE AFTER');

            $table->foreignId('recive')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('DELETE AFTER');

            $table->timestamp('date_create')->comment('Date the dialog was created ')->useCurrent = true;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dialog');
    }
}
