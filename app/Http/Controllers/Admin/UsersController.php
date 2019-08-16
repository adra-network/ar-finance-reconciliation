<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\User;
use App\Http\Controllers\Controller;
use Phone\Models\AccountPhoneNumber;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Phone\Services\UserNumberSyncService;
use App\Http\Requests\MassDestroyUserRequest;

class UsersController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('user_access'), 403);

        $users = User::all();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('user_create'), 403);

        $roles = Role::all()->pluck('title', 'id');
        $accountPhoneNumbers = AccountPhoneNumber::whereNull('user_id')->get();

        return view('admin.users.create', compact('roles', 'accountPhoneNumbers'));
    }

    public function store(StoreUserRequest $request)
    {
        abort_unless(\Gate::allows('user_create'), 403);

        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', []));

        $syncService = new UserNumberSyncService($user);
        $syncService->syncAccountNumbers($request->input('account_phone_numbers', []));

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $roles = Role::all()->pluck('title', 'id');
        $accountPhoneNumbers = AccountPhoneNumber::whereNull('user_id')->orWhere('user_id', $user->id)->get();

        $user->load('roles');
        $user->load('accountPhoneNumbers');

        return view('admin.users.edit', compact('roles', 'user', 'accountPhoneNumbers'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        $syncService = new UserNumberSyncService($user);
        $syncService->syncAccountNumbers($request->input('account_phone_numbers', []));

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_unless(\Gate::allows('user_show'), 403);

        $user->load('roles');
        $user->load('accountPhoneNumbers');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_unless(\Gate::allows('user_delete'), 403);

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}
