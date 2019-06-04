<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountMonthRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('account_month_create');
    }

    public function rules()
    {
        return [
            'account_id'        => [
                'required',
                'integer',
            ],
            'month_date'        => [
                'required',
                'date_format:'.config('panel.date_format'),
            ],
            'beginning_balance' => [
                'required',
            ],
            'net_change'        => [
                'required',
            ],
            'ending_balance'    => [
                'required',
            ],
            'export_date'       => [
                'date_format:'.config('panel.date_format'),
                'nullable',
            ],
        ];
    }
}
