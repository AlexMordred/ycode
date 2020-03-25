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

    protected $data = [];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'to' => factory(Account::class)->create()->id,
            'amount' => 1.0,
            'details' => 'A dummy transaction',
        ];
    }

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
        $response = $this->getJson(route('transactions.index', 123), $this->data)
            ->assertStatus(404);

        $this->assertEmpty($response->json()['payload']);
    }

    /** @test */
    public function new_transactions_can_be_created()
    {
        $this->assertEquals(0, Transaction::count());

        $accountFrom = factory(Account::class)->create();
        $accountTo = factory(Account::class)->create();

        $data = [
            'to' => $accountTo->id,
            'amount' => 1.0,
            'details' => 'A dummy transaction',
        ];

        $this->postJson(route('transactions.store', $accountFrom->id), $data)
            ->assertStatus(201);

        // Make sure a correct transaction was created
        $transaction = Transaction::first();

        $this->assertNotNull($transaction);

        $this->assertEquals([
            $accountFrom->id,
            $data['to'],
            $data['amount'],
            $data['details'],
        ], [
            $transaction['from'],
            $transaction['to'],
            $transaction['amount'],
            $transaction['details'],
        ]);

        // Make sure the balances have been updated
        $this->assertEquals(
            $accountFrom->balance - $data['amount'],
            $accountFrom->fresh()->balance
        );

        $this->assertEquals(
            $accountTo->balance + $data['amount'],
            $accountTo->fresh()->balance
        );
    }

    /** @test */
    public function funds_cannot_be_sent_from_unexisting_accounts()
    {
        $this->postJson(route('transactions.store', 123), $this->data)
            ->assertStatus(404);
    }

    /** @test */
    public function all_fields_are_required_when_creating_a_transaction()
    {
        $this->assertEquals(0, Transaction::count());

        $accountFrom = factory(Account::class)->create();

        $response = $this->postJson(route('transactions.store', $accountFrom->id), [])
            ->assertStatus(422);

        $this->assertEquals(
            ['to', 'amount', 'details'],
            array_keys($response->json()['payload'])
        );
    }

    /** @test */
    public function funds_cannot_be_sent_to_unexisting_accounts()
    {
        $this->assertEquals(0, Transaction::count());

        $accountFrom = factory(Account::class)->create();
        $this->data['to'] = 123;

        $response = $this->postJson(route('transactions.store', $accountFrom->id), $this->data)
            ->assertStatus(422);

        $this->assertEquals(
            ['to'],
            array_keys($response->json()['payload'])
        );
    }

    /** @test */
    public function funds_cannot_be_sent_to_the_own_account()
    {
        $this->assertEquals(0, Transaction::count());

        $accountFrom = factory(Account::class)->create();
        $this->data['to'] = $accountFrom->id;

        $response = $this->postJson(route('transactions.store', $accountFrom->id), $this->data)
            ->assertStatus(422);

        $this->assertEquals(
            ['to'],
            array_keys($response->json()['payload'])
        );
    }

    /** @test */
    public function a_transaction_cannot_be_created_if_funds_are_insufficient()
    {
        $this->assertEquals(0, Transaction::count());

        $accountFrom = factory(Account::class)->create();
        $this->data['amount'] = $accountFrom->balance + 1;

        $response = $this->postJson(route('transactions.store', $accountFrom->id), $this->data)
            ->assertStatus(422);

        $this->assertEquals(
            ['amount'],
            array_keys($response->json()['payload'])
        );
    }
}
