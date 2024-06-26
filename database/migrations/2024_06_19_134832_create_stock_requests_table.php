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
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id');
            $table->decimal('delivery_price', 10,2)->default(0)->nullable();
            $table->decimal('sub_total', 10,2)->default(1)->nullable();
            $table->decimal('total_amount', 10,2)->default(1);
            $table->char('pay_status')->default('DUE')->nullable();
            $table->char('deliver_status')->default('pending')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};
