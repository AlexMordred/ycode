<?php

namespace App\Http\Requests;

use App\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'to' => [
                'required',
                Rule::exists('accounts', 'id')
                    ->whereNot('accounts.id', $this->id),
            ],
            'amount' => [
                'required',
                'numeric',
                'min:1',
                
            ],
            'details' => 'required|string',
        ];

        $account = Account::find($this->id);

        if ($account) {
            $rules['amount'][] = 'max:' . $account->balance;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'to.exists' => 'Invalid account number.',
            'amount.max' => 'You don\'t have that much money on your account.',
        ];
    }
}
