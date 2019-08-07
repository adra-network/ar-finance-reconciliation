<?php

namespace Phone\Controllers;

use App\User;
use Illuminate\View\View;
use Phone\Models\PhoneNumber;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Phone\Requests\UpdatePhoneNumberRequest;

class PhoneNumbersController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        abort_unless(Gate::allows('transaction_access'), 403);

        $phoneNumbers = PhoneNumber::all();

        return view('phone::phone_numbers.index', compact('phoneNumbers'));
    }

    /**
     * @param PhoneNumber $phoneNumber
     * @return View
     */
    public function edit(PhoneNumber $phoneNumber): View
    {
        abort_unless(Gate::allows('transaction_edit'), 403);

        $users = User::all();

        return view('phone::phone_numbers.edit', compact('phoneNumber', 'users'));
    }

    /**
     * @param UpdatePhoneNumberRequest $request
     * @param PhoneNumber $phoneNumber
     * @return RedirectResponse
     */
    public function update(UpdatePhoneNumberRequest $request, PhoneNumber $phoneNumber): RedirectResponse
    {
        abort_unless(Gate::allows('transaction_edit'), 403);

        $phoneNumber->user_id = $request->user_id;
        $phoneNumber->save();

        return redirect()->route('phone.phone-numbers.index')->withMessage('Edited successfully.');
    }
}
