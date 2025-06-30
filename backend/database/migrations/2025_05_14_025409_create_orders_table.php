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
            $table->string('shopify_order_id')->unique();
            $table->foreignId('status_id')->constrained('statuses')->onDelete('restrict');
            $table->string('customer_name')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->date('order_date')->nullable();  
            $table->enum('payment_method', ['in_store', 'phone', 'website']);
            $table->enum('delivery_type', ['delivery', 'pickup'])->nullable();
            $table->boolean('is_custom_order')->default(false);
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
