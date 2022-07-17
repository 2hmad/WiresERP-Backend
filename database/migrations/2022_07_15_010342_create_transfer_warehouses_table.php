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
        Schema::create('transfer_warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->text('from');
            $table->text('to');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->date('date');
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_warehouses');
    }
};
