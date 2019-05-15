<?php

namespace App\Http\Requests;

use App\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('transaction_create');
    }

    public function rules()
    {
        return [
            'account_id'       => [
                'required',
                'integer',
            ],
            'transaction_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'code'             => [
                'required',
            ],
        ];
    }
}
