<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard · AutiSync</title>

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

        .tab{ display:flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; color:var(--ink-soft); cursor:pointer; }
        .tab.active{ background:var(--canvas); color:var(--ink); border:1px solid var(--line); }

        button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible { outline:2px solid var(--ink); outline-offset:2px; }
    </style>
</head>
<body class="min-h-screen">
@php
    $admin = auth()->user();
    $adminAvatarUrl = $admin?->profilePictureUrl();
    $practitioners = $practitioners ?? collect();
    $clinicians = $clinicians ?? $practitioners->where('role', 'clinician')->values();
    $children = $children ?? collect();
    $totalUsersCount = $totalUsersCount ?? $practitioners->count();
    $clinicianCount = $clinicianCount ?? $clinicians->count();
    $caregiverCount = $caregiverCount ?? $practitioners->where('role', 'caregiver')->count();
    $adminCount = $adminCount ?? $practitioners->where('role', 'administrator')->count();
    $childrenCount = $childrenCount ?? 0;
    $assignedChildrenCount = $assignedChildrenCount ?? 0;
    $unassignedChildrenCount = $unassignedChildrenCount ?? 0;
    $observationsCount = $observationsCount ?? 0;
    $assessedObservationsCount = $assessedObservationsCount ?? 0;
    $totalPredictionsCount = $totalPredictionsCount ?? 0;
    $validatedPredictionsCount = $validatedPredictionsCount ?? 0;
    $pendingPredictionsCount = $pendingPredictionsCount ?? 0;
    $therapyGoalsCount = $therapyGoalsCount ?? 0;
    $milestonesCount = $milestonesCount ?? 0;
    $recommendationsCount = $recommendationsCount ?? 0;
    $mlHealth = $mlHealth ?? [
        'reachable' => false,
        'status_label' => 'Offline / Unreachable',
        'model_loaded' => false,
        'artifact_exists' => false,
        'artifact_name' => 'child_intervention_pipeline.joblib',
        'artifact_size_kb' => null,
        'summary' => 'Prediction service status unavailable.',
    ];
@endphp

