<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SeedController extends Controller
{
    public function seedAllModules()
    {
        try {
            Artisan::call('db:seed', ['--class' => 'DebtSeeder']);
            Artisan::call('db:seed', ['--class' => 'TransactionSeeder']);
            Artisan::call('db:seed', ['--class' => 'InvoiceSeeder']);

            return response()->json(['message' => 'Data seeded successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Seeding failed: ' . $e->getMessage()], 500);
        }
    }

    public function deleteAllData(Request $request)
    {
        try {
            // Validate the request to ensure valid table names are provided
            $request->validate([
                'tables' => 'required|array',
                'tables.*' => 'required|string|in:debts,transactions,invoices'
            ]);

            $tables = $request->input('tables');

            foreach ($tables as $table) {
                // Delete all data from each specified table
                DB::table($table)->delete();
            }

            return response()->json(['message' => "All data from the specified tables have been deleted successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
