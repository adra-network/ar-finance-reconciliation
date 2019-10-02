<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $user = auth()->user();

        return view('admin.profile.index', ['user' => $user]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function save(Request $request): RedirectResponse
    {
        auth()->user()->update(['email_notifications_enabled' => $request->input('email_notifications_enabled')]);

        return redirect()->back();
    }
}
