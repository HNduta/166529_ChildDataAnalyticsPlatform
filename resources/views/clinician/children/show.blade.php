<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->first_name }} · Clinical Monitoring · AutiSync</title>

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

        button:focus-visible, a:focus-visible, input:focus-visible, textarea:focus-visible, select:focus-visible { outline:2px solid var(--ink); outline-offset:2px; }
    </style>
</head>
<body class="min-h-screen">
@php
    $clinician = $clinician ?? auth()->user();
    $clinicianAvatarUrl = $clinician?->profilePictureUrl();
    $childAvatarUrl = $child->profilePictureUrl();

    // Helper closures for clinical domain score labels & severity/ability bars
    $abilityDescriptor = function (float $score): array {
        if ($score >= 7.0) {
            return ['label' => 'Strong Ability', 'bar' => 'bg-emerald-500', 'badge' => 'text-emerald-700 bg-emerald-50 border-emerald-200'];
        }
        if ($score >= 4.0) {
            return ['label' => 'Moderate / Developing', 'bar' => 'bg-amber-500', 'badge' => 'text-amber-800 bg-amber-50 border-amber-200'];
        }
        return ['label' => 'Needs Support', 'bar' => 'bg-[var(--accent)]', 'badge' => 'text-[var(--accent)] bg-[var(--accent-tint)] border-[var(--accent-soft)]'];
    };

    $repetitiveSeverityDescriptor = function (float $score): array {
        // For Repetitive Behaviour, lower score = milder severity; higher score = higher severity
        if ($score <= 3.5) {
            return ['label' => 'Mild Severity', 'bar' => 'bg-emerald-500', 'badge' => 'text-emerald-700 bg-emerald-50 border-emerald-200'];
        }
        if ($score <= 6.5) {
            return ['label' => 'Moderate Concern', 'bar' => 'bg-amber-500', 'badge' => 'text-amber-800 bg-amber-50 border-amber-200'];
        }
        return ['label' => 'Elevated Severity', 'bar' => 'bg-[var(--accent)]', 'badge' => 'text-[var(--accent)] bg-[var(--accent-tint)] border-[var(--accent-soft)]'];
    };

    $commInfo = $abilityDescriptor((float) ($domainAverages['communication'] ?? 0));
    $socialInfo = $abilityDescriptor((float) ($domainAverages['social'] ?? 0));
    $engageInfo = $abilityDescriptor((float) ($domainAverages['engagement'] ?? 0));
    $repInfo = $repetitiveSeverityDescriptor((float) ($domainAverages['repetitive'] ?? 0));
@endphp

