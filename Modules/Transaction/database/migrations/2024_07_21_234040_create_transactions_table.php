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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('created_by');
            $table->decimal('cost', 10, 2);
            $table->text('description');
            $table->date('date');
            $table->enum('type', ['in', 'out']);
            $table->enum('category', ['maintenance', 'expense', 'general']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
