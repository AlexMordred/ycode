<?php

use App\Account;
use App\Http\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
*/

Route::get('accounts/{id}', function ($id) {
    $account = Account::find($id);

    if (!$account) {
        return ApiResponse::error('Not Found', [], 404);
    }

    return ApiResponse::response($account->toArray());
})->name('accounts.show');

Route::get('accounts/{id}/transactions', 'TransactionsController@index')
    ->name('transactions.index');

Route::post('accounts/{id}/transactions', function (Request $request, $id) {
    $to = $request->input('to');
    $amount = $request->input('amount');
    $details = $request->input('details');

    $account = DB::table('accounts')
             ->whereRaw("id=$id")
             ->update(['balance' => DB::raw('balance-' . $amount)]);

    $account = DB::table('accounts')
             ->whereRaw("id=$to")
             ->update(['balance' => DB::raw('balance+' . $amount)]);

    DB::table('transactions')->insert(
        [
            'from' => $id,
            'to' => $to,
            'amount' => $amount,
            'details' => $details
        ]
    );
});

Route::get('currencies', function () {
    $account = DB::table('currencies')
              ->get();

    return $account;
});
