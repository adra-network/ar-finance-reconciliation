<?php

namespace Phone\Controllers;

use Illuminate\View\View;
use Phone\Models\PhoneNumber;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class PhoneNumbersController extends Controller
{
    /**
     * @return View
     */
    public function index() : View
    {
        $phoneNumbers = PhoneNumber::all();

        return view('phone::phone_numbers.index', compact('phoneNumbers'));
    }

    /**
     * @return View
     */
    public function edit() : View
    {
        return view('phone::phone_numbers.edit');
    }

    /**
     * @return RedirectResponse
     */
    public function update() : RedirectResponse
    {
        return redirect()->route('phone.phone-numbers.index')->withMessage('Edited successfully.');
    }
}
