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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->text('company_id');
            $table->text('warehouse_id');
            $table->text('barcode');
            $table->text('warehouse_balance');
            $table->unsignedInteger('total_price');
            $table->text('product_name');
            $table->text('product_unit');
            $table->unsignedInteger('wholesale_price');
            $table->unsignedInteger('piece_price');
            $table->unsignedInteger('min_stock')->nullable();
            $table->text('product_model')->nullable();
            $table->text('category');
            $table->text('sub_category');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('products');
    }
};
