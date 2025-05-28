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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('number');
            $table->date('due_date');
            $table->string('username');
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('created_by');
            $table->integer('discount')->default(0);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
