<?php

namespace App\Http\Controllers;

use App\Account;
use App\Http\ApiResponse;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index($id) {
        $account = Account::find($id);

        if (!$account) {
            return ApiResponse::error('Not Found', [], 404);
        }

        $transactions = Transaction::where('from', $id)
            ->orWhere('to', $id)
            ->get();
        
        return ApiResponse::response($transactions->toArray());
    }

    public function store(Request $request, $id) {
        $account = Account::find($id);

        if (!$account) {
            return ApiResponse::error('Not Found', [], 404);
        }

        Transaction::create(array_merge($request->all(), [
            'from' => $id,
        ]));

        return ApiResponse::created();
    }
}
