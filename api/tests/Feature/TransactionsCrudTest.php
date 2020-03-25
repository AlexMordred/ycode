<?php

namespace Tests\Feature;

use App\Account;
use App\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionsCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_list_of_account_transactions_can_be_fetched()
    {
        $account = factory(Account::class)->create();

        // Transactions sent FROM the account
        $transactionsFrom = factory(Transaction::class, 3)->create([
            'from' => $account->id,
        ]);

        // Transactions sent TO the account
        $transactionsTo = factory(Transaction::class, 3)->create([
            'from' => $account->id,
        ]);

        $transactions = array_merge(
            $transactionsFrom->toArray(), $transactionsTo->toArray()
        );

        $response = $this->getJson(route('transactions.index', $account->id))
            ->assertStatus(200);

        $this->assertEquals($transactions, $response->json()['payload']);
    }

    /** @test */
    public function transactions_cannot_be_fetched_for_unexisting_accounts()
    {
        $response = $this->getJson(route('transactions.index', 123))
            ->assertStatus(404);

        $this->assertEmpty($response->json()['payload']);
    }
}
