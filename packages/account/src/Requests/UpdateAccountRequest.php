<?php

namespace Account\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAccountRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('account_edit');
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
