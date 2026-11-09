<section class="space-y-4">
    <header>
        <p class="eyebrow uppercase text-red-600 mb-1">Danger Zone</p>
        <h2 class="text-base font-extrabold tracking-tight text-red-700">
            Delete Account
        </h2>
        <p class="mt-1 text-xs text-[var(--ink-soft)] leading-relaxed">
            Once your account is deleted, all associated profile records will be permanently removed. Please enter your password below only if you wish to permanently close your account.
        </p>
    </header>

    <details class="group" {{ $errors->userDeletion->isNotEmpty() ? 'open' : '' }}>
        <summary class="inline-flex items-center gap-1.5 border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold px-3.5 py-2 rounded-lg cursor-pointer list-none transition">
            <span>Delete My Account</span>
        </summary>

        <form method="post" action="{{ route('profile.destroy') }}" class="mt-4 p-4 rounded-xl bg-red-50/60 border border-red-200 space-y-4">
            @csrf
            @method('delete')

            <p class="text-xs font-semibold text-red-800">
                Are you sure you want to permanently delete your account? Enter your current password to confirm:
            </p>

            <div>
                <label for="delete_account_password" class="sr-only">Password</label>
                <div class="relative">
                    <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="4" y="11" width="16" height="9" rx="2"/>
                        <path d="M8 11V7a4 4 0 118 0v4"/>
                    </svg>
                    <input
                        id="delete_account_password"
                        name="password"
                        type="password"
                        placeholder="Confirm with your password"
                        class="field-input"
                    />
                </div>
                @if ($errors->userDeletion->has('password'))
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="flex items-center justify-end gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center bg-red-600 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-red-700 transition"
                >
                    Confirm Permanent Deletion
                </button>
            </div>
        </form>
    </details>
</section>
