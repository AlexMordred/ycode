<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Account;
use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'from' => factory(Account::class)->create()->id,
        'to' => factory(Account::class)->create()->id,
        'details' => $faker->sentence(),
        'amount' => mt_rand(1, 10),
    ];
});
