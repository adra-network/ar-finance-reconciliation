<?php

namespace Phone\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Phone\Models\Allocation;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Phone\Requests\StoreAllocationRequest;
use Phone\Requests\UpdateAllocationRequest;

class AllocationsController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $allocations = Allocation::all();

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
     * @return RedirectResponse
     */
    public function store(StoreAllocationRequest $request): RedirectResponse
    {
        $allocation = Allocation::create($request->all());

        return redirect()->route('phone.allocations.index');
    }

    /**
     * @param Allocation $allocations
     */
    public function show(Allocation $allocations)
    {
        //
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
     * @param Allocation $allocation
     * @return RedirectResponse
     */
    public function update(UpdateAllocationRequest $request, Allocation $allocation): RedirectResponse
    {
        $allocation->update($request->all());

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
