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
        Schema::create('status_transitions', function (Blueprint $table) {
            $table->id(); // Add a primary key
            $table->foreignId('current_status_id')->constrained('statuses')->onDelete('cascade');
            $table->foreignId('next_status_id')->constrained('statuses')->onDelete('cascade');
            $table->unique(['current_status_id', 'next_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_transitions');
    }
};
