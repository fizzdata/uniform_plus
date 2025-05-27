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
        Schema::create('inventory_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->bigInteger('inventory_item_id');
            $table->enum('action_type', ['Add', 'Remove', 'Adjust', 'Transfer']);
            $table->integer('quantity')->default(0);
            $table->string('shopify_location_id_from')->nullable();
            $table->string('shopify_location_id_to')->nullable();
            $table->timestamp('performed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_actions');
    }
};
