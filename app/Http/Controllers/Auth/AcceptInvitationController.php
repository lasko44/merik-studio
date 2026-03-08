<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invitations\AcceptInvitationRequest;
use App\Services\InvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles invitation acceptance and user registration.
 */
class AcceptInvitationController extends Controller
{
    /**
     * Show the invitation acceptance form.
     */
    public function show(string $token, InvitationService $service): Response|RedirectResponse
    {
        $invitation = $service->findByToken($token);

        if (!$invitation || !$invitation->isValid()) {
            return redirect()
                ->route('login')
                ->with('error', 'This invitation is invalid or has expired.');
        }

        return Inertia::render('Auth/AcceptInvitation', [
            'invitation' => [
                'email' => $invitation->email,
                'role' => $invitation->role,
                'inviter_name' => $invitation->inviter->name,
                'token' => $invitation->token,
            ],
        ]);
    }

    /**
     * Accept the invitation and create the user account.
     */
    public function store(string $token, AcceptInvitationRequest $request, InvitationService $service): RedirectResponse
    {
        $invitation = $service->findByToken($token);

        if (!$invitation || !$invitation->isValid()) {
            return redirect()
                ->route('login')
                ->with('error', 'This invitation is invalid or has expired.');
        }

        $user = $service->acceptInvitation($invitation, $request->validated());

        Auth::login($user);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Welcome! Your account has been created successfully.');
    }
}
