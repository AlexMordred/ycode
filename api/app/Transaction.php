<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'from',
        'to',
        'details',
        'amount',
    ];

    protected $hidden = [
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($transaction) {
            $accountFrom = Account::find($transaction->from);
            $accountTo = Account::find($transaction->to);

            $accountFrom->update([
                'balance' => $accountFrom->balance - $transaction->amount,
            ]);

            $accountTo->update([
                'balance' => $accountTo->balance + $transaction->amount,
            ]);
        });
    }
}
