<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('user_edit');
    }

    public function rules()
    {
        return [
            'name'     => [
                'required',
            ],
            'lastname' => [
                'required',
            ],
            'email'    => [
                'required',
            ],
            'roles.*'  => [
                'integer',
            ],
            'roles'    => [
                'required',
                'array',
            ],
        ];
    }
}
