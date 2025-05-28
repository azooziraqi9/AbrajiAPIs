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
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('tower')->nullable();
            $table->string('invoice_number');
            $table->string('subscriber_name');
            $table->string('subs_type');
            $table->string('subs_price');
            $table->date('activation_date');
            $table->date('expiry_date');
            $table->string('payment_date');
            $table->string('payment_method')->nullable();
            $table->string('payed_price')->default(0);
            $table->string('remaining_price')->default(0);
            $table->string('created_by');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {

        });
    }
};
