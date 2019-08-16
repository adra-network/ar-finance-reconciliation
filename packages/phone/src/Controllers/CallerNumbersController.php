<?php

namespace Phone\Controllers;

use App\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Phone\Models\CallerPhoneNumber;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Phone\Requests\UpdatePhoneNumberRequest;

class CallerNumbersController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        abort_unless(Gate::allows('transaction_access'), 403);

        $query = CallerPhoneNumber::query();

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $phoneNumbers = $query->paginate(100);

        return view('phone::caller_numbers.index', compact('phoneNumbers'));
    }

    /**
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        abort_unless(Gate::allows('transaction_edit'), 403);

        $phoneNumber = CallerPhoneNumber::findOrFail($id);
        $users = User::all();

        return view('phone::caller_numbers.edit', compact('phoneNumber', 'users'));
    }

    /**
     * @param UpdatePhoneNumberRequest $request
     * @param CallerPhoneNumber $phoneNumber
     * @return RedirectResponse
     */
    public function update(UpdatePhoneNumberRequest $request, int $id): RedirectResponse
    {
        abort_unless(Gate::allows('transaction_edit'), 403);

        $phoneNumber = CallerPhoneNumber::findOrFail($id);
        $phoneNumber->user_id = $request->input('user_id');
        $phoneNumber->save();

        return redirect()->route('phone.caller-numbers.index')->withMessage('Edited successfully.');
    }
}
