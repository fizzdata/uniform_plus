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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable(); // Optional shop association
            $table->integer('s_id')->unique(); // Unique status ID
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color'); // Default color for the status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            //
        });
    }
};
