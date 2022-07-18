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
            $table->text('from_warehouse');
            $table->text('to_warehouse');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->date('date');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('transfer_warehouses');
    }
};
