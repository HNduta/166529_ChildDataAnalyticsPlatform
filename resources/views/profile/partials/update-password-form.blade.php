<section>
    <header class="pb-5 border-b border-[var(--line)]">
        <p class="eyebrow uppercase mb-1">Account Security</p>
        <h2 class="text-lg font-extrabold tracking-tight text-[var(--ink)]">
            Update Password
        </h2>
        <p class="mt-1 text-xs text-[var(--ink-soft)] leading-relaxed">
            Use a strong, unique password to keep your account and child records protected.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div>
            <label for="update_password_current_password" class="field-label">Current Password</label>
            <div class="relative">
                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="4" y="11" width="16" height="9" rx="2"/>
                    <path d="M8 11V7a4 4 0 118 0v4"/>
                </svg>
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="Enter current password"
                    class="field-input"
                />
            </div>
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        {{-- New Password --}}
        <div>
            <label for="update_password_password" class="field-label">New Password</label>
            <div class="relative">
                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="4" y="11" width="16" height="9" rx="2"/>
                    <path d="M8 11V7a4 4 0 118 0v4"/>
                </svg>
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Create a new password"
                    class="field-input"
                />
            </div>
            @if ($errors->updatePassword->has('password'))
                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        {{-- Confirm New Password --}}
        <div>
            <label for="update_password_password_confirmation" class="field-label">Confirm New Password</label>
            <div class="relative">
                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="4" y="11" width="16" height="9" rx="2"/>
                    <path d="M8 11V7a4 4 0 118 0v4"/>
                </svg>
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Re-enter new password"
                    class="field-input"
                />
            </div>
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="pt-1 flex items-center gap-3">
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition"
            >
                Update Password
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-xs font-semibold text-emerald-700">
                    Password updated!
                </span>
            @endif
        </div>
    </form>
</section>
