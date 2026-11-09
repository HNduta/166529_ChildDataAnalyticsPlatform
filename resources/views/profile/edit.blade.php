<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings · AutiSync</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root{
            --ink:#12735A;
            --ink-soft:#6f6a62;
            --line:#e6dfd4;
            --surface:#ffffff;
            --canvas:#faf6f0;
            --accent:#e8845c;
            --accent-soft:#f4b79c;
            --accent-tint:#fdf1ea;
        }
        html,body{ font-family:'Inter',ui-sans-serif,system-ui,sans-serif; background:var(--canvas); color:var(--ink); }
        .eyebrow{ font-size:11px; letter-spacing:.06em; color:var(--ink-soft); font-weight:600; }
        .scrollbar-thin::-webkit-scrollbar{ width:6px; }
        .scrollbar-thin::-webkit-scrollbar-thumb{ background:#e6dfd4; border-radius:999px; }

        .navlink{ display:flex; align-items:center; gap:10px; padding:8px 12px; border-radius:8px; font-size:14px; color:#5b574f; font-weight:500; }
        .navlink:hover{ background:#f3ede3; color:var(--ink); }
        .navlink.active{ background:var(--ink); color:#fff; }

        button:focus-visible, a:focus-visible, input:focus-visible { outline:2px solid var(--ink); outline-offset:2px; }

        .field-label{ display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:var(--ink); }
        .field-input{
            width:100%; border:1px solid var(--line); border-radius:10px; padding:10px 12px 10px 38px;
            font-size:14px; background:#fff; color:var(--ink); outline:none;
            transition:border-color .2s ease, box-shadow .2s ease;
        }
        .field-input:focus{ border-color:var(--ink); box-shadow:0 0 0 2px rgba(18,115,90,.12); }
        .field-input::placeholder{ color:#a89f93; }
        .field-icon{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--ink-soft); pointer-events:none; }
    </style>
</head>
<body class="min-h-screen">
@php
    $user = $user ?? auth()->user();
    $role = strtolower((string) ($user->role ?? 'caregiver'));
    $dashboardRoute = match ($role) {
        'clinician' => route('clinician.dashboard'),
        'administrator' => route('administrator.dashboard'),
        default => route('caregiver.dashboard'),
    };
    $userAvatarUrl = $user->profilePictureUrl();
@endphp

<div class="flex min-h-screen">

    {{-- ============================= SIDEBAR ============================= --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-[var(--line)] h-screen sticky top-0 overflow-y-auto scrollbar-thin">
        <div class="flex items-center gap-2 px-5 h-16 border-b border-[var(--line)]">
            <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">A</div>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-tight">AutiSync</p>
                <p class="text-[10px] tracking-[.12em] text-[var(--ink-soft)] font-semibold">ANALYTICS</p>
            </div>
        </div>

        <nav class="px-3 py-5 space-y-6 flex-1">
            @if ($role === 'clinician')
                <div>
                    <p class="eyebrow px-3 mb-2 uppercase">Clinical Workspace</p>
                    <div class="space-y-1">
                        <a href="{{ route('clinician.dashboard') }}" class="navlink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                            Clinical Dashboard
                        </a>
                    </div>
                </div>
            @elseif ($role === 'administrator')
                <div>
                    <p class="eyebrow px-3 mb-2 uppercase">Administration</p>
                    <div class="space-y-1">
                        <a href="{{ route('administrator.dashboard') }}" class="navlink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                            Admin Dashboard
                        </a>
                    </div>
                </div>
            @else
                <div>
                    <p class="eyebrow px-3 mb-2 uppercase">Caregiver</p>
                    <div class="space-y-1">
                        <a href="{{ route('caregiver.dashboard') }}" class="navlink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('children.index') }}" class="navlink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                            Child Profile
                        </a>
                    </div>
                </div>

                <div>
                    <p class="eyebrow px-3 mb-2 uppercase">Behavior</p>
                    <div class="space-y-1">
                        <a href="{{ route('behavioural-records.create') }}" class="navlink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                            Observation Form
                        </a>
                        <a href="{{ route('behavioural-records.index') }}" class="navlink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19V6M10 19V10M16 19V4M22 19V13"/></svg>
                            Records
                        </a>
                    </div>
                </div>

                <div>
                    <p class="eyebrow px-3 mb-2 uppercase">Support</p>
                    <div class="space-y-1">
                        <a href="{{ route('caregiver.recommendations') }}" class="navlink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 10-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/></svg>
                            Recommendations
                        </a>
                    </div>
                </div>
            @endif

            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Account Settings</p>
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        Profile &amp; Picture
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    {{-- ============================= MAIN ============================= --}}
    <div class="flex-1 min-w-0">

        {{-- Topbar --}}
        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <a href="{{ $dashboardRoute }}" class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap">
                    ← Back to Dashboard
                </a>
                <span class="text-[var(--line)]">|</span>
                <p class="font-semibold text-[15px]">Account &amp; Profile Settings</p>
            </div>

            <div class="ml-auto flex items-center gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold overflow-hidden">
                        @if ($userAvatarUrl)
                            <img src="{{ $userAvatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $user->initials() }}
                        @endif
                    </div>

                    <div class="hidden sm:block leading-tight">
                        <p class="text-[13px] font-semibold">{{ $user->name }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">{{ ucfirst($user->role ?? 'caregiver') }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="px-4 sm:px-8 py-8">
            <div class="max-w-5xl mx-auto space-y-6">

                {{-- Flash Notification Banner --}}
                @if (session('status') === 'profile-updated')
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 flex items-center gap-2.5" role="status">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Your profile information and photo have been saved successfully.</span>
                    </div>
                @elseif (session('status') === 'password-updated')
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 flex items-center gap-2.5" role="status">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Your account password has been updated successfully.</span>
                    </div>
                @endif

                {{-- ===================== PROFILE OVERVIEW BANNER ===================== --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 shrink-0 rounded-2xl border-2 border-[var(--accent-soft)] bg-[var(--accent-tint)] flex items-center justify-center overflow-hidden text-xl font-extrabold text-[var(--accent)] shadow-sm">
                            @if ($userAvatarUrl)
                                <img src="{{ $userAvatarUrl }}" alt="{{ $user->name }} profile picture" class="w-full h-full object-cover">
                            @else
                                <span>{{ $user->initials() }}</span>
                            @endif
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-2xl sm:text-[26px] font-extrabold tracking-tight">{{ $user->name }}</h1>
                                <span class="text-[11px] font-bold uppercase tracking-wide bg-[var(--canvas)] border border-[var(--line)] text-[var(--ink)] px-2.5 py-0.5 rounded-full">
                                    {{ ucfirst($user->role ?? 'caregiver') }} Account
                                </span>
                            </div>
                            <p class="text-sm text-[var(--ink-soft)] mt-1">{{ $user->email }}</p>
                            <p class="text-xs text-[var(--ink-soft)] mt-1">
                                Manage your profile photo, personal contact details, and account security settings.
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0">
                        <a href="{{ $dashboardRoute }}" class="inline-flex items-center gap-1.5 border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)] text-xs font-semibold px-4 py-2.5 rounded-lg hover:bg-[#efe7dc] transition">
                            Return to Dashboard
                        </a>
                    </div>
                </section>

                {{-- ===================== TWO-COLUMN SETTINGS GRID ===================== --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    {{-- Left Column: Profile Information & Picture --}}
                    <div class="lg:col-span-7 bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    {{-- Right Column: Password & Account Deletion --}}
                    <div class="lg:col-span-5 space-y-6">
                        <div class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8">
                            @include('profile.partials.update-password-form')
                        </div>

                        <div class="bg-white border border-red-200 rounded-2xl p-6 sm:p-8">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>
</div>
</body>
</html>
