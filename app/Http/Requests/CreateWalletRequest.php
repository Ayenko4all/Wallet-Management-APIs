<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'user_id' => 'required|exists:users,id',
            'initial_balance' => 'nullable|numeric|min:0|max:1000000'
        ];
    }
}
