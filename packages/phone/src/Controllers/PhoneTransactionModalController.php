<?php

namespace Phone\Controllers;

use Illuminate\Http\Request;
use Phone\Models\Allocation;
use Phone\Models\PhoneNumber;
use Illuminate\Validation\Rule;
use Phone\Enums\AutoAllocation;
use Phone\Models\PhoneTransaction;
use App\Http\Controllers\Controller;
use Phone\Resources\AllocationResource;
use Phone\Resources\PhoneNumberResource;
use Phone\Resources\PhoneTransactionResource;

class PhoneTransactionModalController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function load(Request $request)
    {
        if ($transaction_id = $request->input('transaction_id', null)) {
            $transaction = PhoneTransaction::with('phone_number')->findOrFail($transaction_id);
            $phoneNumber = $transaction->phone_number;
        }
        if ($phoneNumber_id = $request->input('phone_number_id', null)) {
            $phoneNumber = PhoneNumber::findOrFail($phoneNumber_id);
        }

        $phoneNumber->loadSuggestedAllocation();

        $allocations = Allocation::get();

        return response()->json([
            'transaction' => isset($transaction) ? new PhoneTransactionResource($transaction) : null,
            'allocations' => AllocationResource::collection($allocations),
            'phoneNumber' => new PhoneNumberResource($phoneNumber),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $transaction_id = $request->input('transaction.id', null);
        $phoneNumber_id = $request->input('phoneNumber.id', null);

        abort_if(! $phoneNumber_id, 'No phone number object provided.');

        if ($transaction_id) {
            $transactionData = data_get($request->validate([
                'transaction.comment' => [],
                'transaction.allocation_id' => [],
            ]), 'transaction');

            /** @var PhoneTransaction $transaction */
            $transaction = PhoneTransaction::find($transaction_id);
            $transaction->update($transactionData);

            $phoneNumberData = data_get($request->validate([
                'phoneNumber.auto_allocation' => [Rule::in(AutoAllocation::ENUM)],
                'phoneNumber.name' => [],
                'phoneNumber.phone_number' => [],
                'phoneNumber.remember' => [],
            ]), 'phoneNumber');
        } else {
            $phoneNumberData = data_get($request->validate([
                'phoneNumber.auto_allocation' => [],
                'phoneNumber.name' => [],
                'phoneNumber.phone_number' => [],
                'phoneNumber.remember' => [],
                'phoneNumber.comment' => [],
                'phoneNumber.allocation_id' => [],
            ]), 'phoneNumber');
        }

        /** @var PhoneNumber $phoneNumber */
        $phoneNumber = PhoneNumber::find($phoneNumber_id);
        $phoneNumber->update($phoneNumberData);

        return response()->json('OK', 200);
    }
}
