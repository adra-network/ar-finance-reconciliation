<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    protected $models = [
        'Account' => 'global.account.title_plural',
        'AccountTransaction' => 'global.transactions',
    ];

    public function search(Request $request)
    {

        $search = $request->input('search', false);
        $term = $search['term'];

        if (!$term) {
            abort(500);
        }

        $return = [];
        foreach ($this->models as $modelString => $translation) {
            $model = 'App\\' . $modelString;

            $query = $model::query();

            $fields = $model::$searchable;

            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $term . '%');
            }

            $results = $query->get();

            foreach ($results as $result) {
                $results_formated = $result->only($fields);
                $results_formated['model'] = trans($translation);
                $results_formated['fields'] = $fields;
                $fields_formated = [];
                foreach ($fields as $field) {
                    $fields_formated[$field] = title_case(str_replace('_', ' ', $field));
                }
                $results_formated['fields_formated'] = $fields_formated;
                if($modelString=="Account")
                    $id_url=$result->id;
                else $id_url=$result->account_id;
                $results_formated['url'] = url('/admin/transactions/account?account_id=' . $id_url);

                $return[] = $results_formated;
            }
        }

        return response()->json(['results' => $return]);
    }
}
