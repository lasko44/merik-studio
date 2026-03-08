<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Services\InvitationService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles user management CRUD operations.
 */
class UserController extends Controller
{
    /**
     * Display list of users.
     */
    public function index(UserService $service, InvitationService $invitationService): Response
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('Admin/Users/Index', [
            'users' => $service->getPaginated(),
            'statistics' => $service->getStatistics(),
            'invitationStatistics' => $invitationService->getStatistics(),
            'pendingInvitations' => $invitationService->getPendingPaginated(),
            'roles' => $service->getRoles(),
        ]);
    }

    /**
     * Show create user form.
     */
    public function create(UserService $service): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Admin/Users/Create', [
            'roles' => $service->getRoles(),
        ]);
    }

    /**
     * Store a new user.
     */
    public function store(StoreUserRequest $request, UserService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show edit user form.
     */
    public function edit(User $user, UserService $service): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('roles'),
            'roles' => $service->getRoles(),
        ]);
    }

    /**
     * Update a user.
     */
    public function update(UpdateUserRequest $request, User $user, UserService $service): RedirectResponse
    {
        $service->update($user, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user, UserService $service): RedirectResponse
    {
        $this->authorize('delete', $user);

        $service->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
