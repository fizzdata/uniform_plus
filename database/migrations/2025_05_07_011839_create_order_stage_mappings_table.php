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
        Schema::create('order_stage_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_stage_id');
            $table->string('shopify_order_id');
            $table->string('order_id')->nullable(); // For your internal reference if needed
            $table->timestamp('entered_at');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_stage_mappings');
    }
};
