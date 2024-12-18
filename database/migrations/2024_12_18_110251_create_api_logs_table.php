<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('User Id')->nullable();
            $table->string('route')->comment('API route');
            $table->string('method')->comment('API HTTP method');
            $table->ipAddress('ip_address')->comment('Ip address');
            $table->text('headers')->comment('Headers')->nullable();
            $table->text('request_body')->comment('Request body')->nullable();
            $table->integer('status_code')->comment('Returned response code')->nullable();
            $table->text('response_body')->comment('Response body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_logs');
    }
}
