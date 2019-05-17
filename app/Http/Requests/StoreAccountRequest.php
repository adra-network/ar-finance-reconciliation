<?php

namespace App\Http\Requests;

use App\Account;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('account_create');
    }

    public function rules()
    {
        return [
            'code' => [
                'required',
            ],
        ];
    }
}
