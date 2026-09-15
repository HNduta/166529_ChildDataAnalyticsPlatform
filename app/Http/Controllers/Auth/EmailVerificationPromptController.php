<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
            ? match ($request->user()->role) {
                'caregiver' => redirect()->intended(route('caregiver.dashboard')),
                'clinician' => redirect()->intended(route('clinician.dashboard')),
                'administrator' => redirect()->intended(route('administrator.dashboard')),
                default => abort(403, 'Invalid user role.'),
            }
            : view('auth.verify-email');
    }
}
