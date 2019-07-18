<?php

namespace Account\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

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
