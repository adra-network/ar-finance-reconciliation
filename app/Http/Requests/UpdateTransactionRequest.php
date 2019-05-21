<?php

namespace App\Http\Requests;

use App\AccountTransaction;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('transaction_edit');
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
