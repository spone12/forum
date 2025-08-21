<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dialog_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dialog_id')
                ->constrained('dialog', 'dialog_id')
                ->cascadeOnDelete()
                ->comment('Dialog id');

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('User id');

            $table->enum('role', [
                'member', 'admin', 'owner'
            ])->default('member');

            $table->timestamp('joined_at')->comment('The time at which the user entered the chat')->useCurrent = true;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dialog_participants');
    }
};