<div class="flex min-h-screen">

    {{-- ============================= SIDEBAR ============================= --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-[var(--line)] h-screen sticky top-0 overflow-y-auto scrollbar-thin">
        <div class="flex items-center gap-2 px-5 h-16 border-b border-[var(--line)]">
            <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">A</div>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-tight">AutiSync</p>
                <p class="text-[10px] tracking-[.12em] text-[var(--ink-soft)] font-semibold">CLINICAL PORTAL</p>
            </div>
        </div>

        <nav class="px-3 py-5 space-y-6 flex-1">
            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Clinical Workspace</p>
                <div class="space-y-1">
                    <a href="{{ route('clinician.dashboard') }}" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        Clinical Dashboard
                    </a>
                    <a href="{{ route('clinician.children.show', $child) }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        {{ $child->first_name }} · Monitoring
                    </a>
                </div>
            </div>

            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Child Sections</p>
                <div class="space-y-1">
                    <a href="#goals-milestones-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/></svg>
                        Goals &amp; Milestones ({{ $therapyGoals->count() }})
                    </a>
                    <a href="#longitudinal-scores-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19V6"/><path d="M10 19V10"/><path d="M16 19V4"/><path d="M22 19V13"/></svg>
                        Caregiver Logs &amp; History ({{ $observations->count() }})
                    </a>
                    <a href="#caregiver-recommendations-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 10-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/></svg>
                        Caregiver Support ({{ $child->recommendations->count() }})
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
                    @if ($clinicianAvatarUrl)
                        <img src="{{ $clinicianAvatarUrl }}" alt="{{ $clinician->name }}" class="w-full h-full object-cover">
                    @else
                        {{ $clinician?->initials() ?? 'CL' }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold truncate">{{ $clinician->name }}</p>
                    <p class="text-[11px] text-[var(--ink-soft)] truncate">Specialist Clinician</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ============================= MAIN ============================= --}}
    <div class="flex-1 min-w-0">

        {{-- Topbar --}}
        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('clinician.dashboard') }}" class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap">
                    ← Clinical Dashboard
                </a>
                <span class="text-[var(--line)]">|</span>
                <p class="font-semibold text-[15px] truncate">{{ $child->first_name }} · Clinical Monitoring</p>
            </div>

            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 hover:opacity-90 transition" title="Profile Settings">
                    <div class="w-8 h-8 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold overflow-hidden">
                        @if ($clinicianAvatarUrl)
                            <img src="{{ $clinicianAvatarUrl }}" alt="{{ $clinician->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $clinician?->initials() ?? 'CL' }}
                        @endif
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <p class="text-[13px] font-semibold">{{ $clinician->name }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">{{ ucfirst($clinician->role) }}</p>
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

            {{-- ===================== CHILD HEADER & CLINICAL ACTIONS ===================== --}}
            <section class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-20 h-20 shrink-0 rounded-2xl bg-[var(--accent-tint)] border-2 border-[var(--accent-soft)] flex items-center justify-center overflow-hidden text-lg font-extrabold text-[var(--accent)]">
                        @if ($childAvatarUrl)
                            <img src="{{ $childAvatarUrl }}" alt="{{ $child->first_name }}" class="w-full h-full object-cover">
                        @else
                            {{ $child->initials() }}
                        @endif
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">{{ $child->first_name }}</h1>
                            @if ($child->diagnosis_confirmed)
                                <span class="text-[10px] font-bold tracking-wide bg-[var(--ink)] text-white px-2.5 py-1 rounded-full">ASD CONFIRMED</span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 mt-2 text-xs text-[var(--ink-soft)]">
                            <span>
                                <strong>Age:</strong>
                                @if ($child->age_in_months >= 12)
                                    {{ floor($child->age_in_months / 12) }} yrs{{ $child->age_in_months % 12 > 0 ? ', ' . ($child->age_in_months % 12) . ' mos' : '' }} ({{ $child->age_in_months }} mos)
                                @else
                                    {{ $child->age_in_months }} mos
                                @endif
                            </span>
                            <span><strong>Sex:</strong> {{ $child->sex }}</span>
                            <span><strong>Caregiver:</strong> {{ $child->caregiver->name ?? 'Assigned Caregiver' }} ({{ $child->caregiver->email ?? '' }})</span>
                        </div>

                        <p class="text-xs text-[var(--ink-soft)] mt-2 max-w-2xl leading-relaxed">
                            Review caregiver behavioural logs, record standardized clinical assessments, manage SMART therapy goals and milestones, and approve home support guidance.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    @if ($latestObservation)
                        {{-- 14-Feature Assessment Button + Contextual Info Popover --}}
                        <div class="relative inline-flex items-center gap-1.5">
                            <a
                                href="{{ route('clinician.assessments.edit', $latestObservation) }}"
                                class="inline-flex items-center gap-2 border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)] text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[#efe7dc] transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                                {{ $hasCompleteAssessment ? 'Edit Assessment' : 'Start Assessment' }}
                            </a>

                            <button
                                type="button"
                                onclick="toggleAssessmentDomainsTooltip()"
                                aria-label="View 14-Feature Clinical Assessment Domains"
                                title="What is included in the 14-Feature Clinical Assessment?"
                                class="w-8 h-8 rounded-full border border-[var(--line)] bg-white text-[var(--ink)] hover:bg-[var(--canvas)] flex items-center justify-center text-xs font-extrabold transition shadow-sm"
                            >
                                i
                            </button>

                            {{-- Contextual Popover Checklist of the 14 Clinical Features --}}
                            <div
                                id="assessment-domains-tooltip"
                                class="hidden absolute right-0 top-12 z-30 w-80 sm:w-96 rounded-2xl border border-[var(--line)] bg-white p-4 shadow-xl text-left"
                            >
                                <div class="flex items-start justify-between gap-2 mb-2 pb-2 border-b border-[var(--line)]">
                                    <div>
                                        <p class="text-xs font-extrabold text-[var(--ink)]">14-Feature Clinical Assessment Checklist</p>
                                        <p class="text-[11px] text-[var(--ink-soft)]">Standardized clinical &amp; developmental domains evaluated:</p>
                                    </div>
                                    <button type="button" onclick="toggleAssessmentDomainsTooltip()" class="text-xs font-bold text-[var(--ink-soft)] hover:text-[var(--ink)] px-1">✕</button>
                                </div>
                                <ul class="text-[11px] text-[var(--ink-soft)] space-y-1.5 leading-relaxed">
                                    <li><strong class="text-[var(--ink)]">1. ADOS-2 Diagnostic Scores (3):</strong> Social Affect (eye contact, joint attention, reciprocal interaction), Restricted &amp; Repetitive Behaviours (RRB), and Comparison Severity Category.</li>
                                    <li><strong class="text-[var(--ink)]">2. Standardized Cognitive Profile (3):</strong> Verbal, Nonverbal, and General Conceptual Ability (GCA) percentiles.</li>
                                    <li><strong class="text-[var(--ink)]">3. Adaptive Functioning (1):</strong> Adaptive Composite percentile (everyday independence &amp; self-care).</li>
                                    <li><strong class="text-[var(--ink)]">4. Clinical &amp; Therapy Context (5):</strong> Primary Domain, Assessment Domain, DSM-5 Support Level, Active Therapy Services Count, and Early Session Task Success (%).</li>
                                    <li><strong class="text-[var(--ink)]">5. Developmental Baseline (2):</strong> Child Age (years) and Sex/Gender category.</li>
                                </ul>
                            </div>
                        </div>

                        @if ($hasCompleteAssessment)
                            <form method="POST" action="{{ route('behavioural-records.predict', $latestObservation) }}">
                                @csrf
                                <input type="hidden" name="redirect_to" value="/clinician/children/{{ $child->id }}">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 bg-[var(--ink)] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Calculate Clinical Progress Status
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </section>

            {{-- ===================== 4 SUMMARY KPI CARDS ===================== --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {{-- Card 1: Logged Sessions & Avg Success --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <p class="eyebrow uppercase">Behavioural Sessions</p>
                        <p class="text-3xl font-extrabold tracking-tight mt-2">{{ $observations->count() }}</p>
                    </div>
                    <p class="text-xs text-[var(--ink-soft)] mt-2">
                        Avg task completion: <strong>{{ number_format($domainAverages['success_rate'], 1) }}%</strong> ({{ $domainAverages['session_duration'] }} min avg)
                    </p>
                </div>

                {{-- Card 2: Overall Domain Summary (with color-coded bars & directionality labels) --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <p class="eyebrow uppercase">Overall Domain Summary</p>
                        <span class="text-[10px] font-semibold text-[var(--ink-soft)]">0–10 Scale</span>
                    </div>

                    {{-- Communication (Higher = Higher Ability) --}}
                    <div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-semibold text-[var(--ink)]">Comm: <strong>{{ $domainAverages['communication'] }}/10</strong></span>
                            <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded border {{ $commInfo['badge'] }}">{{ $commInfo['label'] }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-[var(--canvas)] overflow-hidden mt-1">
                            <div class="h-full {{ $commInfo['bar'] }} rounded-full" style="width: {{ min(100, round($domainAverages['communication'] * 10)) }}%;"></div>
                        </div>
                    </div>

                    {{-- Social Interaction (Higher = Higher Ability) --}}
                    <div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-semibold text-[var(--ink)]">Social: <strong>{{ $domainAverages['social'] }}/10</strong></span>
                            <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded border {{ $socialInfo['badge'] }}">{{ $socialInfo['label'] }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-[var(--canvas)] overflow-hidden mt-1">
                            <div class="h-full {{ $socialInfo['bar'] }} rounded-full" style="width: {{ min(100, round($domainAverages['social'] * 10)) }}%;"></div>
                        </div>
                    </div>

                    {{-- Engagement (Higher = Higher Ability) --}}
                    <div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-semibold text-[var(--ink)]">Engage: <strong>{{ $domainAverages['engagement'] }}/10</strong></span>
                            <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded border {{ $engageInfo['badge'] }}">{{ $engageInfo['label'] }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-[var(--canvas)] overflow-hidden mt-1">
                            <div class="h-full {{ $engageInfo['bar'] }} rounded-full" style="width: {{ min(100, round($domainAverages['engagement'] * 10)) }}%;"></div>
                        </div>
                    </div>

                    {{-- Repetitive Behaviour (Higher = Higher Severity; Lower = Milder Severity) --}}
                    <div class="pt-1 border-t border-[var(--line)]">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-semibold text-[var(--ink)]">Repetitive: <strong>{{ $domainAverages['repetitive'] }}/10</strong></span>
                            <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded border {{ $repInfo['badge'] }}">{{ $repInfo['label'] }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-[var(--canvas)] overflow-hidden mt-1">
                            <div class="h-full {{ $repInfo['bar'] }} rounded-full" style="width: {{ min(100, round($domainAverages['repetitive'] * 10)) }}%;"></div>
                        </div>
                        <p class="text-[10px] text-[var(--ink-soft)] mt-1">
                            <em>Note:</em> Higher Comm/Social/Engage = stronger ability; lower Repetitive = milder severity.
                        </p>
                    </div>
                </div>

                {{-- Card 3: Therapy Goals & Milestones --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <p class="eyebrow uppercase">Therapy Goals &amp; Milestones</p>
                        <p class="text-3xl font-extrabold tracking-tight mt-2">
                            {{ $activeGoalsCount }} <span class="text-sm font-semibold text-[var(--ink-soft)]">active</span>
                        </p>
                    </div>
                    <p class="text-xs text-[var(--ink-soft)] mt-2">
                        <strong>{{ $achievedMilestonesCount }}/{{ $totalMilestonesCount }}</strong> milestones achieved · {{ $completedGoalsCount }} {{ Str::plural('goal', $completedGoalsCount) }} completed
                    </p>
                </div>

                {{-- Card 4: Clinical Progress Status --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <p class="eyebrow uppercase">Clinical Progress Status</p>
                        @if ($latestPrediction)
                            <div class="mt-2">
                                <span class="inline-block text-xs font-bold px-2.5 py-1 rounded-full {{ (string) $latestPrediction->predicted_outcome === '1' ? 'bg-[var(--accent-tint)] text-[var(--accent)] border border-[var(--accent-soft)]' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                    {{ $latestPrediction->caregiverIndicatorLabel() }}
                                </span>
                            </div>
                        @else
                            <p class="text-lg font-bold text-[var(--ink-soft)] mt-2">Pending Assessment</p>
                        @endif
                    </div>
                    @if ($latestPrediction)
                        <p class="text-xs text-[var(--ink-soft)] mt-2">
                            {{ $latestPrediction->validated_by_clinician ? '✓ Verified by Clinician' : '⏳ Pending Clinician Verification' }}
                            @if ($latestPrediction->confidence_score !== null)
                                · Calculated Progress Score: {{ number_format((float) $latestPrediction->confidence_score * 100, 1) }}%
                            @endif
                        </p>
                    @else
                        <p class="text-xs text-[var(--ink-soft)] mt-2">
                            Complete the standardized assessment to calculate clinical progress status.
                        </p>
                    @endif
                </div>
            </section>

            {{-- ===================== AI-ASSISTED PROGRESS SUMMARY & CLINICAL FEEDBACK PANEL ===================== --}}
            @if ($latestPrediction)
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        <div class="lg:col-span-5 space-y-2.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="eyebrow uppercase">
                                    {{ $latestPrediction->validated_by_clinician ? 'AI-Assisted Progress Summary (Verified)' : 'AI-Assisted Progress Summary (Pending Verification)' }}
                                </span>
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $latestPrediction->validated_by_clinician ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                    {{ $latestPrediction->validated_by_clinician ? 'Approved by Clinician' : 'Pending Verification' }}
                                </span>
                            </div>
                            <h2 class="text-lg font-bold tracking-tight">
                                {{ $latestPrediction->caregiverIndicatorLabel() }}
                            </h2>
                            <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                {{ $latestPrediction->clinicalProgressSummaryDescription() }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('clinician.predictions.validate', $latestPrediction) }}" class="lg:col-span-7 space-y-3 bg-[var(--canvas)]/60 border border-[var(--line)] rounded-xl p-4 sm:p-5">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="validated_by_clinician" value="1">
                            <input type="hidden" name="redirect_to" value="/clinician/children/{{ $child->id }}">

                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                                    <label for="child-clinician-feedback" class="block text-xs font-bold text-[var(--ink)]">
                                        Specialist Feedback &amp; Guidance for Caregiver
                                    </label>
                                    <span class="text-[11px] text-[var(--ink-soft)]">Quick Clinical Note Templates:</span>
                                </div>

                                {{-- Quick Clinical Template Buttons --}}
                                <div class="flex flex-wrap items-center gap-1.5 mb-2.5">
                                    <button
                                        type="button"
                                        onclick="insertClinicalFeedbackTemplate('Continue daily 10-minute structured play and visual choice routines at home; reinforce all spontaneous communication attempts warmly.')"
                                        class="text-[11px] font-semibold bg-white border border-[var(--line)] hover:border-[var(--ink)] text-[var(--ink)] px-2.5 py-1 rounded-lg transition"
                                    >
                                        + Insert Recommended Home Activity
                                    </button>
                                    <button
                                        type="button"
                                        onclick="insertClinicalFeedbackTemplate('Please log 2–3 home observation sessions this week focusing on transition routines so we can review progress at our next clinic appointment.')"
                                        class="text-[11px] font-semibold bg-white border border-[var(--line)] hover:border-[var(--ink)] text-[var(--ink)] px-2.5 py-1 rounded-lg transition"
                                    >
                                        + Request Caregiver Follow-Up
                                    </button>
                                    <button
                                        type="button"
                                        onclick="insertClinicalFeedbackTemplate('Current developmental trajectory aligns well with active therapy goals. Maintain consistent visual schedules during daily self-care routines.')"
                                        class="text-[11px] font-semibold bg-white border border-[var(--line)] hover:border-[var(--ink)] text-[var(--ink)] px-2.5 py-1 rounded-lg transition"
                                    >
                                        + Reinforce Current Routine
                                    </button>
                                </div>

                                <textarea
                                    id="child-clinician-feedback"
                                    name="clinician_feedback"
                                    rows="5"
                                    placeholder="Record clinical interpretation, home routine adjustments, or follow-up guidance for {{ $child->first_name }}'s caregiver..."
                                    class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2.5 text-xs text-[var(--ink)] placeholder:text-[var(--ink-soft)] focus:outline-none resize-y leading-relaxed"
                                >{{ old('clinician_feedback', $latestPrediction->clinician_feedback) }}</textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 bg-[var(--ink)] text-white text-xs font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    Approve Summary &amp; Send Feedback to Caregiver
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            @endif

            {{-- ===================== THERAPY GOALS & MILESTONES MANAGEMENT ===================== --}}
            <section id="goals-milestones-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[var(--line)] pb-4">
                    <div>
                        <p class="eyebrow uppercase mb-0.5">Individualized Intervention Plan</p>
                        <h2 class="text-lg font-bold tracking-tight">Therapy Goals &amp; Developmental Milestones ({{ $therapyGoals->count() }})</h2>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Create SMART therapy goals and record milestone attainment. All updates reflect on the caregiver's Goals &amp; Milestones view.
                        </p>
                    </div>
                </div>

                {{-- Create New Therapy Goal Form --}}
                <div class="bg-[var(--canvas)]/70 border border-[var(--line)] rounded-2xl p-5">
                    <h3 class="text-sm font-bold mb-3">Create New Therapy Goal for {{ $child->first_name }}</h3>
                    <form method="POST" action="{{ route('clinician.goals.store', $child) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                        @csrf
                        <div class="lg:col-span-2">
                            <label for="goal_domain" class="block text-xs font-semibold mb-1">Clinical Domain</label>
                            <select id="goal_domain" name="goal_domain" required class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2 text-xs font-medium text-[var(--ink)]">
                                <option value="Communication" {{ old('goal_domain') === 'Communication' ? 'selected' : '' }}>Communication</option>
                                <option value="Social Interaction" {{ old('goal_domain') === 'Social Interaction' ? 'selected' : '' }}>Social Interaction</option>
                                <option value="Adaptive & Daily Living" {{ old('goal_domain') === 'Adaptive & Daily Living' ? 'selected' : '' }}>Adaptive &amp; Daily Living</option>
                                <option value="Engagement & Attention" {{ old('goal_domain') === 'Engagement & Attention' ? 'selected' : '' }}>Engagement &amp; Attention</option>
                                <option value="Sensory & Emotional Regulation" {{ old('goal_domain') === 'Sensory & Emotional Regulation' ? 'selected' : '' }}>Sensory &amp; Emotional Regulation</option>
                            </select>
                        </div>

                        <div>
                            <label for="baseline_level" class="block text-xs font-semibold mb-1">Current Level (%)</label>
                            <input type="text" id="baseline_level" name="baseline_level" value="{{ old('baseline_level', '35') }}" placeholder="e.g. 35" required class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2 text-xs text-[var(--ink)]">
                        </div>

                        <div>
                            <label for="target_level" class="block text-xs font-semibold mb-1">Target Level (%)</label>
                            <input type="text" id="target_level" name="target_level" value="{{ old('target_level', '80') }}" placeholder="e.g. 80" required class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2 text-xs text-[var(--ink)]">
                        </div>

                        <div>
                            <label for="target_date" class="block text-xs font-semibold mb-1">Target Date</label>
                            <input type="date" id="target_date" name="target_date" value="{{ old('target_date') }}" class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2 text-xs text-[var(--ink)]">
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-semibold mb-1">Initial Status</label>
                            <select id="status" name="status" required class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2 text-xs font-medium text-[var(--ink)]">
                                <option value="active">Active</option>
                                <option value="in_progress">In Progress</option>
                                <option value="on_track">On Track</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2 lg:col-span-5">
                            <label for="goal_description" class="block text-xs font-semibold mb-1">SMART Goal Description</label>
                            <input
                                type="text"
                                id="goal_description"
                                name="goal_description"
                                value="{{ old('goal_description') }}"
                                required
                                placeholder="e.g. Initiate two-word functional requests during structured play in 4 out of 5 opportunities."
                                class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2 text-xs text-[var(--ink)]"
                            >
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-[var(--ink)] text-white text-xs font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition">
                                + Add Therapy Goal
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Existing Therapy Goals Grid --}}
                @if ($therapyGoals->isNotEmpty())
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        @foreach ($therapyGoals as $goal)
                            @php
                                $goalStatus = strtolower($goal->status ?? 'active');
                                $progressPct = is_numeric($goal->baseline_level) && is_numeric($goal->target_level) && (float) $goal->target_level > 0
                                    ? min(100, round(((float) $goal->baseline_level / (float) $goal->target_level) * 100))
                                    : ($goalStatus === 'completed' ? 100 : 50);
                            @endphp
                            <div class="border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between bg-white space-y-4">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)]">
                                            {{ $goal->goal_domain }}
                                        </span>
                                        <span class="text-[10px] font-bold tracking-wider px-2.5 py-0.5 rounded-full uppercase {{ $goalStatus === 'completed' ? 'border border-emerald-300 bg-emerald-50 text-emerald-800' : 'border border-[var(--accent-soft)] bg-[var(--accent-tint)] text-[var(--accent)]' }}">
                                            {{ str_replace('_', ' ', ucfirst($goal->status ?? 'Active')) }}
                                        </span>
                                    </div>

                                    <h4 class="text-sm font-bold leading-snug">{{ $goal->goal_description }}</h4>

                                    {{-- Progress Bar --}}
                                    <div>
                                        <div class="flex justify-between text-[11px] font-semibold mb-1">
                                            <span class="text-[var(--ink-soft)]">Current: {{ $goal->baseline_level }} → Target: {{ $goal->target_level }}</span>
                                            <span class="text-[var(--ink)]">{{ $progressPct }}%</span>
                                        </div>
                                        <div class="h-2 w-full rounded-full bg-[var(--canvas)] border border-[var(--line)] overflow-hidden">
                                            <div class="h-full bg-[var(--ink)] rounded-full transition-all duration-300" style="width: {{ $progressPct }}%;"></div>
                                        </div>
                                    </div>

                                    {{-- Update Goal Status / Levels Form --}}
                                    <form method="POST" action="{{ route('clinician.goals.update', [$child, $goal]) }}" class="pt-2 border-t border-[var(--line)] grid grid-cols-1 sm:grid-cols-4 gap-2 items-end">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[var(--ink-soft)] mb-0.5">Current Level</label>
                                            <input type="text" name="baseline_level" value="{{ $goal->baseline_level }}" class="w-full rounded-md border border-[var(--line)] bg-[var(--canvas)] px-2.5 py-1.5 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[var(--ink-soft)] mb-0.5">Target Level</label>
                                            <input type="text" name="target_level" value="{{ $goal->target_level }}" class="w-full rounded-md border border-[var(--line)] bg-[var(--canvas)] px-2.5 py-1.5 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[var(--ink-soft)] mb-0.5">Status</label>
                                            <select name="status" class="w-full rounded-md border border-[var(--line)] bg-[var(--canvas)] px-2 py-1.5 text-xs font-semibold">
                                                <option value="active" {{ $goal->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="in_progress" {{ $goal->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="on_track" {{ $goal->status === 'on_track' ? 'selected' : '' }}>On Track</option>
                                                <option value="completed" {{ $goal->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>
                                        <div>
                                            <button type="submit" class="w-full rounded-md border border-[var(--ink)] bg-white text-[var(--ink)] hover:bg-[var(--ink)] hover:text-white text-xs font-semibold px-2.5 py-1.5 transition">
                                                Save Goal
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Milestones Checklist & Toggle --}}
                                    <div class="pt-3 border-t border-[var(--line)] space-y-2.5">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                            Developmental Milestones ({{ $goal->milestones->where('achieved_flag', true)->count() }}/{{ $goal->milestones->count() }} achieved)
                                        </p>

                                        @if ($goal->milestones->isNotEmpty())
                                            <div class="space-y-2">
                                                @foreach ($goal->milestones as $ms)
                                                    <div class="flex items-center justify-between gap-2 rounded-lg bg-[var(--canvas)]/60 border border-[var(--line)] px-3 py-2 text-xs">
                                                        <div class="flex items-center gap-2 min-w-0">
                                                            @if ($ms->achieved_flag)
                                                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                                                </span>
                                                                <span class="line-through text-[var(--ink-soft)] truncate">{{ $ms->milestone_description }}</span>
                                                            @else
                                                                <span class="w-4 h-4 rounded-full border border-[var(--line)] bg-white shrink-0"></span>
                                                                <span class="font-medium truncate">{{ $ms->milestone_description }}</span>
                                                            @endif
                                                        </div>

                                                        <form method="POST" action="{{ route('clinician.milestones.toggle', [$child, $ms]) }}" class="shrink-0">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="achieved_flag" value="{{ $ms->achieved_flag ? '0' : '1' }}">
                                                            <button type="submit" class="text-[11px] font-semibold px-2 py-0.5 rounded {{ $ms->achieved_flag ? 'text-[var(--ink-soft)] hover:underline' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' }}">
                                                                {{ $ms->achieved_flag ? 'Reopen' : 'Mark Achieved' }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-xs text-[var(--ink-soft)] italic">No milestones added to this goal yet.</p>
                                        @endif

                                        {{-- Add Milestone Form --}}
                                        <form method="POST" action="{{ route('clinician.milestones.store', [$child, $goal]) }}" class="flex items-center gap-2 pt-1">
                                            @csrf
                                            <input
                                                type="text"
                                                name="milestone_description"
                                                required
                                                placeholder="Add a step or milestone for this goal..."
                                                class="flex-1 rounded-lg border border-[var(--line)] bg-white px-3 py-1.5 text-xs placeholder:text-[var(--ink-soft)]"
                                            >
                                            <button type="submit" class="shrink-0 rounded-lg bg-[var(--ink)] text-white text-xs font-semibold px-3 py-1.5 hover:opacity-90 transition">
                                                + Milestone
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-[var(--line)] flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                    <span>Target Date: {{ $goal->target_date?->format('M d, Y') ?? 'Ongoing' }}</span>
                                    <span>Clinician: <strong>{{ $goal->clinician?->name ?? $clinician->name }}</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="border border-dashed border-[var(--line)] rounded-xl p-8 text-center">
                        <p class="text-sm font-semibold">No therapy goals configured for {{ $child->first_name }} yet</p>
                        <p class="text-xs text-[var(--ink-soft)] mt-1">
                            Use the form above to add the first SMART therapy goal and milestone checklist.
                        </p>
                    </div>
                @endif
            </section>

            {{-- ===================== CAREGIVER LOGS & SESSION HISTORY ===================== --}}
            <section id="longitudinal-scores-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="eyebrow uppercase mb-0.5">Caregiver Logs &amp; Session History</p>
                        <h2 class="text-lg font-bold tracking-tight">Behavioural Observation History ({{ $observations->count() }})</h2>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Session-by-session caregiver ratings across Communication, Social Interaction, Engagement, Repetitive Behaviour, and Task Completion.
                        </p>
                    </div>
                </div>

                @if ($observations->isNotEmpty())
                    <div class="overflow-x-auto -mx-1">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-[var(--ink-soft)] font-semibold border-b border-[var(--line)]">
                                    <th class="py-2.5 px-2">Session Date</th>
                                    <th class="py-2.5 px-2">Comm (0–10)</th>
                                    <th class="py-2.5 px-2">Social (0–10)</th>
                                    <th class="py-2.5 px-2">Engage (0–10)</th>
                                    <th class="py-2.5 px-2">Repetitive (0–10)</th>
                                    <th class="py-2.5 px-2">Success Rate</th>
                                    <th class="py-2.5 px-2">Caregiver Notes</th>
                                    <th class="py-2.5 px-2 text-right">Clinical Evaluation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--line)]">
                                @foreach ($observations as $obs)
                                    @php
                                        $obsComplete = \App\Http\Controllers\ClinicianDashboardController::hasCompleteAssessment($obs);
                                        $obsRepInfo = $repetitiveSeverityDescriptor((float) $obs->repetitive_behaviour_score);
                                    @endphp
                                    <tr class="hover:bg-[var(--canvas)]/50 transition">
                                        <td class="py-3 px-2 font-semibold text-xs whitespace-nowrap">
                                            {{ $obs->observation_date?->format('M d, Y') }}
                                            <p class="text-[11px] font-normal text-[var(--ink-soft)]">{{ $obs->session_duration_minutes }} mins</p>
                                        </td>
                                        <td class="py-3 px-2 text-xs font-bold">{{ $obs->communication_score }}/10</td>
                                        <td class="py-3 px-2 text-xs font-bold">{{ $obs->social_interaction_score }}/10</td>
                                        <td class="py-3 px-2 text-xs font-bold">{{ $obs->engagement_level }}/10</td>
                                        <td class="py-3 px-2 text-xs">
                                            <span class="font-bold text-[var(--accent)]">{{ $obs->repetitive_behaviour_score }}/10</span>
                                            <span class="block text-[10px] text-[var(--ink-soft)]">{{ $obsRepInfo['label'] }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-xs font-semibold">{{ number_format((float) $obs->success_rate, 0) }}%</td>
                                        <td class="py-3 px-2 text-xs text-[var(--ink-soft)] max-w-xs">
                                            {{ $obs->notes ?: '—' }}
                                        </td>
                                        <td class="py-3 px-2 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <a
                                                    href="{{ route('clinician.assessments.edit', $obs) }}"
                                                    class="inline-flex items-center gap-1 border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)] text-xs font-semibold px-2.5 py-1.5 rounded-lg hover:bg-[var(--ink)] hover:text-white transition"
                                                >
                                                    {{ $obsComplete ? 'Edit Assessment' : 'Start Assessment' }}
                                                </a>

                                                @if ($obsComplete)
                                                    <form method="POST" action="{{ route('behavioural-records.predict', $obs) }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="redirect_to" value="/clinician/children/{{ $child->id }}">
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1 bg-[var(--ink)] text-white text-xs font-semibold px-2.5 py-1.5 rounded-lg hover:opacity-90 transition"
                                                        >
                                                            Calculate Clinical Progress Status
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="border border-dashed border-[var(--line)] rounded-xl p-8 text-center">
                        <p class="text-sm font-semibold">No behavioural observations logged yet</p>
                        <p class="text-xs text-[var(--ink-soft)] mt-1">
                            Once the caregiver logs behavioural sessions for {{ $child->first_name }}, longitudinal domain scores will appear here.
                        </p>
                    </div>
                @endif
            </section>

            {{-- ===================== CAREGIVER HOME SUPPORT RECOMMENDATIONS ===================== --}}
            <section id="caregiver-recommendations-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 space-y-4">
                <div>
                    <p class="eyebrow uppercase mb-0.5">Home-to-Clinic Continuity</p>
                    <h2 class="text-lg font-bold tracking-tight">Caregiver Home Support Recommendations ({{ $child->recommendations->count() }})</h2>
                    <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                        Everyday support activities shared with {{ $child->first_name }}'s caregiver and their home practice status.
                    </p>
                </div>

                @if ($child->recommendations->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($child->recommendations as $rec)
                            @php
                                $guide = $rec->caregiverActivityGuide($child->first_name);
                                $isPractised = in_array(strtolower((string) $rec->status), ['implemented', 'completed'], true);
                            @endphp
                            <div class="border border-[var(--line)] rounded-xl p-4 bg-[var(--canvas)]/40 flex flex-col justify-between space-y-3">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-white border border-[var(--line)] text-[var(--ink)]">
                                            {{ $guide['area'] }}
                                        </span>
                                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full {{ $isPractised ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[var(--accent-tint)] text-[var(--accent)] border border-[var(--accent-soft)]' }}">
                                            {{ $isPractised ? 'Practised at Home' : 'Trying at Home' }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-semibold text-[var(--ink)] leading-relaxed">
                                        {{ $rec->recommendation_text }}
                                    </p>
                                    <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                        <strong>Home Routine Tip:</strong> {{ $guide['how_to_practise'] }}
                                    </p>
                                </div>
                                <div class="pt-2 border-t border-[var(--line)] flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                    <span>Generated: {{ $rec->date_generated?->format('M d, Y') ?? 'Recently' }}</span>
                                    <span>Category: {{ $rec->category }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="border border-dashed border-[var(--line)] rounded-xl p-8 text-center">
                        <p class="text-sm font-semibold">No caregiver home recommendations generated yet</p>
                        <p class="text-xs text-[var(--ink-soft)] mt-1">
                            Home activity guides are automatically generated when you calculate the Clinical Progress Status for an observation.
                        </p>
                    </div>
                @endif
            </section>

        </main>
    </div>
</div>

<script>
    function toggleAssessmentDomainsTooltip() {
        const tooltip = document.getElementById('assessment-domains-tooltip');
        if (tooltip) {
            tooltip.classList.toggle('hidden');
        }
    }

    function insertClinicalFeedbackTemplate(templateText) {
        const textarea = document.getElementById('child-clinician-feedback');
        if (!textarea) return;
        const existing = textarea.value.trim();
        textarea.value = existing ? (existing + ' ' + templateText) : templateText;
        textarea.focus();
    }
</script>
</body>
</html>
