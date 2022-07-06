<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->text('full_name');
            $table->string('email', 500);
            $table->text('phone');
            $table->text('password');
            $table->text('role');
            $table->text('token');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->text('status');
            $table->text('image')->nullable();
        });
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
};
