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
        Schema::table('debts', function (Blueprint $table) {
            $table->renameColumn('date', 'debt_timestamp'); // Rename the column
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->timestamp('debt_timestamp')->change(); // Ensure it's a timestamp
            $table->timestamp('paid_at')->nullable()->after('created_at'); // Add the new column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->renameColumn('debt_timestamp', 'date'); // Rename the column back
            $table->dropColumn('paid_at'); // Drop the new column
        });
    }
};
