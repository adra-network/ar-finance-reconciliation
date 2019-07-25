<?php

namespace Phone\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Phone\Models\Allocations;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class AllocationsController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $allocations = Allocations::all();

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
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $allocation = Allocations::create($request->all());

        return redirect()->route('phone.allocations.index');
    }

    /**
     * @param Allocations $allocations
     */
    public function show(Allocations $allocations)
    {
        //
    }

    /**
     * @param Allocations $allocation
     * @return View
     */
    public function edit(Allocations $allocation): View
    {
        return view('phone::allocations.edit', compact('allocation'));
    }

    /**
     * @param Request $request
     * @param Allocations $allocation
     * @return RedirectResponse
     */
    public function update(Request $request, Allocations $allocation): RedirectResponse
    {
        $allocation->update($request->all());

        return redirect()->route('phone.allocations.index');
    }

    /**
     * @param Allocations $allocation
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Allocations $allocation): RedirectResponse
    {
        $allocation->delete();

        return back();
    }
}
