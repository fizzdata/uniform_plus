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
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->foreignId('status_id')->constrained('statuses');
            $table->string('by_user')->nullable(); // Tracks the user who updated
            $table->timestamp('changed_at')->default(now()); // Time of change
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_status', function (Blueprint $table) {
            //
        });
    }
};
