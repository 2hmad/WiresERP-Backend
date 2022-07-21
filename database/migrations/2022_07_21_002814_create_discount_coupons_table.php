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
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('code');
            $table->unsignedFloat('amount');
            $table->date('expire_date');
            $table->text('section');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
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
        Schema::dropIfExists('discount_coupons');
    }
};
