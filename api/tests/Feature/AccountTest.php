<?php

namespace Tests\Feature;

use App\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_account_can_be_fetched()
    {
        $account = factory(Account::class)->create();

        $response = $this->getJson(route('accounts.show', $account->id))
            ->assertStatus(200);

        $this->assertEquals($account->toArray(), $response->json()['payload']);
    }

    /** @test */
    public function fetching_unexisting_account_returns_a_404()
    {
        $response = $this->getJson(route('accounts.show', 123))
            ->assertStatus(404);

        $this->assertEmpty($response->json()['payload']);
    }
}
