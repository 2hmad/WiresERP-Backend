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
        Schema::create('sale_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('bill_number');
            $table->timestamp('date_time');
            $table->unsignedInteger('warehouse_id');
            $table->unsignedInteger('value_added_tax')->comment("شامل الضريبة 1 ، غير شامل الضريبة 0");
            $table->unsignedInteger('final_total');
            $table->unsignedInteger('paid');
            $table->text('status');
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
        Schema::dropIfExists('sale_bills');
    }
};
