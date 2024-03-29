<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name');
                $table->string('email', 191)->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->smallInteger('gender')->default(1)->comment('Gender');
                $table->text('avatar')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->timestamp("last_online_at")->useCurrent();
                $table->ipAddress('registration_ip');
                $table->text('user_agent');
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
        Schema::dropIfExists('users');
    }
}
