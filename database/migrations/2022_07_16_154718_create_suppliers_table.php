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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->text('s_name');
            $table->text('indebt_type');
            $table->unsignedInteger('indebt_amount');
            $table->text('s_phone')->nullable();
            $table->text('s_address')->nullable();
            $table->text('s_notes')->nullable();
            $table->text('deal_type')->nullable();
            $table->text('s_email')->nullable();
            $table->text('s_company')->nullable();
            $table->text('s_nationality')->nullable();
            $table->text('s_tax_number')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
