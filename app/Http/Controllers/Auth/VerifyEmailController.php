<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if (!$request->user()->hasVerifiedEmail()) {
            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }
        }

        $dashboardRoute = match ($request->user()->role) {
            'caregiver' => 'caregiver.dashboard',
            'clinician' => 'clinician.dashboard',
            'administrator' => 'administrator.dashboard',
            default => abort(403, 'Invalid user role.'),
        };

        return redirect()->intended(route($dashboardRoute) . '?verified=1');
    }
}