<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return match ($request->user()->role) {
            'caregiver' => redirect()->intended(route('caregiver.dashboard')),
            'clinician' => redirect()->intended(route('clinician.dashboard')),
            'administrator' => redirect()->intended(route('administrator.dashboard')),
            default => abort(403, 'Invalid user role.'),
        };
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
