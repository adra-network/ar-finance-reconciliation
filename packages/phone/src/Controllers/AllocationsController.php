<?php

namespace Phone\Controllers;

use Illuminate\View\View;
use Phone\Enums\ChargeTo;
use Phone\Models\Allocation;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Phone\Resources\AllocationResource;
use Phone\Requests\StoreAllocationRequest;
use Phone\Requests\UpdateAllocationRequest;

class AllocationsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|View
     */
    public function index()
    {
        $allocations = Allocation::all();
        if (request()->ajax()) {
            return AllocationResource::collection($allocations);
        }

        return view('phone::allocations.index', compact('allocations'));
    }

    /**
     * @return View
     */
    public function create(): View
    {
        return view('phone::allocations.create', compact('allocations'));
    }

    /**
     * @param StoreAllocationRequest $request
     * @return RedirectResponse|AllocationResource
     */
    public function store(StoreAllocationRequest $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'charge_to' => [Rule::in(ChargeTo::ENUM)],
            'account_number' => [],
        ]);

        $allocation = new Allocation($data);
        $allocation->save();

        if ($request->ajax()) {
            return new AllocationResource($allocation);
        }

        return redirect()->route('phone.allocations.index');
    }

    /**
     * @param int $id
     * @return AllocationResource
     */
    public function show(int $id)
    {
        return new AllocationResource(Allocation::findOrFail($id));
    }

    /**
     * @param Allocation $allocation
     * @return View
     */
    public function edit(Allocation $allocation): View
    {
        return view('phone::allocations.edit', compact('allocation'));
    }

    /**
     * @param UpdateAllocationRequest $request
     * @param int $id
     * @return RedirectResponse|AllocationResource
     */
    public function update(UpdateAllocationRequest $request, int $id)
    {
        $data = $request->validate([
            'name' => ['required'],
            'charge_to' => [Rule::in(ChargeTo::ENUM)],
            'account_number' => [],
        ]);

        /** @var Allocation $allocation */
        $allocation = Allocation::findOrFail($id);
        $allocation->update($data);

        if ($request->ajax()) {
            return new AllocationResource($allocation);
        }

        return redirect()->route('phone.allocations.index');
    }

    /**
     * @param Allocation $allocation
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Allocation $allocation): RedirectResponse
    {
        $allocation->delete();

        return back();
    }
}
