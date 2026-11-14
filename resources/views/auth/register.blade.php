<x-guest-layout>

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        {{-- ===================== LEFT: BRAND PANEL ===================== --}}
        <div class="hidden lg:flex flex-col px-12 xl:px-16 py-12 border-r border-[var(--line)] bg-[var(--canvas)]">

            {{-- AutiSync Brand --}}
            <div class="flex items-center gap-2.5 mb-10">
                <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">
                    A
                </div>

                <div class="leading-tight">
                    <span class="text-[15px] font-bold tracking-tight text-[var(--ink)]">
                        AutiSync
                    </span>

                    <span class="text-[10px] tracking-[.12em] text-[var(--ink-soft)] font-semibold ml-1.5 align-middle">
                        ANALYTICS
                    </span>
                </div>
            </div>

            {{-- Introduction (Expanded width to comfortably occupy horizontal canvas space) --}}
            <h1 class="text-[32px] xl:text-[36px] leading-[1.2] font-extrabold tracking-tight max-w-2xl mb-4 text-[var(--ink)]">
                Navigating our journey, together.
            </h1>

            <p class="text-[var(--ink-soft)] text-sm sm:text-[15px] leading-relaxed max-w-2xl mb-8">
                Join a secure, compassionate space where caregivers and specialists collaborate. Track daily milestones, uncover hidden patterns, and unlock personalized support for children on the Autism Spectrum.
            </p>

            {{-- Key Features (Clean Checkmark Design) --}}
            <ul class="space-y-3.5 mb-10">

                <li class="flex items-center gap-2.5 text-sm font-medium text-[var(--ink)]">
                    <svg class="w-4 h-4 shrink-0 text-[var(--ink)]"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24"
                         aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>

                    Your Child’s Data, Kept Strictly Private
                </li>

                <li class="flex items-center gap-2.5 text-sm font-medium text-[var(--ink)]">
                    <svg class="w-4 h-4 shrink-0 text-[var(--ink)]"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24"
                         aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>

                    Smart, Personalized Daily Support
                </li>

                <li class="flex items-center gap-2.5 text-sm font-medium text-[var(--ink)]">
                    <svg class="w-4 h-4 shrink-0 text-[var(--ink)]"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24"
                         aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>

                    Seamless Caregiver &amp; Clinician Teamwork
                </li>

            </ul>

            {{-- User-Supplied Registration Image --}}
            @php
                $registerImageCandidates = [
                    'images/auth/register.png',
                    'images/auth/register.jpg',
                    'images/auth/register.jpeg',
                    'images/auth/register.webp',
                    'images/auth/register-illustration.png',
                    'images/auth/register-illustration.jpg',
                    'images/auth/register-illustration.jpeg',
                    'images/auth/register-illustration.webp',
                ];
                $registerImagePath = null;
                foreach ($registerImageCandidates as $candidate) {
                    if (is_file(public_path($candidate))) {
                        $registerImagePath = $candidate;
                        break;
                    }
                }
            @endphp

            <div class="flex-1 min-h-[240px] rounded-2xl overflow-hidden border border-[var(--line)] bg-[var(--canvas)] flex flex-col">
                @if ($registerImagePath)
                    <img
                        src="{{ asset($registerImagePath) }}"
                        alt="AutiSync Caregiver Registration"
                        class="w-full h-full object-cover flex-1"
                    >
                @else
                    <div id="register-image-placeholder" class="flex-1 p-6 flex flex-col items-center justify-center text-center border-2 border-dashed border-[var(--accent-soft)] rounded-2xl bg-[var(--accent-tint)]/50">
                        <div class="w-12 h-12 rounded-2xl bg-white border border-[var(--accent-soft)] flex items-center justify-center text-[var(--accent)] mb-3 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <circle cx="8.5" cy="9.5" r="1.5"/>
                                <path d="M21 15l-5-5L5 20"/>
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[var(--accent)] bg-white border border-[var(--accent-soft)] px-2.5 py-0.5 rounded-full mb-2">
                            Registration Image Placeholder
                        </span>
                        <p class="text-sm font-bold text-[var(--ink)] max-w-xs">
                            A welcoming space for caregivers to record everyday milestones and observations
                        </p>
                        <p class="text-xs text-[var(--ink-soft)] mt-1.5 max-w-xs leading-relaxed">
                            Place your custom registration page image at <code class="font-mono text-[11px] bg-white px-1.5 py-0.5 rounded border border-[var(--line)]">public/images/auth/register.jpg</code> (or <code class="font-mono text-[11px]">.png</code> / <code class="font-mono text-[11px]">.webp</code>) to display it here automatically.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <p class="text-[12px] text-[var(--ink-soft)] mt-8">
                &copy; {{ date('Y') }} AutiSync Analytics. All rights reserved.
            </p>

        </div>


        {{-- ===================== RIGHT: AUTH PANEL ===================== --}}
        <div class="flex items-center justify-center px-6 py-12">

            <div class="w-full max-w-sm">

                {{-- Mobile Brand & Visual Banner --}}
                <div class="lg:hidden mb-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">
                            A
                        </div>
                        <span class="text-[15px] font-bold tracking-tight">
                            AutiSync
                        </span>
                    </div>

                    @if ($registerImagePath)
                        <div class="h-36 rounded-2xl overflow-hidden border border-[var(--line)] bg-white">
                            <img src="{{ asset($registerImagePath) }}" alt="AutiSync Registration" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="p-3.5 rounded-xl border border-dashed border-[var(--accent-soft)] bg-[var(--accent-tint)]/50 text-xs text-[var(--ink-soft)] flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-[var(--accent-soft)] flex items-center justify-center text-[var(--accent)] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="M21 15l-5-5L5 20"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-[var(--ink)]">Registration Image Placeholder</p>
                                <p class="text-[11px]">Supply <code class="font-mono">public/images/auth/register.jpg</code></p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Heading --}}
                <h2 class="text-2xl sm:text-[26px] font-extrabold tracking-tight mb-2 text-[var(--ink)]">
                    Join AutiSync
                </h2>

                <p class="text-sm text-[var(--ink-soft)] leading-relaxed mb-6">
                    Create your account to start logging daily observations, tracking progress, or managing your clinical caseload.
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
                      enctype="multipart/form-data"
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


                    {{-- Optional Profile Picture Upload --}}
                    <div>
                        <label
                            for="profile_picture"
                            class="block text-[13px] font-semibold mb-1.5">
                            Profile Picture <span class="text-[var(--ink-soft)] font-normal">(Optional)</span>
                        </label>

                        <div class="flex items-center gap-3 bg-white border border-[var(--line)] rounded-lg p-2.5">
                            <div id="registerAvatarPreviewBox" class="w-10 h-10 shrink-0 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center overflow-hidden text-[var(--accent)]">
                                <svg id="registerAvatarIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4"/>
                                    <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                                </svg>
                                <img id="registerAvatarPreviewImg" src="" alt="Selected profile preview" class="hidden w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <input
                                    id="profile_picture"
                                    type="file"
                                    name="profile_picture"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    class="block w-full text-xs text-[var(--ink-soft)] file:mr-2.5 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[var(--canvas)] file:text-[var(--ink)] hover:file:bg-[#efe7dc] cursor-pointer"
                                >
                                <p class="text-[11px] text-[var(--ink-soft)] mt-1">
                                    JPG, PNG, or WEBP up to 2 MB.
                                </p>
                            </div>
                        </div>

                        @error('profile_picture')
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

            const fileInput = document.getElementById('profile_picture');
            const previewImg = document.getElementById('registerAvatarPreviewImg');
            const previewIcon = document.getElementById('registerAvatarIcon');

            if (fileInput && previewImg && previewIcon) {
                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files && e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (ev) {
                            previewImg.src = ev.target.result;
                            previewImg.classList.remove('hidden');
                            previewIcon.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

        });
    </script>

</x-guest-layout>