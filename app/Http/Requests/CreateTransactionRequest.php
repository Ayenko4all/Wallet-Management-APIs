<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'debit_wallet_id' => 'required|exists:wallets,id',
            'credit_wallet_id' => 'required|exists:wallets,id|different:debit_wallet_id',
            'amount' => 'required|numeric|min:0.01|max:9999999.99',
            'currency' => 'sometimes|string|size:3',
            'metadata' => 'sometimes|array',
            'description' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'debit_wallet_id.required' => 'Debit wallet is required',
            'credit_wallet_id.required' => 'Credit wallet is required',
            'credit_wallet_id.different' => 'Debit and credit wallets must be different',
            'amount.min' => 'Amount must be at least 0.01',
        ];
    }
}
