<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $models = [
        'Account'            => 'global.account.title_plural',
        'AccountTransaction' => 'global.transactions',
    ];

    public function search(Request $request)
    {
        $search = $request->input('search', null);
        $term   = $search['term'];

        if (is_null($term)) {
            abort(500);
        }

        $return = [];
        foreach ($this->models as $modelString => $translation) {
            $model = 'App\\'.$modelString;

            $query = $model::query();

            $fields = $model::$searchable;

            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%'.$term.'%');
            }

            $results = $query->get();

            foreach ($results as $result) {
                $results_formated           = $result->only($fields);
                $results_formated['model']  = trans($translation);
                $results_formated['fields'] = $fields;
                $fields_formated            = [];
                foreach ($fields as $field) {
                    $fields_formated[$field] = title_case(str_replace('_', ' ', $field));
                }
                $results_formated['fields_formated'] = $fields_formated;
                if ($modelString == 'Account') {
                    $id_url    = $result->id;
                    $month_url = date('Y-m', strtotime('now'));
                } else {
                    $id_url    = $result->account_id;
                    $month_url = date('Y-m', strtotime($result->transaction_date));
                }
                $results_formated['url'] = url('/admin/account/transactions?account_id='.$id_url.'&month='.$month_url);

                $return[] = $results_formated;
            }
        }

        return response()->json(['results' => $return]);
    }
}
