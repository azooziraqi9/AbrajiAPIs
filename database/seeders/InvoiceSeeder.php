<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Invoice\Models\Invoice;
use Nette\Utils\Random;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $invoices = [
            [
                'user_id' => '318820',
                'due_date' => '2024-07-15',
                'username' => 'johndoe',
                'type' => 'service',
                'amount' => 1500.00,
                'description' => 'Consulting services',
                'created_by' => 'admin',
                'discount' => 5,
                'discount_value' => 75.00,
                'total' => 1425.00,
                'payment_method' => 'credit_card',
                'payment_date' => '2024-07-05',
                'invoice_items' => [
                    [
                        'name' => 'item1',
                        'price' => '50',
                        'quantity' => '5',
                        'total' => '250'
                    ]
                ]
            ],
            [
                'user_id' => '314159',
                'due_date' => '2024-08-10',
                'username' => 'janedoe',
                'type' => 'product',
                'amount' => 2500.00,
                'description' => 'Software development',
                'created_by' => 'admin',
                'discount' => 10,
                'discount_value' => 250.00,
                'total' => 2250.00,
                'payment_method' => 'bank_transfer',
                'payment_date' => '2024-08-01',
                'invoice_items' => [
                    [
                        'name' => 'item2',
                        'price' => '100',
                        'quantity' => '10',
                        'total' => '1000'
                    ]
                ]
            ],
            [
                'user_id' => '271828',
                'due_date' => '2024-09-20',
                'username' => 'johndoe',
                'type' => 'service',
                'amount' => 3500.00,
                'description' => 'Web design services',
                'created_by' => 'admin',
                'discount' => 15,
                'discount_value' => 525.00,
                'total' => 2975.00,
                'payment_method' => 'cash',
                'payment_date' => '2024-09-10',
                'invoice_items' => [
                    [
                        'name' => 'item3',
                        'price' => '150',
                        'quantity' => '15',
                        'total' => '2250'
                    ]
                ]
            ],
            [
                'user_id' => '161803',
                'due_date' => '2024-10-25',
                'username' => 'janedoe',
                'type' => 'product',
                'amount' => 4500.00,
                'description' => 'Mobile app development',
                'created_by' => 'admin',
                'discount' => 20,
                'discount_value' => 900.00,
                'total' => 3600.00,
                'payment_method' => 'credit_card',
                'payment_date' => '2024-10-15',
                'invoice_items' => [
                    [
                        'name' => 'item4',
                        'price' => '200',
                        'quantity' => '20',
                        'total' => '4000'
                    ]
                ]
            ],
            [
                'user_id' => '141421',
                'due_date' => '2024-11-30',
                'username' => 'johndoe',
                'type' => 'service',
                'amount' => 5500.00,
                'description' => 'SEO services',
                'created_by' => 'admin',
                'discount' => 25,
                'discount_value' => 1375.00,
                'total' => 4125.00,
                'payment_method' => 'bank_transfer',
                'payment_date' => '2024-11-20',
                'invoice_items' => [
                    [
                        'name' => 'item5',
                        'price' => '250',
                        'quantity' => '25',
                        'total' => '6250'
                    ]
                ]
            ],
            [
                'user_id' => '112358',
                'due_date' => '2024-12-15',
                'username' => 'janedoe',
                'type' => 'product',
                'amount' => 6500.00,
                'description' => 'E-commerce website development',
                'created_by' => 'admin',
                'discount' => 30,
                'discount_value' => 1950.00,
                'total' => 4550.00,
                'payment_method' => 'cash',
                'payment_date' => '2024-12-05',
                'invoice_items' => [
                    [
                        'name' => 'item6',
                        'price' => '300',
                        'quantity' => '30',
                        'total' => '9000'
                    ]
                ]
            ],
            [
                'user_id' => '101010',
                'due_date' => '2025-01-20',
                'username' => 'johndoe',
                'type' => 'service',
                'amount' => 7500.00,
                'description' => 'Content writing services',
                'created_by' => 'admin',
                'discount' => 35,
                'discount_value' => 2625.00,
                'total' => 4875.00,
                'payment_method' => 'credit_card',
                'payment_date' => '2025-01-10',
                'invoice_items' => [
                    [
                        'name' => 'item7',
                        'price' => '350',
                        'quantity' => '35',
                        'total' => '12250'
                    ]
                ]
            ],
            [
                'user_id' => '202020',
                'due_date' => '2025-02-25',
                'username' => 'janedoe',
                'type' => 'product',
                'amount' => 8500.00,
                'description' => 'Graphic design services',
                'created_by' => 'admin',
                'discount' => 40,
                'discount_value' => 3400.00,
                'total' => 5100.00,
                'payment_method' => 'bank_transfer',
                'payment_date' => '2025-02-15',
                'invoice_items' => [
                    [
                        'name' => 'item8',
                        'price' => '400',
                        'quantity' => '40',
                        'total' => '16000'
                    ]
                ]
            ],
            [
                'user_id' => '404040',
                'due_date' => '2025-03-30',
                'username' => 'johndoe',
                'type' => 'service',
                'amount' => 9500.00,
                'description' => 'Marketing services',
                'created_by' => 'admin',
                'discount' => 45,
                'discount_value' => 4275.00,
                'total' => 5225.00,
                'payment_method' => 'cash',
                'payment_date' => '2025-03-20',
                'invoice_items' => [
                    [
                        'name' => 'item9',
                        'price' => '450',
                        'quantity' => '45',
                        'total' => '20250'
                    ]
                ]
            ],
            [
                'user_id' => '505050',
                'due_date' => '2025-04-15',
                'username' => 'janedoe',
                'type' => 'product',
                'amount' => 10500.00,
                'description' => 'Video production services',
                'created_by' => 'admin',
                'discount' => 50,
                'discount_value' => 5250.00,
                'total' => 5250.00,
                'payment_method' => 'credit_card',
                'payment_date' => '2025-04-05',
                'invoice_items' => [
                    [
                        'name' => 'item10',
                        'price' => '500',
                        'quantity' => '50',
                        'total' => '25000'
                    ]
                ]
            ],
            [
                'user_id' => '606060',
                'due_date' => '2025-05-20',
                'username' => 'johndoe',
                'type' => 'service',
                'amount' => 11500.00,
                'description' => 'Photography services',
                'created_by' => 'admin',
                'discount' => 55,
                'discount_value' => 6325.00,
                'total' => 5175.00,
                'payment_method' => 'bank_transfer',
                'payment_date' => '2025-05-10',
                'invoice_items' => [
                    [
                        'name' => 'item11',
                        'price' => '550',
                        'quantity' => '55',
                        'total' => '30250'
                    ]
                ]
            ]

        ];

        foreach ($invoices as $invoiceData) {
            DB::beginTransaction();
            try {
                // Create the invoice
                $invoice = Invoice::create([
                    'user_id' => $invoiceData['user_id'],
                    'due_date' => $invoiceData['due_date'],
                    'username' => $invoiceData['username'],
                    'type' => $invoiceData['type'],
                    'amount' => $invoiceData['amount'],
                    'description' => $invoiceData['description'],
                    'created_by' => $invoiceData['created_by'],
                    'discount' => $invoiceData['discount'],
                    'discount_value' => $invoiceData['discount_value'],
                    'total' => $invoiceData['total'],
                    'payment_method' => $invoiceData['payment_method'],
                    'payment_date' => $invoiceData['payment_date'],
                    'number' => Random::generate(6, '0-9')  // Generate a random number for the invoice
                ]);

                // Create related invoice items
                $invoice->invoiceItems()->createMany($invoiceData['invoice_items']);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error creating invoice: ' . $e->getMessage());
                throw new \Exception('An error occurred while creating the invoice.');
            }
        }
    }
}
