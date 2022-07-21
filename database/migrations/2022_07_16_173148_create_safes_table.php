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
        Schema::create('safes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->text('safe_name');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('safe_balance');
            $table->text('safe_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('safes');
    }
};
