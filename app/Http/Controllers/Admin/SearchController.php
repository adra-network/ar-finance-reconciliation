<?php

namespace App\Http\Controllers\Admin;

use Account\Models\Account;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Account\Models\Transaction;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    /** @var array */
    protected $models = [
        Account::class     => 'global.account.title_plural',
        Transaction::class => 'global.transactions',
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('search', null);
        $term = $search['term'];

        if (is_null($term)) {
            abort(500);
        }

        $return = [];
        foreach ($this->models as $modelString => $translation) {
            $model = $modelString.'';

            $query = $model::query();

            $fields = $model::$searchable;

            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%'.$term.'%');
            }

            $results = $query->get();

            foreach ($results as $result) {
                $results_formated = $result->only($fields);
                $results_formated['model'] = trans($translation);
                $results_formated['fields'] = $fields;
                $fields_formated = [];
                foreach ($fields as $field) {
                    $fields_formated[$field] = Str::title(str_replace('_', ' ', $field));
                }
                $results_formated['fields_formated'] = $fields_formated;
                if ($modelString == Account::class) {
                    $id_url = $result->id;
                    $month_url = date('Y-m', strtotime('now'));
                } else {
                    $id_url = $result->account_id;
                    $month_url = date('Y-m', strtotime($result->transaction_date));
                }
                $results_formated['url'] = route('account.transactions.index', ['account_id' => $id_url, 'month' => $month_url]);

                $return[] = $results_formated;
            }
        }

        return response()->json(['results' => $return]);
    }
}
