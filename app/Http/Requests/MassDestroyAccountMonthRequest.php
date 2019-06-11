<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyAccountMonthRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('account_month_delete'), 403, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:account_months,id',
        ];
    }
}
