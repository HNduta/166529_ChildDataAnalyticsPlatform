<x-guest-layout>

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        {{-- ===================== LEFT: BRAND PANEL ===================== --}}
        <div class="hidden lg:flex flex-col px-14 py-12 border-r border-[var(--line)]">

            {{-- AutiSync Brand --}}
            <div class="flex items-center gap-2 mb-10">
                <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">
                    A
                </div>

                <div class="leading-tight">
                    <span class="text-[15px] font-bold tracking-tight">
                        AutiSync
                    </span>

                    <span class="text-[10px] tracking-[.12em] text-[var(--ink-soft)] font-semibold ml-1.5 align-middle">
                        ANALYTICS
                    </span>
                </div>
            </div>

            {{-- Introduction --}}
            <h1 class="text-[34px] leading-[1.15] font-extrabold tracking-tight max-w-md mb-4">
                Advanced Insights for Developmental Growth.
            </h1>

            <p class="text-[var(--ink-soft)] text-sm max-w-sm mb-8">
                Empowering caregivers and clinicians with AI-based behavioural
                analytics to support children on the Autism Spectrum.
                Professional, secure, and compassionate.
            </p>

            {{-- Key Features --}}
            <ul class="space-y-3 mb-10">

                <li class="flex items-center gap-2.5 text-sm font-medium">
                    <svg class="w-4 h-4 shrink-0 text-[var(--ink)]"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24"
                         aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>

                    Secure Data Handling &amp; Privacy
                </li>

                <li class="flex items-center gap-2.5 text-sm font-medium">
                    <svg class="w-4 h-4 shrink-0 text-[var(--ink)]"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24"
                         aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>

                    Evidence-Based AI Insights
                </li>

                <li class="flex items-center gap-2.5 text-sm font-medium">
                    <svg class="w-4 h-4 shrink-0 text-[var(--ink)]"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24"
                         aria-hidden="true">
                        <path d="M9 3a4 4 0 014 4v10a4 4 0 01-4 4"/>
                        <path d="M15 3a4 4 0 00-4 4v10a4 4 0 004 4"/>
                        <path d="M9 8h6M9 16h6"/>
                    </svg>

                    Collaborative Care Ecosystem
                </li>

            </ul>

            {{-- Decorative Illustration Placeholder --}}
            <div class="flex-1 min-h-[220px] rounded-2xl skel"></div>

            {{-- Footer --}}
            <p class="text-[12px] text-[var(--ink-soft)] mt-8">
                &copy; {{ date('Y') }} AutiSync Analytics.
                All rights reserved.
            </p>

        </div>


        {{-- ===================== RIGHT: AUTH PANEL ===================== --}}
        <div class="flex items-center justify-center px-6 py-12">

            <div class="w-full max-w-sm">

                {{-- Mobile Brand --}}
                <div class="lg:hidden flex items-center gap-2 mb-8">

                    <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">
                        A
                    </div>

                    <span class="text-[15px] font-bold tracking-tight">
                        AutiSync
                    </span>

                </div>


                {{-- Heading --}}
                <h2 class="text-2xl font-extrabold tracking-tight mb-1.5">
                    Create Account
                </h2>

                <p class="text-sm text-[var(--ink-soft)] mb-6">
                    Set up caregiver access to start logging observations and tracking progress.
                </p>


                {{-- Login / Create Account Switch --}}
                <div class="grid grid-cols-2 gap-2 mb-6">

                    <a
                        href="{{ route('login') }}"
                        class="text-center text-sm font-semibold rounded-lg py-2.5 border border-[var(--line)] text-[var(--ink-soft)] hover:text-[var(--ink)]">
                        Login
                    </a>

                    <span
                        class="text-center text-sm font-semibold rounded-lg py-2.5 bg-[var(--ink)] text-white">
                        Create Account
                    </span>

                </div>


                {{-- General Validation Errors --}}
                @if ($errors->any())
                    <div
                        class="mb-4 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2"
                        role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif


                {{-- ===================== REGISTER FORM ===================== --}}
                <form method="POST"
                      action="{{ route('register') }}"
                      class="space-y-5">

                    @csrf


                    {{-- Full Name --}}
                    <div>

                        <label
                            for="name"
                            class="block text-[13px] font-semibold mb-1.5">
                            Full Name
                        </label>

                        <div class="relative">

                            <svg
                                class="w-4 h-4 field-icon"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                            </svg>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Enter your full name"
                                class="field-input"
                            >

                        </div>

                        @error('name')
                            <p class="text-xs text-red-600 mt-1.5">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div>

                        <label
                            for="email"
                            class="block text-[13px] font-semibold mb-1.5">
                            Email Address
                        </label>

                        <div class="relative">

                            <svg
                                class="w-4 h-4 field-icon"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="M3 7l9 6 9-6"/>
                            </svg>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="Enter your email address"
                                class="field-input"
                            >

                        </div>

                        @error('email')
                            <p class="text-xs text-red-600 mt-1.5">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Password --}}
                    <div>

                        <label
                            for="password"
                            class="block text-[13px] font-semibold mb-1.5">
                            Password
                        </label>

                        <div class="relative">

                            <svg
                                class="w-4 h-4 field-icon"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true">
                                <rect x="4" y="11" width="16" height="9" rx="2"/>
                                <path d="M8 11V7a4 4 0 118 0v4"/>
                            </svg>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Create a password"
                                class="field-input pr-10"
                            >

                            {{-- Show / Hide Password --}}
                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)] hover:text-[var(--ink)]"
                                aria-label="Show password">

                                <svg
                                    id="eyeIconPassword"
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true">

                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                    <circle cx="12" cy="12" r="3"/>

                                </svg>

                            </button>

                        </div>

                        @error('password')
                            <p class="text-xs text-red-600 mt-1.5">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Confirm Password --}}
                    <div>

                        <label
                            for="password_confirmation"
                            class="block text-[13px] font-semibold mb-1.5">
                            Confirm Password
                        </label>

                        <div class="relative">

                            <svg
                                class="w-4 h-4 field-icon"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true">
                                <rect x="4" y="11" width="16" height="9" rx="2"/>
                                <path d="M8 11V7a4 4 0 118 0v4"/>
                            </svg>

                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Re-enter your password"
                                class="field-input pr-10"
                            >

                            {{-- Show / Hide Confirm Password --}}
                            <button
                                type="button"
                                id="togglePasswordConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)] hover:text-[var(--ink)]"
                                aria-label="Show password">

                                <svg
                                    id="eyeIconPasswordConfirm"
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true">

                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                    <circle cx="12" cy="12" r="3"/>

                                </svg>

                            </button>

                        </div>

                        @error('password_confirmation')
                            <p class="text-xs text-red-600 mt-1.5">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{--
                        Account type — informational only, not a selectable field.
                        No input is submitted for this; the backend keeps assigning
                        role = 'caregiver' to every self-registered account, exactly
                        as the existing RegisteredUserController already does.
                        Clinician / Admin accounts are provisioned separately
                        (e.g. via the Administrator "Add New User" / invite flow),
                        never through public self-registration.
                    --}}
                    <div class="flex items-center justify-between bg-[var(--canvas)] border border-[var(--line)] rounded-lg px-3.5 py-3">

                        <div>
                            <p class="text-[13px] font-semibold">
                                Account Type
                            </p>

                            <p class="text-[12px] text-[var(--ink-soft)]">
                                Assigned automatically for new sign-ups
                            </p>
                        </div>

                        <span class="text-[12px] font-semibold bg-white border border-[var(--line)] px-3 py-1 rounded-full">
                            Caregiver
                        </span>

                    </div>


                    {{-- Register Button --}}
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold py-3 rounded-lg hover:opacity-90 transition">

                        Create Account

                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>

                    </button>

                </form>


                {{-- Login Link --}}
                <p class="text-center text-[13px] text-[var(--ink-soft)] mt-8">

                    Already have an account?

                    <a
                        href="{{ route('login') }}"
                        class="font-semibold text-[var(--ink)] hover:underline">
                        Log in
                    </a>

                </p>

            </div>

        </div>

    </div>


    {{-- ===================== PASSWORD VISIBILITY ===================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const bindToggle = (inputId, buttonId) => {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);

                if (!input || !button) {
                    return;
                }

                button.addEventListener('click', function () {
                    const isPassword = input.type === 'password';

                    input.type = isPassword ? 'text' : 'password';

                    button.setAttribute(
                        'aria-label',
                        isPassword ? 'Hide password' : 'Show password'
                    );
                });
            };

            bindToggle('password', 'togglePassword');
            bindToggle('password_confirmation', 'togglePasswordConfirm');

        });
    </script>

</x-guest-layout>