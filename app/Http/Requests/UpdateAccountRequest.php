<?php

namespace App\Http\Requests;

use App\Account;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('account_edit');
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
