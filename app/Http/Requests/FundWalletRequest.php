<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FundWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1|max:1000000',
            'description' => 'nullable|string|max:255',
            'wallet_number' => 'required|exists:wallets,wallet_number'
        ];
    }
}
