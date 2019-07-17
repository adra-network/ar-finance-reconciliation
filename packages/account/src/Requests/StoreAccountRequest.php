<?php

namespace Account\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreAccountRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('account_create');
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
