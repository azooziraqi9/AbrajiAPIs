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
        Schema::table('transactions', function (Blueprint $table) {

            $table->string('admin_id')->after('id');

            // Add the foreign key constraint referencing the managers table
            $table->foreign('admin_id')->references('admin_id')->on('managers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['admin_id']); // Drop the foreign key constraint
            $table->dropColumn('admin_id');    // Drop the admin_id column
        });
    }
};
