<?php

use App\Transaction;
use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transaction::create([
            'from' => 1,
            'to' => 2,
            'details' => 'sample transaction',
            'amount' => 14
        ]);

        Transaction::create([
            'from' => 1,
            'to' => 2,
            'details' => 'sample transaction 2',
            'amount' => 24
        ]);

        Transaction::create([
            'from' => 2,
            'to' => 1,
            'details' => 'sample transaction 3',
            'amount' => 15
        ]);
    }
}
