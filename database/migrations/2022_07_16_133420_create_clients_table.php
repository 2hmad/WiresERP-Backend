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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->text('c_name');
            $table->unsignedInteger('releated_user')->nullable();
            $table->text('indebt_type');
            $table->unsignedInteger('indebt_amount');
            $table->text('c_phone')->nullable();
            $table->text('c_address')->nullable();
            $table->text('c_notes')->nullable();
            $table->text('deal_type');
            $table->text('c_email')->nullable();
            $table->text('c_company')->nullable();
            $table->text('c_nationality')->nullable();
            $table->text('c_tax_number')->nullable();
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
        Schema::dropIfExists('clients');
    }
};
