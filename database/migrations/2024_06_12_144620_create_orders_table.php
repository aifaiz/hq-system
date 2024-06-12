<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id');
            $table->bigInteger('distributor_id');
            $table->char('delivery_status')->default('pending')->nullable();
            $table->char('pay_status')->default('DUE')->nullable();
            $table->dateTime('pay_at')->nullable();
            $table->decimal('discount_amount', 10,2)->default(0)->nullable();
            $table->char('discount_type')->default('RM')->nullable();
            $table->decimal('delivery_price', 10,2)->default(0)->nullable();
            $table->decimal('sub_total', 10,2)->default(0)->nullable();
            $table->decimal('grand_total', 10,2)->default(0)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('fpx_ref')->nullable();
            $table->decimal('agent_comm', 10,2)->default(0)->nullable();
            $table->char('agent_paid')->default('NO')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
