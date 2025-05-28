<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Transaction\Models\Transaction;
use Nette\Utils\Random;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $transactions =[[
            'created_by' => 'admin',
            'cost' => 150.75,
            'description' => 'Payment for services',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ],
        [
            'created_by' => 'admin',
            'cost' => 250.75,
            'description' => 'Payment for goods',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ],
        [
            'created_by' => 'admin',
            'cost' => 350.75,
            'description' => 'Payment for services',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ],
        [
            'created_by' => 'admin',
            'cost' => 450.75,
            'description' => 'Payment for goods',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ],
        [
            'created_by' => 'admin',
            'cost' => 550.75,
            'description' => 'Payment for services',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ],
        [
            'created_by' => 'admin',
            'cost' => 650.75,
            'description' => 'Payment for goods',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ],
        [
            'created_by' => 'admin',
            'cost' => 750.75,
            'description' => 'Payment for services',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ],
        [
            'created_by' => 'admin',
            'cost' => 850.75,
            'description' => 'Payment for goods',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ],
        [
            'created_by' => 'admin',
            'cost' => 950.75,
            'description' => 'Payment for services',
            'date' => '2024-07-04',
            'type' => 'in',
            'category' => 'general'
        ]];
        foreach ($transactions as $transaction) {
            $transaction['transaction_id'] = Random::generate(6); // Generate a random transaction_id
            Transaction::create($transaction);
        }

    }
}
