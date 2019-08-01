<?php


namespace Phone\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Phone\Models\PhoneTransaction;
use Phone\Resources\PhoneTransactionResource;

class PhoneTransactionModalController extends Controller
{

    /**
     * @param int $transaction_id
     * @return PhoneTransactionResource
     */
    public function show(int $transaction_id)
    {
        $transaction = PhoneTransaction::findOrFail($transaction_id);

        return new PhoneTransactionResource($transaction);
    }

    /**
     * @param Request $request
     * @param int $transaction_id
     * @return PhoneTransactionResource
     */
    public function update(Request $request, int $transaction_id)
    {
        $transaction = PhoneTransaction::findOrFail($transaction_id);

        $data = $request->validate([
            'comment' => [],
        ]);

        $transaction->update($data);

        return new PhoneTransactionResource($transaction);
    }

}