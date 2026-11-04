<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->first_name }} · Child Profile · AutiSync</title>

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

        html, body {
            font-family:'Inter', ui-sans-serif, system-ui, sans-serif;
            background:var(--canvas);
            color:var(--ink);
        }

        .eyebrow {
            font-size:11px;
            letter-spacing:.06em;
            color:var(--ink-soft);
            font-weight:600;
        }

        .scrollbar-thin::-webkit-scrollbar { width:6px; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background:#e6dfd4; border-radius:999px; }

        .navlink { display:flex; align-items:center; gap:10px; padding:8px 12px; border-radius:8px; font-size:14px; color:#5b574f; font-weight:500; }
        .navlink:hover { background:#f3ede3; color:var(--ink); }
        .navlink.active { background:var(--ink); color:#fff; }

        button:focus-visible, a:focus-visible, input:focus-visible { outline:2px solid var(--ink); outline-offset:2px; }
    </style>
</head>

<body class="min-h-screen">

@php
    $currentTab = request('tab', 'overview');
    $childTotalRecords = $totalRecords ?? $child->behaviouralRecords()->count();
    $childAvgSuccess = $averageSuccessRate ?? ($childTotalRecords > 0 ? $child->behaviouralRecords()->avg('success_rate') : null);
    $childAvgDuration = $averageDuration ?? ($childTotalRecords > 0 ? $child->behaviouralRecords()->avg('session_duration_minutes') : null);
    $childRecordsList = $records ?? $child->behaviouralRecords()->latest('observation_date')->latest('id')->paginate(10);
    $recentRecords = $recentRecords ?? $child->behaviouralRecords()->latest('observation_date')->latest('id')->take(5)->get();
    $therapyGoalsCount = $therapyGoalsCount ?? $child->therapyGoals()->count();
    $recommendationsCount = $recommendationsCount ?? $child->recommendations()->count();
    $latestPrediction = $latestPrediction ?? $child->predictions()->latest('date_generated')->first();
@endphp

<div class="flex min-h-screen">

    {{-- ============================= SIDEBAR ============================= --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-[var(--line)] h-screen sticky top-0 overflow-y-auto scrollbar-thin">

        {{-- Logo --}}
        <div class="flex items-center gap-2 px-5 h-16 border-b border-[var(--line)]">
            <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">A</div>
            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-tight">AutiSync</p>
                <p class="text-[10px] tracking-[.12em] text-[var(--ink-soft)] font-semibold">ANALYTICS</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="px-3 py-5 space-y-6 flex-1">

            {{-- Caregiver --}}
            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Caregiver</p>
                <div class="space-y-1">
                    <a href="{{ route('caregiver.dashboard') }}" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('children.index') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        Child Profile
                    </a>
                </div>
            </div>

            {{-- Behavior --}}
            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Behavior</p>
                <div class="space-y-1">
                    <a href="{{ route('behavioural-records.create') }}" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                        Observation Form
                    </a>
                    <a href="{{ route('behavioural-records.index') }}" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19V6"/><path d="M10 19V10"/><path d="M16 19V4"/><path d="M22 19V13"/></svg>
                        Records
                    </a>
                </div>
            </div>

            {{-- Support --}}
            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Support</p>
                <div class="space-y-1">
                    <a href="{{ route('children.recommendations', $child) }}" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 10-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/></svg>
                        Recommendations
                    </a>
                </div>
            </div>

        </nav>

    </aside>


    {{-- ============================= MAIN ============================= --}}
    <div class="flex-1 min-w-0">

        {{-- ============================= TOPBAR ============================= --}}
        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white sticky top-0 z-10">

            <p class="hidden sm:block font-semibold text-[15px]">Child Profile</p>

            {{-- Search --}}
            <div class="flex-1 max-w-md ml-0 sm:ml-6">
                <form method="GET" action="{{ route('children.show', $child) }}" class="relative flex items-center" onsubmit="filterChildProfileRecords(); return true;">
                    <input type="hidden" name="tab" value="{{ $currentTab }}">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)] pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input type="text" id="topbar-record-search" name="search" value="{{ request('search') }}" oninput="filterChildProfileRecords()" placeholder="Search records..."
                           class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-20 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:bg-white focus:outline-none">
                    <button type="submit" onclick="filterChildProfileRecords()" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-[var(--ink)] text-white text-xs font-semibold px-2.5 py-1 rounded-md hover:opacity-90 transition">
                        Search
                    </button>
                </form>
            </div>

            {{-- Right side --}}
            <div class="ml-auto flex items-center gap-3">

                {{-- Notifications --}}
                <button type="button" class="relative w-9 h-9 rounded-lg border border-[var(--line)] flex items-center justify-center" aria-label="Notifications">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 8a6 6 0 1112 0c0 5 2 6 2 6H4s2-1 2-6z"/><path d="M10 21a2 2 0 004 0"/></svg>
                </button>

                {{-- User --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <p class="text-[13px] font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap">
                        Logout
                    </button>
                </form>

            </div>

        </header>


        {{-- ============================= PAGE CONTENT ============================= --}}
        <main class="px-4 sm:px-8 py-8">

            {{-- ===================== CHILD PROFILE HEADER ===================== --}}
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5 mb-6">

                <div class="flex items-start gap-4">

                    {{-- Child Avatar --}}
                    <div class="w-20 h-20 shrink-0 border-2 border-[var(--accent-soft)] bg-[var(--accent-tint)] rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                        </svg>
                    </div>

                    {{-- Child Details --}}
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">{{ $child->first_name }}</h1>
                            @if ($child->diagnosis_confirmed)
                                <span class="text-[10px] font-bold tracking-wide bg-[var(--ink)] text-white px-2.5 py-1 rounded-full">ASD CONFIRMED</span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-2 text-[12px] text-[var(--ink-soft)]">
                            <span>
                                @if ($child->age_in_months >= 12)
                                    {{ floor($child->age_in_months / 12) }} years{{ $child->age_in_months % 12 > 0 ? ', ' . ($child->age_in_months % 12) . ' months' : '' }}
                                @else
                                    {{ $child->age_in_months }} months
                                @endif
                            </span>
                            <span>Sex: {{ $child->sex }}</span>
                            <span>Caregiver: {{ auth()->user()->name }}</span>
                        </div>
                    </div>

                </div>

                {{-- Profile Actions --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('children.index') }}" class="inline-flex items-center gap-1.5 border border-[var(--accent-soft)] bg-white text-[var(--accent)] text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[var(--accent-tint)] transition">
                        Back
                    </a>
                    <a href="{{ route('children.edit', $child) }}" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition">
                        Edit Profile
                    </a>
                </div>

            </div>


            {{-- ===================== PROFILE TABS ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-xl mb-6">
                <div class="flex items-center overflow-x-auto">

                    <a href="{{ route('children.show', $child) }}"
                       class="px-6 py-3 text-sm font-semibold whitespace-nowrap border-b-2 {{ $currentTab === 'overview' ? 'border-[var(--ink)] text-[var(--ink)]' : 'border-transparent text-[var(--ink-soft)] hover:text-[var(--accent)]' }}">
                        Overview
                    </a>

                    <a href="{{ route('children.show', ['child' => $child, 'tab' => 'behavioral-logs']) }}"
                       class="px-6 py-3 text-sm font-semibold whitespace-nowrap border-b-2 {{ $currentTab === 'behavioral-logs' ? 'border-[var(--ink)] text-[var(--ink)]' : 'border-transparent text-[var(--ink-soft)] hover:text-[var(--accent)]' }}">
                        Behavioral Logs
                    </a>

                    <a href="{{ route('children.goals-milestones', $child) }}"
                       class="px-6 py-3 text-sm text-[var(--ink-soft)] whitespace-nowrap border-b-2 border-transparent hover:text-[var(--accent)]">
                        Goals and Milestones
                    </a>

                    <a href="{{ route('children.recommendations', $child) }}"
                       class="px-6 py-3 text-sm text-[var(--ink-soft)] whitespace-nowrap border-b-2 border-transparent hover:text-[var(--accent)]">
                        Recommendations
                    </a>

                    <a href="{{ route('children.predictions-analytics', $child) }}"
                       class="px-6 py-3 text-sm text-[var(--ink-soft)] whitespace-nowrap border-b-2 border-transparent hover:text-[var(--accent)]">
                        ML Predictions and Analytics
                    </a>

                </div>
            </div>


            {{-- ===================== PROFILE CONTENT ===================== --}}

            {{-- TAB 1: OVERVIEW --}}
            <div id="tab-content-overview" class="{{ $currentTab === 'overview' ? '' : 'hidden' }}">

                {{-- Summary KPI Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                        <p class="eyebrow uppercase">Observation Sessions</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight">{{ number_format($childTotalRecords) }}</p>
                        <p class="mt-1 text-xs text-[var(--ink-soft)]">Recorded behavioral logs</p>
                    </div>

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                        <p class="eyebrow uppercase">Average Success Rate</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-[var(--ink)]">
                            {{ $childAvgSuccess !== null ? number_format($childAvgSuccess, 0) . '%' : '—' }}
                        </p>
                        <p class="mt-1 text-xs text-[var(--ink-soft)]">Average session task attainment</p>
                    </div>

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                        <p class="eyebrow uppercase">Avg. Session Duration</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight">
                            {{ $childAvgDuration !== null ? round($childAvgDuration) . ' min' : '—' }}
                        </p>
                        <p class="mt-1 text-xs text-[var(--ink-soft)]">Per observation session</p>
                    </div>

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                        <p class="eyebrow uppercase">ML Assessment</p>
                        <p class="mt-2 text-2xl font-extrabold tracking-tight text-[var(--ink)]">
                            {{ $latestPrediction ? 'Class ' . $latestPrediction->predicted_outcome : 'Pending' }}
                        </p>
                        <p class="mt-1 text-xs text-[var(--ink-soft)]">
                            {{ $latestPrediction ? 'Generated ' . ($latestPrediction->date_generated?->format('M d') ?? '') : 'Awaiting clinician assessment' }}
                        </p>
                    </div>

                </div>

                {{-- Navigation Cards to 3 Dedicated Pages --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

                    {{-- Goals & Milestones --}}
                    <div class="bg-white border border-[var(--line)] rounded-2xl p-6 flex flex-col justify-between hover:border-[var(--ink)] transition">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center mb-4">
                                <svg class="w-5 h-5 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r=".5"/></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)]">
                                {{ $therapyGoalsCount }} Monitored Targets
                            </span>
                            <h3 class="text-[15px] font-bold tracking-tight mt-2">Goals &amp; Milestones</h3>
                            <p class="text-[12px] text-[var(--ink-soft)] mt-1 leading-relaxed">Track developmental progress, therapy targets and clinician reviews.</p>
                        </div>
                        <div class="pt-4 border-t border-[var(--line)] mt-5">
                            <a href="{{ route('children.goals-milestones', $child) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[var(--ink)] hover:underline">
                                Open Goals &amp; Milestones
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Recommendations --}}
                    <div class="bg-white border border-[var(--line)] rounded-2xl p-6 flex flex-col justify-between hover:border-[var(--ink)] transition">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center mb-4">
                                <svg class="w-5 h-5 text-[var(--ink)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 10-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)]">
                                {{ $recommendationsCount }} Recommendations
                            </span>
                            <h3 class="text-[15px] font-bold tracking-tight mt-2">Support Recommendations</h3>
                            <p class="text-[12px] text-[var(--ink-soft)] mt-1 leading-relaxed">Actionable interventions, sensory strategies and evidence-informed routine adjustments.</p>
                        </div>
                        <div class="pt-4 border-t border-[var(--line)] mt-5">
                            <a href="{{ route('children.recommendations', $child) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[var(--ink)] hover:underline">
                                View Recommendations
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- ML Predictions & Analytics --}}
                    <div class="bg-white border border-[var(--line)] rounded-2xl p-6 flex flex-col justify-between hover:border-[var(--ink)] transition">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center mb-4">
                                <svg class="w-5 h-5 text-[var(--ink)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 20V4"/><path d="M3 20h18"/><path d="M6 16l4-5 3 3 5-7"/></svg>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)]">
                                14 Features Calibrated
                            </span>
                            <h3 class="text-[15px] font-bold tracking-tight mt-2">ML Predictions &amp; Analytics</h3>
                            <p class="text-[12px] text-[var(--ink-soft)] mt-1 leading-relaxed">Model feature contributions, developmental trajectories and clinician sign-offs.</p>
                        </div>
                        <div class="pt-4 border-t border-[var(--line)] mt-5">
                            <a href="{{ route('children.predictions-analytics', $child) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[var(--ink)] hover:underline">
                                Open Analytics Dashboard
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                </div>

                {{-- Child Information --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                    <h2 class="text-lg font-bold tracking-tight mb-4">Child Information</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="border border-[var(--line)] rounded-xl p-4">
                            <p class="text-[11px] uppercase tracking-wide text-[var(--ink-soft)]">Age</p>
                            <p class="text-sm font-semibold mt-1">
                                @if ($child->age_in_months >= 12)
                                    {{ floor($child->age_in_months / 12) }} years{{ $child->age_in_months % 12 > 0 ? ', ' . ($child->age_in_months % 12) . ' months' : '' }}
                                @else
                                    {{ $child->age_in_months }} months
                                @endif
                            </p>
                        </div>

                        <div class="border border-[var(--line)] rounded-xl p-4">
                            <p class="text-[11px] uppercase tracking-wide text-[var(--ink-soft)]">Sex</p>
                            <p class="text-sm font-semibold mt-1">{{ $child->sex }}</p>
                        </div>

                        <div class="border border-[var(--line)] rounded-xl p-4">
                            <p class="text-[11px] uppercase tracking-wide text-[var(--ink-soft)]">Born with Jaundice</p>
                            <p class="text-sm font-semibold mt-1">
                                @if (is_null($child->born_with_jaundice))
                                    Not specified
                                @elseif ($child->born_with_jaundice)
                                    Yes
                                @else
                                    No
                                @endif
                            </p>
                        </div>

                        <div class="border border-[var(--line)] rounded-xl p-4">
                            <p class="text-[11px] uppercase tracking-wide text-[var(--ink-soft)]">Family History of ASD</p>
                            <p class="text-sm font-semibold mt-1">
                                @if (is_null($child->family_member_with_asd))
                                    Not specified
                                @elseif ($child->family_member_with_asd)
                                    Yes
                                @else
                                    No
                                @endif
                            </p>
                        </div>

                        <div class="border border-[var(--line)] rounded-xl p-4">
                            <p class="text-[11px] uppercase tracking-wide text-[var(--ink-soft)]">Clinical Diagnosis</p>
                            <p class="text-sm font-semibold mt-1">{{ $child->diagnosis_confirmed ? 'Confirmed ASD' : 'Under Assessment' }}</p>
                        </div>

                        <div class="border border-[var(--line)] rounded-xl p-4">
                            <p class="text-[11px] uppercase tracking-wide text-[var(--ink-soft)]">Registered Caregiver</p>
                            <p class="text-sm font-semibold mt-1">{{ auth()->user()->name }}</p>
                        </div>

                    </div>
                </section>

                {{-- Recent Sessions Preview --}}
                @if($recentRecords->isNotEmpty())
                    <section class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 mt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-[15px] font-bold tracking-tight">Recent Behavioral Sessions</h2>
                            <a href="{{ route('children.show', ['child' => $child, 'tab' => 'behavioral-logs']) }}" class="text-[13px] font-semibold text-[var(--ink)] hover:underline">
                                View All &rarr;
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-[var(--line)] text-left text-[11px] font-semibold uppercase tracking-wider text-[var(--ink-soft)]">
                                        <th class="py-2.5 px-3">Date</th>
                                        <th class="py-2.5 px-3">Success Rate</th>
                                        <th class="py-2.5 px-3">Duration</th>
                                        <th class="py-2.5 px-3">Communication</th>
                                        <th class="py-2.5 px-3">Social</th>
                                        <th class="py-2.5 px-3">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--line)]">
                                    @foreach($recentRecords as $rec)
                                        <tr class="hover:bg-[var(--canvas)] transition">
                                            <td class="py-2.5 px-3 font-semibold">{{ $rec->observation_date?->format('M d, Y') }}</td>
                                            <td class="py-2.5 px-3 font-bold text-[var(--ink)]">{{ number_format($rec->success_rate, 0) }}%</td>
                                            <td class="py-2.5 px-3 text-[var(--ink-soft)]">{{ $rec->session_duration_minutes ?? '—' }} min</td>
                                            <td class="py-2.5 px-3">{{ $rec->communication_score }}/10</td>
                                            <td class="py-2.5 px-3">{{ $rec->social_interaction_score }}/10</td>
                                            <td class="py-2.5 px-3 text-[var(--ink-soft)] max-w-xs truncate">{{ $rec->notes ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif

            </div>


            {{-- TAB 2: BEHAVIORAL LOGS --}}
            <div id="tab-content-behavioral-logs" class="{{ $currentTab === 'behavioral-logs' ? '' : 'hidden' }}">
                <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">

                    <div class="p-6 border-b border-[var(--line)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="eyebrow uppercase mb-1">Child Observations</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Behavioral Logs for {{ $child->first_name }}</h2>
                            <p class="text-xs text-[var(--ink-soft)] mt-1">Logged sessions and observational recordings strictly for {{ $child->first_name }}.</p>
                        </div>
                        <a href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--ink)] px-4 py-2 text-sm font-semibold text-white hover:opacity-90 transition shrink-0">
                            + Record Observation
                        </a>
                    </div>

                    @if($childRecordsList->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-[var(--line)] bg-[var(--canvas)] text-left text-[11px] font-semibold uppercase tracking-wider text-[var(--ink-soft)]">
                                        <th class="py-3 px-5">Date</th>
                                        <th class="py-3 px-5">Success Rate</th>
                                        <th class="py-3 px-5">Duration</th>
                                        <th class="py-3 px-5">Scores</th>
                                        <th class="py-3 px-5">Prompts</th>
                                        <th class="py-3 px-5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--line)]">
                                    @foreach($childRecordsList as $record)
                                        <tr class="hover:bg-[var(--canvas)] transition">
                                            <td class="py-3.5 px-5 font-semibold">{{ $record->observation_date?->format('M d, Y') }}</td>
                                            <td class="py-3.5 px-5 font-bold text-[var(--ink)]">{{ number_format($record->success_rate, 0) }}%</td>
                                            <td class="py-3.5 px-5 text-[var(--ink-soft)]">{{ $record->session_duration_minutes }} min</td>
                                            <td class="py-3.5 px-5 text-[12px] text-[var(--ink-soft)]">
                                                Comm: {{ $record->communication_score }} &bull; Soc: {{ $record->social_interaction_score }} &bull; Eng: {{ $record->engagement_level }}
                                            </td>
                                            <td class="py-3.5 px-5 text-[var(--ink-soft)]">{{ $record->prompts_required }} prompts</td>
                                            <td class="py-3.5 px-5 text-right">
                                                <a href="{{ route('behavioural-records.edit', $record) }}" class="text-[13px] font-semibold text-[var(--ink)] hover:underline">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 border-t border-[var(--line)]">
                            {{ $childRecordsList->links() }}
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="w-8 h-8 mx-auto text-[var(--ink-soft)] mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M9 12h6M9 16h6M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                            </svg>
                            <p class="text-sm font-semibold">No behavioral records found for {{ $child->first_name }}.</p>
                            <p class="text-[12px] text-[var(--ink-soft)] mt-1">Start recording observations to track development over time.</p>
                            <a href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}"
                               class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-white bg-[var(--ink)] px-4 py-2 rounded-lg hover:opacity-90 transition">
                                Record First Observation
                            </a>
                        </div>
                    @endif

                </div>
            </div>

        </main>

    </div>

</div>

<script>
    function filterChildProfileRecords() {
        const input = document.getElementById('topbar-record-search');
        const q = input ? input.value.toLowerCase().trim() : '';
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(function (row) {
            const text = (row.textContent || '').toLowerCase();
            if (!q || text.indexOf(q) > -1) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }
</script>

</body>
</html>
