<?php

namespace App\Http\Controllers\Admin;

use Account\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Arr;
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
                    $dateFrom = now()->startOfMonth()->format('Y-m-d');
                    $dateTo = now()->endOfMonth()->format('Y-m-d');
                } else {
                    $id_url = $result->account_id;
                    $startDate = Carbon::parse($result->transaction_date);
                    $dateFrom = $startDate->startOfMonth()->format('Y-m-d');
                    $dateTo = $startDate->endOfMonth()->format('Y-m-d');
                }

                $className = strtolower(get_class($result));
                $className = Arr::last(explode('\\', $className));
                $results_formated['url'] = route('account.transactions.index', [
                    'account_id' => $id_url, 'date_filter' => $dateFrom . ' - ' . $dateTo]).'#'.$className.'-'.$result->id;

                $return[] = $results_formated;
            }
        }

        return response()->json(['results' => $return]);
    }
}
