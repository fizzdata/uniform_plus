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
        Schema::create('status_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('digit_number')->comment('Digit position in the status code');
            $table->integer('code_digit')->comment('Digit value for the status code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_options');
    }
};
