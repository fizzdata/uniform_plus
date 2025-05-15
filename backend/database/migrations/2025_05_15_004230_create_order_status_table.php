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
        Schema::table('order_status', function (Blueprint $table) {
            $table->id();
        $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
        $table->foreignId('status_id')->constrained('statuses');
        $table->foreignId('user_id')->constrained('users'); // Tracks the user who updated
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