<div class="flex min-h-screen">

    {{-- ============================= SIDEBAR ============================= --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-[var(--line)] h-screen sticky top-0 overflow-y-auto scrollbar-thin">
        <div class="flex items-center gap-2 px-5 h-16 border-b border-[var(--line)]">
            <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">A</div>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-tight">AutiSync</p>
                <p class="text-[10px] tracking-[.12em] text-[var(--ink-soft)] font-semibold">ADMIN GOVERNANCE</p>
            </div>
        </div>

        <nav class="px-3 py-5 space-y-6 flex-1">
            <div>
                <p class="eyebrow px-3 mb-2 uppercase">System</p>
                <div class="space-y-1">
                    <a href="{{ route('administrator.dashboard') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        Admin Control
                    </a>
                    <a href="#user-onboarding-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M18 8v6M15 11h6"/></svg>
                        Onboard New User
                    </a>
                    <a href="#practitioners-directory" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M17 11a3 3 0 100-6 3 3 0 000 6z"/></svg>
                        Users &amp; Roles ({{ $totalUsersCount }})
                    </a>
                    <a href="#caseload-governance-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Child &amp; Caseload Oversight ({{ $childrenCount }})
                    </a>
                    <a href="#system-health-panel" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h4l2-7 4 14 2-7h6"/></svg>
                        Service &amp; Model Health
                    </a>
                </div>
            </div>

            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Account Settings</p>
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        Profile &amp; Picture
                    </a>
                </div>
            </div>
        </nav>

        <div class="p-4 border-t border-[var(--line)] bg-[var(--canvas)]/60">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 shrink-0 rounded-full bg-white border border-[var(--line)] flex items-center justify-center text-xs font-bold overflow-hidden">
                    @if ($adminAvatarUrl)
                        <img src="{{ $adminAvatarUrl }}" alt="{{ $admin?->name }}" class="w-full h-full object-cover">
                    @else
                        {{ $admin?->initials() ?? 'AD' }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold truncate">{{ $admin?->name ?? 'Administrator' }}</p>
                    <p class="text-[11px] text-[var(--ink-soft)] truncate">Platform Administrator</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ============================= MAIN ============================= --}}
    <div class="flex-1 min-w-0">

        {{-- Topbar --}}
        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white sticky top-0 z-10">
            <p class="hidden sm:block font-semibold text-[15px]">Platform &amp; User Governance</p>

            <div class="flex-1 max-w-md ml-0 sm:ml-6">
                <div class="relative flex items-center">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)] pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input
                        type="text"
                        id="admin-user-search"
                        oninput="filterAdminUsers(this.value)"
                        placeholder="Search users by name, email, or role..."
                        class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-3 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:bg-white focus:outline-none"
                    >
                </div>
            </div>

            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 hover:opacity-90 transition" title="Profile Settings">
                    <div class="w-8 h-8 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold overflow-hidden">
                        @if ($adminAvatarUrl)
                            <img src="{{ $adminAvatarUrl }}" alt="{{ $admin?->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $admin?->initials() ?? 'AD' }}
                        @endif
                    </div>

                    <div class="hidden sm:block leading-tight">
                        <p class="text-[13px] font-semibold">{{ $admin?->name }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">{{ ucfirst($admin?->role ?? 'administrator') }}</p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="px-4 sm:px-8 py-6 space-y-6">

            {{-- Flash & Validation Alerts --}}
            @if (session('success') || session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 flex items-center gap-2.5" role="status">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') ?? session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    <p class="font-semibold">Please check the following:</p>
                    <ul class="mt-1.5 list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ===================== HERO / GOVERNANCE BANNER ===================== --}}
            <section class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold tracking-wide bg-[var(--canvas)] border border-[var(--line)] px-3 py-1 rounded-full mb-3">
                        <span class="w-2 h-2 rounded-full {{ $mlHealth['reachable'] ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        Administrator Governance &amp; Platform Oversight
                    </span>
                    <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight mb-2">
                        System Health &amp; Governance
                    </h1>
                    <p class="text-[var(--ink-soft)] text-sm leading-relaxed">
                        Welcome to the AutiSync Analytics administrative control center. Onboard specialist clinicians and caregivers, manage role-based privileges with strict safeguards, assign clinical caseloads, and monitor live FastAPI and PostgreSQL status.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="#user-onboarding-section" class="inline-flex items-center gap-2 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M18 8v6M15 11h6"/></svg>
                        Onboard New User
                    </a>
                    <a href="#caseload-governance-section" class="inline-flex items-center gap-2 border border-[var(--accent-soft)] bg-white text-[var(--accent)] text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[var(--accent-tint)] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6"/></svg>
                        Child &amp; Caseload Oversight ({{ $childrenCount }})
                    </a>
                </div>
            </section>

            {{-- ===================== 4 LIVE POSTGRESQL KPI STAT CARDS ===================== --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {{-- Card 1: Total Registered Users by Role --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="eyebrow uppercase">Total Active Users</span>
                        <div class="w-8 h-8 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--ink)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M17 11a3 3 0 100-6 3 3 0 000 6z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight">{{ $totalUsersCount }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">
                        <strong>{{ $caregiverCount }}</strong> {{ Str::plural('caregiver', $caregiverCount) }} · <strong>{{ $clinicianCount }}</strong> {{ Str::plural('clinician', $clinicianCount) }} · <strong>{{ $adminCount }}</strong> {{ Str::plural('admin', $adminCount) }}
                    </p>
                </div>

                {{-- Card 2: Registered Children & Caseload Coverage --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="eyebrow uppercase">Registered Children</span>
                        <div class="w-8 h-8 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center text-[var(--accent)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight">{{ $childrenCount }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">
                        <strong>{{ $assignedChildrenCount }}</strong> assigned to clinicians · <strong>{{ $unassignedChildrenCount }}</strong> unassigned
                    </p>
                </div>

                {{-- Card 3: Behavioural Observations & Specialist Assessments --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="eyebrow uppercase">Logged Observations</span>
                        <div class="w-8 h-8 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--ink)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight">{{ $observationsCount }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">
                        <strong>{{ $assessedObservationsCount }}</strong> with specialist 14-feature assessment inputs
                    </p>
                </div>

                {{-- Card 4: Developmental Indicators & Clinical Reviews --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="eyebrow uppercase">Prediction Reviews</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight">{{ $totalPredictionsCount }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">
                        <strong class="text-emerald-700">{{ $validatedPredictionsCount }}</strong> validated · <strong class="text-[var(--accent)]">{{ $pendingPredictionsCount }}</strong> awaiting clinician review
                    </p>
                </div>
            </section>

            {{-- ===================== LIVE SYSTEM & ML SERVICE HEALTH PANEL ===================== --}}
            <section id="system-health-panel" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 pb-4 border-b border-[var(--line)]">
                    <div>
                        <p class="eyebrow uppercase mb-0.5">Live Infrastructure &amp; Decision-Support Status</p>
                        <h2 class="text-lg font-bold tracking-tight">System &amp; Service Health Panel</h2>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Live reachability check for the FastAPI ML prediction microservice (<code class="font-semibold text-[var(--ink)]">GET /health</code>) and verified scikit-learn pipeline artifact status.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full {{ $mlHealth['reachable'] && $mlHealth['model_loaded'] ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                        <span class="w-2 h-2 rounded-full {{ $mlHealth['reachable'] && $mlHealth['model_loaded'] ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ $mlHealth['status_label'] }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- 1. FastAPI Service Reachability --}}
                    <div class="rounded-xl border border-[var(--line)] bg-[var(--canvas)]/60 p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[var(--ink)]">FastAPI Service (<code class="text-[11px]">GET /health</code>)</span>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $mlHealth['reachable'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[var(--accent-tint)] text-[var(--accent)] border border-[var(--accent-soft)]' }}">
                                {{ $mlHealth['reachable'] ? 'Reachable' : 'Offline' }}
                            </span>
                        </div>
                        <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                            {{ $mlHealth['summary'] }}
                        </p>
                        <p class="text-[11px] text-[var(--ink-soft)]">
                            Runtime Model Loaded: <strong>{{ $mlHealth['model_loaded'] ? 'Yes (Ready for /predict)' : 'No' }}</strong>
                        </p>
                    </div>

                    {{-- 2. Serialized Model Artifact Status --}}
                    <div class="rounded-xl border border-[var(--line)] bg-[var(--canvas)]/60 p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[var(--ink)]">Trained Pipeline Artifact</span>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $mlHealth['artifact_exists'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                {{ $mlHealth['artifact_exists'] ? 'Verified on Disk' : 'Missing' }}
                            </span>
                        </div>
                        <p class="text-xs text-[var(--ink-soft)]">
                            Artifact: <code class="font-semibold text-[var(--ink)]">{{ $mlHealth['artifact_name'] }}</code>
                            @if ($mlHealth['artifact_size_kb'])
                                ({{ number_format((float) $mlHealth['artifact_size_kb'], 1) }} KB)
                            @endif
                        </p>
                        <p class="text-[11px] text-[var(--ink-soft)]">
                            Contract: <strong>14 verified specialist &amp; developmental features</strong> (Non-diagnostic decision support).
                        </p>
                    </div>

                    {{-- 3. Platform Intervention & Continuity Activity --}}
                    <div class="rounded-xl border border-[var(--line)] bg-[var(--canvas)]/60 p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[var(--ink)]">Clinical &amp; Home Continuity</span>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-white border border-[var(--line)] text-[var(--ink)]">
                                PostgreSQL Live
                            </span>
                        </div>
                        <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                            Active SMART Therapy Goals: <strong>{{ $therapyGoalsCount }}</strong> · Recorded Milestones: <strong>{{ $milestonesCount }}</strong>
                        </p>
                        <p class="text-[11px] text-[var(--ink-soft)]">
                            Caregiver Support Guides Generated: <strong>{{ $recommendationsCount }}</strong>
                        </p>
                    </div>
                </div>
            </section>

            {{-- ===================== USER ONBOARDING SECTION ===================== --}}
            <section id="user-onboarding-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                <div class="mb-4 pb-4 border-b border-[var(--line)]">
                    <p class="eyebrow uppercase mb-0.5">Practitioner &amp; Caregiver Onboarding</p>
                    <h2 class="text-lg font-bold tracking-tight">Onboard New Platform User</h2>
                    <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                        Create verified accounts for Specialist Clinicians, Caregivers, or Administrators directly from the governance portal.
                    </p>
                </div>

                <form method="POST" action="{{ route('administrator.users.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                    @csrf
                    <div>
                        <label for="onboard_name" class="block text-xs font-semibold mb-1">Full Name</label>
                        <input
                            type="text"
                            id="onboard_name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="e.g. Dr. Amina Wanjiku"
                            class="w-full rounded-lg border border-[var(--line)] bg-[var(--canvas)] px-3 py-2 text-xs text-[var(--ink)] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="onboard_email" class="block text-xs font-semibold mb-1">Email Address</label>
                        <input
                            type="email"
                            id="onboard_email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="clinician@autisync.org"
                            class="w-full rounded-lg border border-[var(--line)] bg-[var(--canvas)] px-3 py-2 text-xs text-[var(--ink)] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="onboard_role" class="block text-xs font-semibold mb-1">Assigned Role</label>
                        <select
                            id="onboard_role"
                            name="role"
                            required
                            class="w-full rounded-lg border border-[var(--line)] bg-[var(--canvas)] px-3 py-2 text-xs font-semibold text-[var(--ink)] focus:bg-white focus:outline-none"
                        >
                            <option value="clinician" {{ old('role', 'clinician') === 'clinician' ? 'selected' : '' }}>Clinician</option>
                            <option value="caregiver" {{ old('role') === 'caregiver' ? 'selected' : '' }}>Caregiver</option>
                            <option value="administrator" {{ old('role') === 'administrator' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>

                    <div>
                        <label for="onboard_password" class="block text-xs font-semibold mb-1">Password</label>
                        <input
                            type="password"
                            id="onboard_password"
                            name="password"
                            required
                            placeholder="Min 8 characters"
                            class="w-full rounded-lg border border-[var(--line)] bg-[var(--canvas)] px-3 py-2 text-xs text-[var(--ink)] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="onboard_password_confirmation" class="block text-xs font-semibold mb-1">Confirm Password</label>
                        <input
                            type="password"
                            id="onboard_password_confirmation"
                            name="password_confirmation"
                            required
                            placeholder="Repeat password"
                            class="w-full rounded-lg border border-[var(--line)] bg-[var(--canvas)] px-3 py-2 text-xs text-[var(--ink)] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-[var(--ink)] text-white text-xs font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition">
                            + Create User Account
                        </button>
                    </div>
                </form>
            </section>

            {{-- ===================== ROLE FILTER TABS & SEARCH ===================== --}}
            <section class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-1 bg-white border border-[var(--line)] rounded-xl p-1">
                    <button type="button" onclick="filterAdminRole('all')" id="role-tab-all" class="tab active">
                        All Users ({{ $totalUsersCount }})
                    </button>
                    <button type="button" onclick="filterAdminRole('clinician')" id="role-tab-clinician" class="tab">
                        Clinicians ({{ $clinicianCount }})
                    </button>
                    <button type="button" onclick="filterAdminRole('caregiver')" id="role-tab-caregiver" class="tab">
                        Caregivers ({{ $caregiverCount }})
                    </button>
                    <button type="button" onclick="filterAdminRole('administrator')" id="role-tab-administrator" class="tab">
                        Administrators ({{ $adminCount }})
                    </button>
                </div>
            </section>

            {{-- ===================== ACTIVE PRACTITIONERS & USERS TABLE (WITH ROLE GOVERNANCE) ===================== --}}
            <section id="practitioners-directory" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-lg font-bold tracking-tight">Active Practitioners &amp; Platform Users</h2>
                        <p class="text-[13px] text-[var(--ink-soft)]">
                            Search and filter registered accounts in real time, and manage user roles with built-in privilege safeguards.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-1">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-[12px] text-[var(--ink-soft)] font-semibold border-b border-[var(--line)]">
                                <th class="py-2.5 px-2 font-semibold">User</th>
                                <th class="py-2.5 px-2 font-semibold">Role</th>
                                <th class="py-2.5 px-2 font-semibold">Profiles / Caseload</th>
                                <th class="py-2.5 px-2 font-semibold">Status</th>
                                <th class="py-2.5 px-2 font-semibold">Last Activity</th>
                                <th class="py-2.5 px-2 font-semibold text-right">Role Governance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--line)]">
                            @forelse($practitioners as $practitioner)
                                @php
                                    $practitionerAvatarUrl = $practitioner->profilePictureUrl();
                                    $pRole = strtolower($practitioner->role ?? 'caregiver');
                                    $isSelf = (int) $practitioner->id === (int) auth()->id();
                                @endphp
                                <tr
                                    class="admin-user-row hover:bg-[var(--canvas)]/50 transition"
                                    data-role="{{ $pRole }}"
                                    data-search="{{ strtolower($practitioner->name . ' ' . $practitioner->email . ' ' . $pRole) }}"
                                >
                                    <td class="py-3 px-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 shrink-0 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold overflow-hidden">
                                                @if ($practitionerAvatarUrl)
                                                    <img src="{{ $practitionerAvatarUrl }}" alt="{{ $practitioner->name }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ $practitioner->initials() }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-[13px]">{{ $practitioner->name }}</p>
                                                <p class="text-[11.5px] text-[var(--ink-soft)]">{{ $practitioner->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-2">
                                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full border {{ $pRole === 'clinician' ? 'bg-[var(--accent-tint)] text-[var(--accent)] border-[var(--accent-soft)]' : ($pRole === 'administrator' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-[var(--canvas)] text-[var(--ink)] border-[var(--line)]') }}">
                                            {{ ucfirst($pRole) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-xs text-[var(--ink-soft)]">
                                        @if ($pRole === 'clinician')
                                            <strong>{{ $practitioner->assigned_children_count ?? 0 }}</strong> assigned {{ Str::plural('child', $practitioner->assigned_children_count ?? 0) }}
                                        @elseif ($pRole === 'caregiver')
                                            <strong>{{ $practitioner->children_count ?? 0 }}</strong> child {{ Str::plural('profile', $practitioner->children_count ?? 0) }}
                                        @else
                                            System Governance
                                        @endif
                                    </td>
                                    <td class="py-3 px-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-xs text-[var(--ink-soft)]">
                                        {{ $practitioner->updated_at?->diffForHumans() ?? 'Recently' }}
                                    </td>
                                    <td class="py-3 px-2 text-right whitespace-nowrap">
                                        @if ($isSelf)
                                            <div class="inline-flex items-center gap-2">
                                                <span class="text-[11px] font-semibold text-[var(--ink-soft)] bg-[var(--canvas)] border border-[var(--line)] px-2.5 py-1 rounded-md">
                                                    Protected (Your Account)
                                                </span>
                                                <a href="{{ route('profile.edit') }}" class="text-xs font-semibold text-[var(--ink)] hover:underline">
                                                    Profile
                                                </a>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('administrator.users.update-role', $practitioner) }}" class="inline-flex items-center justify-end gap-1.5">
                                                @csrf
                                                @method('PATCH')
                                                <select
                                                    name="role"
                                                    aria-label="Update role for {{ $practitioner->name }}"
                                                    class="rounded-md border border-[var(--line)] bg-[var(--canvas)] px-2.5 py-1 text-xs font-semibold text-[var(--ink)] focus:bg-white focus:outline-none"
                                                >
                                                    <option value="caregiver" {{ $pRole === 'caregiver' ? 'selected' : '' }}>Caregiver</option>
                                                    <option value="clinician" {{ $pRole === 'clinician' ? 'selected' : '' }}>Clinician</option>
                                                    <option value="administrator" {{ $pRole === 'administrator' ? 'selected' : '' }}>Administrator</option>
                                                </select>
                                                <button
                                                    type="submit"
                                                    class="rounded-md border border-[var(--ink)] bg-white text-[var(--ink)] hover:bg-[var(--ink)] hover:text-white text-xs font-semibold px-2.5 py-1 transition"
                                                >
                                                    Update Role
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <p class="text-sm font-semibold mb-1">No registered users yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between mt-5 pt-4 border-t border-[var(--line)]">
                    <p class="text-[12.5px] text-[var(--ink-soft)]">
                        Showing {{ $practitioners->count() }} of {{ $totalUsersCount }} registered users
                    </p>
                </div>
            </section>

            {{-- ===================== CLINICIAN-CHILD CASELOAD & PRIVACY-SAFE DATA OVERSIGHT ===================== --}}
            <section id="caseload-governance-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5 pb-4 border-b border-[var(--line)]">
                    <div>
                        <p class="eyebrow uppercase mb-0.5">Clinical Caseload &amp; Privacy-Safe Data Oversight</p>
                        <h2 class="text-lg font-bold tracking-tight">Child &amp; Caseload Oversight ({{ $children->count() }})</h2>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Assign or unassign specialist clinicians to registered children and monitor record completeness (observation counts, 14-domain assessment readiness, and progress indicator reviews).
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50/70 px-3.5 py-2 text-xs text-emerald-900 shrink-0">
                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        <span><strong>Privacy-Safe Governance:</strong> Private clinical notes, caregiver home reflections, and raw assessment scores are hidden from administrators.</span>
                    </div>
                </div>

                @if ($children->isNotEmpty())
                    <div class="overflow-x-auto -mx-1">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-[var(--ink-soft)] font-semibold border-b border-[var(--line)]">
                                    <th class="py-2.5 px-2">Child Profile</th>
                                    <th class="py-2.5 px-2">Caregiver</th>
                                    <th class="py-2.5 px-2">Record Completeness (Privacy-Safe)</th>
                                    <th class="py-2.5 px-2">Assigned Clinicians</th>
                                    <th class="py-2.5 px-2 text-right">Assign Specialist Clinician</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--line)]">
                                @foreach ($children as $childItem)
                                    @php
                                        $childPic = $childItem->profilePictureUrl();
                                        $obsTotal = (int) ($childItem->behavioural_records_count ?? 0);
                                        $assessedCount = (int) ($childItem->assessed_records_count ?? 0);
                                        $predTotal = (int) ($childItem->predictions_count ?? 0);
                                        $validatedPredCount = (int) ($childItem->validated_predictions_count ?? 0);
                                        $goalsTotal = (int) ($childItem->therapy_goals_count ?? 0);
                                        $milestonesTotal = (int) ($childItem->milestones_count ?? 0);
                                        $isAssessmentReady = $assessedCount > 0;
                                    @endphp
                                    <tr class="hover:bg-[var(--canvas)]/50 transition">
                                        <td class="py-3 px-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 shrink-0 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center overflow-hidden text-xs font-bold text-[var(--accent)]">
                                                    @if ($childPic)
                                                        <img src="{{ $childPic }}" alt="{{ $childItem->first_name }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ $childItem->initials() }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-bold text-xs text-[var(--ink)]">{{ $childItem->first_name }}</p>
                                                    <p class="text-[11px] text-[var(--ink-soft)]">{{ $childItem->age_in_months }} mos · {{ $childItem->sex }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-3 px-2 text-xs">
                                            <p class="font-semibold">{{ $childItem->caregiver->name ?? 'Caregiver' }}</p>
                                            <p class="text-[11px] text-[var(--ink-soft)]">{{ $childItem->caregiver->email ?? '' }}</p>
                                        </td>

                                        <td class="py-3 px-2">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)]">
                                                    <strong>{{ $obsTotal }}</strong> {{ Str::plural('Observation', $obsTotal) }}
                                                </span>

                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md border {{ $isAssessmentReady ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                                    @if ($isAssessmentReady)
                                                        Assessment Ready ({{ $assessedCount }})
                                                    @else
                                                        Assessment Pending
                                                    @endif
                                                </span>

                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md border border-[var(--line)] bg-white text-[var(--ink-soft)]">
                                                    <strong>{{ $predTotal }}</strong> {{ Str::plural('Indicator', $predTotal) }} ({{ $validatedPredCount }} verified)
                                                </span>
                                            </div>
                                            <p class="text-[10.5px] text-[var(--ink-soft)] mt-1">
                                                Therapy Goals: <strong>{{ $goalsTotal }}</strong> · Milestones: <strong>{{ $milestonesTotal }}</strong> · <span class="italic">Clinical notes &amp; raw scores restricted</span>
                                            </p>
                                        </td>

                                        <td class="py-3 px-2">
                                            @if ($childItem->clinicians->isNotEmpty())
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    @foreach ($childItem->clinicians as $assignedClinician)
                                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                                            <span>{{ $assignedClinician->name }}</span>
                                                            <form method="POST" action="{{ route('administrator.children.unassign-clinician', [$childItem, $assignedClinician]) }}" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" title="Remove {{ $assignedClinician->name }}" class="text-emerald-700 hover:text-red-600 font-bold leading-none">
                                                                    ✕
                                                                </button>
                                                            </form>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                                    Unassigned
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-3 px-2 text-right whitespace-nowrap">
                                            @if ($clinicians->isNotEmpty())
                                                <form method="POST" action="{{ route('administrator.children.assign-clinician', $childItem) }}" class="inline-flex items-center justify-end gap-1.5">
                                                    @csrf
                                                    <select
                                                        name="clinician_id"
                                                        required
                                                        class="rounded-md border border-[var(--line)] bg-[var(--canvas)] px-2.5 py-1 text-xs font-semibold text-[var(--ink)] focus:bg-white focus:outline-none"
                                                    >
                                                        <option value="">Select clinician...</option>
                                                        @foreach ($clinicians as $clinOption)
                                                            <option value="{{ $clinOption->id }}">{{ $clinOption->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button
                                                        type="submit"
                                                        class="rounded-md bg-[var(--ink)] text-white text-xs font-semibold px-3 py-1 hover:opacity-90 transition"
                                                    >
                                                        Assign
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-[var(--ink-soft)] italic">Onboard a clinician above first</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="border border-dashed border-[var(--line)] rounded-xl p-8 text-center">
                        <p class="text-sm font-semibold">No child profiles registered yet</p>
                    </div>
                @endif
            </section>

        </main>
    </div>
</div>

<script>
    let currentAdminRoleFilter = 'all';

    function filterAdminRole(role) {
        currentAdminRoleFilter = role;
        ['all', 'clinician', 'caregiver', 'administrator'].forEach(function (r) {
            const btn = document.getElementById('role-tab-' + r);
            if (!btn) return;
            btn.className = r === role ? 'tab active' : 'tab';
        });
        filterAdminUsers(document.getElementById('admin-user-search')?.value || '');
    }

    function filterAdminUsers(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.admin-user-row').forEach(function (row) {
            const rowRole = row.getAttribute('data-role') || '';
            const rowSearch = row.getAttribute('data-search') || '';
            const matchesRole = currentAdminRoleFilter === 'all' || rowRole === currentAdminRoleFilter;
            const matchesQuery = !q || rowSearch.indexOf(q) > -1;
            row.classList.toggle('hidden', !(matchesRole && matchesQuery));
        });
    }
</script>
</body>
</html>
