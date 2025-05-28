<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Debts\Models\Debt;

class DebtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $debts=[[
            'user_id' => 1,
            'username' => 'Abdallah',
            'debt_timestamp' => '2024-01-19 03:14:07',
            'amount' => 100.50,
            'description' => 'test',
            'pay' => false
        ],
        [
            'user_id' => 2,
            'username' => 'Ahmed',
            'debt_timestamp' => '2024-01-19 03:14:07',
            'amount' => 200.50,
            'description' => 'test',
            'pay' => false
        ],
        [
            'user_id' => 3,
            'username' => 'Ali',
            'debt_timestamp' => '2024-01-19 03:14:07',
            'amount' => 300.50,
            'description' => 'test',
            'pay' => false
        ],
        [
            'user_id' => 4,

            'username' => 'Omar',
            'debt_timestamp' => '2024-01-19 03:14:07',
            'amount' => 400.50,
            'description' => 'test',
            'pay' => false
        ],
        [
             'user_id' => 2,
            'username' => 'JohnDoe',
            'debt_timestamp' => '2024-02-20 15:30:10',
            'amount' => 200.75,
            'description' => 'Loan repayment',
            'pay' => true
        ],
        [
            'user_id' => 3,
            'username' => 'JaneDoe',
            'debt_timestamp' => '2024-02-20 15:30:10',
            'amount' => 300.75,
            'description' => 'Loan repayment',
            'pay' => true
        ],
        [
            'user_id' => 4,
            'username' => 'JackDoe',
            'debt_timestamp' => '2024-02-20 15:30:10',
            'amount' => 400.75,
            'description' => 'Loan repayment',
            'pay' => true
        ],
        [
            'user_id' => 5,
            'username' => 'JillDoe',
            'debt_timestamp' => '2024-02-20 15:30:10',
            'amount' => 500.75,
            'description' => 'Loan repayment',
            'pay' => true
        ],
        [
            'user_id' => 6,
            'username' => 'JimDoe',
            'debt_timestamp' => '2024-02-20 15:30:10',
            'amount' => 600.75,
            'description' => 'Loan repayment',
            'pay' => true
        ],
        [
            'user_id' => 7,
            'username' => 'JennyDoe',
            'debt_timestamp' => '2024-02-20 15:30:10',
            'amount' => 700.75,
            'description' => 'Loan repayment',
            'pay' => true
        ],
        [
            'user_id' => 8,
            'username' => 'JesseDoe',
            'debt_timestamp' => '2024-02-20 15:30:10',
            'amount' => 800.75,
            'description' => 'Loan repayment',
            'pay' => true
        ],
        [
            'user_id' => 9,
            'username' => 'JasmineDoe',
            'debt_timestamp' => '2024-02-20 15:30:10',
            'amount' => 900.75,
            'description' => 'Loan repayment',
            'pay' => true
        ]];

        foreach ($debts as $debt) {
            Debt::create($debt);
        }
    }
}
