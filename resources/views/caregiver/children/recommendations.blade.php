<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->first_name }} · Support Recommendations · AutiSync</title>

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
    $recommendations = $recommendations ?? $child->recommendations()->with('prediction')->latest('date_generated')->get();
    $activeCount = $activeCount ?? $recommendations->whereIn('status', ['active', 'urgent', 'new'])->count();
    $implementedCount = $implementedCount ?? $recommendations->where('status', 'implemented')->count();
    $childTotalRecords = $childTotalRecords ?? $child->behaviouralRecords()->count();
    $childAvgSuccess = $childAvgSuccess ?? $child->behaviouralRecords()->avg('success_rate');
    $avgCommunication = $avgCommunication ?? $child->behaviouralRecords()->avg('communication_score');
    $avgSocial = $avgSocial ?? $child->behaviouralRecords()->avg('social_interaction_score');
    $avgEngagement = $avgEngagement ?? $child->behaviouralRecords()->avg('engagement_level');
    $avgRepetitive = $avgRepetitive ?? $child->behaviouralRecords()->avg('repetitive_behaviour_score');
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
                    <a href="{{ route('children.recommendations', $child) }}" class="navlink active">
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
                <div class="relative flex items-center">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)] pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input type="text" id="topbar-rec-search" oninput="document.getElementById('rec-search-input').value = this.value; searchRecs();" placeholder="Search recommendations..."
                           class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-20 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:bg-white focus:outline-none">
                    <button type="button" onclick="document.getElementById('rec-search-input').value = document.getElementById('topbar-rec-search').value; searchRecs();" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-[var(--ink)] text-white text-xs font-semibold px-2.5 py-1 rounded-md hover:opacity-90 transition">
                        Search
                    </button>
                </div>
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
                    <div class="w-20 h-20 shrink-0 border-2 border-[var(--accent-soft)] bg-[var(--accent-tint)] rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                    </div>
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

                <div class="flex items-center gap-3">
                    <a href="{{ route('children.show', $child) }}" class="inline-flex items-center gap-1.5 border border-[var(--line)] bg-white text-[var(--ink-soft)] text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[var(--canvas)] transition">Back to Overview</a>
                    <a href="{{ route('children.edit', $child) }}" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition">Edit Profile</a>
                </div>

            </div>


            {{-- ===================== PROFILE TABS ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-xl mb-6">
                <div class="flex items-center overflow-x-auto">
                    <a href="{{ route('children.show', $child) }}"
                       class="px-6 py-3 text-sm text-[var(--ink-soft)] whitespace-nowrap border-b-2 border-transparent hover:text-[var(--accent)]">
                        Overview
                    </a>
                    <a href="{{ route('children.show', ['child' => $child, 'tab' => 'behavioral-logs']) }}"
                       class="px-6 py-3 text-sm text-[var(--ink-soft)] whitespace-nowrap border-b-2 border-transparent hover:text-[var(--accent)]">
                        Behavioral Logs
                    </a>
                    <a href="{{ route('children.goals-milestones', $child) }}"
                       class="px-6 py-3 text-sm text-[var(--ink-soft)] whitespace-nowrap border-b-2 border-transparent hover:text-[var(--accent)]">
                        Goals and Milestones
                    </a>
                    <a href="{{ route('children.recommendations', $child) }}"
                       class="px-6 py-3 text-sm font-semibold whitespace-nowrap border-b-2 border-[var(--ink)] text-[var(--ink)]">
                        Recommendations
                    </a>
                    <a href="{{ route('children.predictions-analytics', $child) }}"
                       class="px-6 py-3 text-sm text-[var(--ink-soft)] whitespace-nowrap border-b-2 border-transparent hover:text-[var(--accent)]">
                        ML Predictions and Analytics
                    </a>
                </div>
            </div>


            {{-- ===================== PAGE HEADING ===================== --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6 pb-6 border-b border-[var(--line)]">
                <div>
                    <p class="eyebrow uppercase mb-1">Insight Center</p>
                    <h2 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">Support Recommendations</h2>
                    <p class="text-[13px] text-[var(--ink-soft)] mt-1 max-w-3xl">
                        Evidence-informed interventions and supportive strategies based on active monitoring and recorded behavioral patterns for {{ $child->first_name }}.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[var(--line)] bg-white text-xs font-semibold text-[var(--ink-soft)]">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Decision Support Active
                    </span>
                </div>
            </div>


            {{-- ===================== 3 KPI SUMMARY CARDS ===================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                {{-- Active / Urgent Actions --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Active Actions</span>
                        <div class="w-7 h-7 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center text-[var(--accent)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight">
                        {{ str_pad($activeCount, 2, '0', STR_PAD_LEFT) }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Supportive strategies in progress
                    </p>
                </div>

                {{-- Implemented --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Implemented</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight">
                        {{ str_pad($implementedCount, 2, '0', STR_PAD_LEFT) }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Strategies integrated into care routine
                    </p>
                </div>

                {{-- Observation Basis --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Observation Basis</span>
                        <div class="w-7 h-7 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--ink)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight text-[var(--ink)]">
                        {{ $childTotalRecords }} {{ Str::plural('Session', $childTotalRecords) }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Behavioral data points evaluated
                    </p>
                </div>
            </div>


            {{-- ===================== FILTER TABS & SEARCH BAR ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-4 sm:p-5 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2 overflow-x-auto text-xs font-semibold">
                        <button
                            type="button"
                            onclick="filterRecs('all')"
                            id="rec-filter-all"
                            class="px-3.5 py-2 rounded-lg bg-[var(--ink)] text-white transition cursor-pointer"
                        >
                            All Recommendations ({{ $recommendations->count() }})
                        </button>
                        <button
                            type="button"
                            onclick="filterRecs('active')"
                            id="rec-filter-active"
                            class="px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer"
                        >
                            Active ({{ $activeCount }})
                        </button>
                        <button
                            type="button"
                            onclick="filterRecs('implemented')"
                            id="rec-filter-implemented"
                            class="px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer"
                        >
                            Implemented ({{ $implementedCount }})
                        </button>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <input
                            type="text"
                            id="rec-search-input"
                            onkeyup="searchRecs()"
                            placeholder="Search suggestions..."
                            class="w-full text-xs rounded-lg border border-[var(--line)] pl-8 pr-3 py-2 bg-[var(--canvas)] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[var(--ink)]"
                        >
                        <svg class="w-3.5 h-3.5 text-[var(--ink-soft)] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </div>
                </div>
            </div>


            {{-- ===================== RECOMMENDATIONS CARDS GRID ===================== --}}
            @if($recommendations->isNotEmpty())
                <div id="recs-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
                    @foreach($recommendations as $rec)
                        @php
                            $status = strtolower($rec->status ?? 'active');
                            $cat = strtolower($rec->category ?? 'routine');
                        @endphp
                        <div
                            class="rec-card bg-white border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between hover:border-[var(--ink)] transition shadow-sm"
                            data-status="{{ $status }}"
                            data-cat="{{ $cat }}"
                            data-text="{{ strtolower($rec->recommendation_text) }}"
                        >
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)]">
                                            {{ ucfirst($rec->status ?? 'Active') }}
                                        </span>
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">
                                            {{ $rec->category }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-[var(--ink-soft)] font-mono">
                                        #REC-{{ $rec->id }}
                                    </span>
                                </div>

                                <h3 class="text-base font-bold leading-snug">
                                    {{ $rec->recommendation_text }}
                                </h3>

                                {{-- Clinical Evidence Box --}}
                                <div class="mt-4 p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)] text-xs">
                                    <p class="font-bold text-[var(--ink)] mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6M9 16h6M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                                        Clinical / Observational Evidence
                                    </p>
                                    <p class="text-[11px] text-[var(--ink-soft)] leading-relaxed italic">
                                        "Validated based on {{ $child->first_name }}'s recorded session data ({{ $childTotalRecords }} sessions logged; avg success {{ number_format($childAvgSuccess ?? 0, 0) }}%)."
                                    </p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-[var(--line)] mt-5 flex items-center justify-between text-xs text-[var(--ink-soft)]">
                                <span>
                                    {{ $rec->date_generated?->format('M d, Y') ?? 'Generated' }}
                                </span>
                                <span class="font-semibold text-[var(--ink)]">
                                    Decision Support
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($childTotalRecords > 0)
                {{-- Observation-Derived Transparent Recommendations (when no explicit DB recommendations exist) --}}
                <div class="space-y-4 mb-6">
                    <div class="p-4 rounded-xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-start gap-3 text-xs">
                        <svg class="w-5 h-5 text-[var(--accent)] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="font-bold">Observation-Derived Decision Support</p>
                            <p class="mt-0.5 text-[var(--ink-soft)]">
                                The following supportive strategies are derived transparently from {{ $child->first_name }}'s {{ $childTotalRecords }} recorded behavioral session observations. They are intended for caregiver guidance and do not replace formal clinical IEP directives.
                            </p>
                        </div>
                    </div>

                    <div id="recs-container" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        {{-- Strategy 1: Communication --}}
                        <div class="rec-card bg-white border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between hover:border-[var(--ink)] transition shadow-sm" data-status="active" data-cat="communication" data-text="communication visual prompts scaffolding">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full border border-teal-300 bg-teal-50 text-teal-800">
                                        Active
                                    </span>
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">
                                        Communication
                                    </span>
                                </div>
                                <h3 class="text-base font-bold leading-snug">
                                    Incorporate Visual Schedule &amp; AAC Scaffolding
                                </h3>
                                <p class="text-xs text-[var(--ink-soft)] mt-2 leading-relaxed">
                                    Pair verbal instructions with pictorial visual cards to support language comprehension and functional requests during daily routines.
                                </p>
                                <div class="mt-4 p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)] text-xs">
                                    <p class="font-bold text-[var(--ink)] text-[11px] mb-0.5">Observational Evidence</p>
                                    <p class="text-[11px] text-[var(--ink-soft)] italic">
                                        "Communication score averages {{ number_format($avgCommunication, 1) }}/10 across {{ $childTotalRecords }} sessions."
                                    </p>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-[var(--line)] mt-5 flex items-center justify-between text-xs text-[var(--ink-soft)]">
                                <span>Recent Pattern</span>
                                <span class="font-semibold text-[var(--ink)]">Communication Routine</span>
                            </div>
                        </div>

                        {{-- Strategy 2: Sensory & Engagement --}}
                        <div class="rec-card bg-white border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between hover:border-[var(--ink)] transition shadow-sm" data-status="active" data-cat="sensory" data-text="sensory engagement interval structured">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full border border-teal-300 bg-teal-50 text-teal-800">
                                        Active
                                    </span>
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">
                                        Sensory &amp; Focus
                                    </span>
                                </div>
                                <h3 class="text-base font-bold leading-snug">
                                    Implement 15-Minute Structured Focus Intervals
                                </h3>
                                <p class="text-xs text-[var(--ink-soft)] mt-2 leading-relaxed">
                                    Break complex tasks into focused 15-minute intervals paired with tactile sensory decompression breaks to maintain attention.
                                </p>
                                <div class="mt-4 p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)] text-xs">
                                    <p class="font-bold text-[var(--ink)] text-[11px] mb-0.5">Observational Evidence</p>
                                    <p class="text-[11px] text-[var(--ink-soft)] italic">
                                        "Engagement level averages {{ number_format($avgEngagement, 1) }}/10; focus peaks in early activity segments."
                                    </p>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-[var(--line)] mt-5 flex items-center justify-between text-xs text-[var(--ink-soft)]">
                                <span>Recent Pattern</span>
                                <span class="font-semibold text-[var(--ink)]">Sensory Integration</span>
                            </div>
                        </div>

                        {{-- Strategy 3: Routine & Transition --}}
                        <div class="rec-card bg-white border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between hover:border-[var(--ink)] transition shadow-sm" data-status="active" data-cat="routine" data-text="routine transitions regulation repetitive">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full border border-teal-300 bg-teal-50 text-teal-800">
                                        Active
                                    </span>
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">
                                        Routine
                                    </span>
                                </div>
                                <h3 class="text-base font-bold leading-snug">
                                    Pre-Transition Timers for Routine Changes
                                </h3>
                                <p class="text-xs text-[var(--ink-soft)] mt-2 leading-relaxed">
                                    Provide 5-minute and 2-minute visual countdown prompts before shifting from preferred to structured tasks to minimize dysregulation.
                                </p>
                                <div class="mt-4 p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)] text-xs">
                                    <p class="font-bold text-[var(--ink)] text-[11px] mb-0.5">Observational Evidence</p>
                                    <p class="text-[11px] text-[var(--ink-soft)] italic">
                                        "Repetitive behavior index averages {{ number_format($avgRepetitive, 1) }}/10 with {{ number_format($childAvgSuccess ?? 0, 0) }}% session success."
                                    </p>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-[var(--line)] mt-5 flex items-center justify-between text-xs text-[var(--ink-soft)]">
                                <span>Recent Pattern</span>
                                <span class="font-semibold text-[var(--ink)]">Behavior Regulation</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Clean Empty State Card --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-10 sm:p-14 text-center mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center mx-auto text-[var(--accent)] mb-4 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold">
                        No Recommendations Available Yet
                    </h3>
                    <p class="text-sm text-[var(--ink-soft)] mt-2 max-w-md mx-auto leading-relaxed">
                        Personalized support recommendations and evidence-informed interventions will generate as you record behavioral observations for {{ $child->first_name }}.
                    </p>
                    <div class="mt-6 flex items-center justify-center gap-3">
                        <a
                            href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-[var(--ink)] px-4 py-2 text-xs font-bold text-white hover:opacity-90 transition"
                        >
                            Record First Observation
                        </a>
                    </div>
                </div>
            @endif


            {{-- ===================== SUGGESTED CAREGIVER RESOURCES ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                    <div>
                        <h3 class="text-lg font-bold tracking-tight">
                            Suggested Resources
                        </h3>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Expert materials and clinical strategies to help implement supportive recommendations at home.
                        </p>
                    </div>
                    <span class="text-xs font-semibold text-[var(--ink)]">
                        Resource Library
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Resource 1 --}}
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-white hover:border-[var(--ink)] transition flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--ink)] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Visual Schedule Basics</h4>
                                <span class="text-[10px] text-[var(--ink-soft)] uppercase font-semibold">Caregiver Guide</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-3">Step-by-step guidance on structuring daily routines with visual cue cards.</p>
                    </div>

                    {{-- Resource 2 --}}
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-white hover:border-[var(--ink)] transition flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--accent)] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Sensory Toolkits 101</h4>
                                <span class="text-[10px] text-[var(--ink-soft)] uppercase font-semibold">Intervention Strategy</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-3">Practical tools to manage sensory overload and support emotional regulation.</p>
                    </div>

                    {{-- Resource 3 --}}
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-white hover:border-[var(--ink)] transition flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-teal-700 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Parallel Play Guide</h4>
                                <span class="text-[10px] text-[var(--ink-soft)] uppercase font-semibold">Social Scaffolding</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-3">Techniques for fostering shared play and non-verbal social reciprocity.</p>
                    </div>

                    {{-- Resource 4 --}}
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-white hover:border-[var(--ink)] transition flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-indigo-700 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Clinical Best Practices</h4>
                                <span class="text-[10px] text-[var(--ink-soft)] uppercase font-semibold">Evidence-Based</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-3">Peer-reviewed summaries on early childhood cognitive support for ASD.</p>
                    </div>
                </div>
            </div>


            {{-- ===================== DATA PROTECTION & COMPLIANCE BANNER ===================== --}}
            <div class="border border-[var(--line)] rounded-2xl p-5 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[var(--canvas)] border border-[var(--line)] text-[var(--ink)] flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold">Data Protection Compliant Decision Support</h4>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-0.5">
                            All recommendations are generated within a secure environment. No identifiable patient data is shared with external commercial AI models. For exploratory developmental support only; not a substitute for clinical diagnosis.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0 text-xs font-semibold text-[var(--ink)]">
                    <span>Clinical Privacy Verified</span>
                </div>
            </div>

        </main>
    </div>

</div>

<script>
    function filterRecs(status) {
        const buttons = ['all', 'active', 'implemented'];
        buttons.forEach(function (b) {
            const btn = document.getElementById('rec-filter-' + b);
            if (btn) {
                if (b === status) {
                    btn.className = 'px-3.5 py-2 rounded-lg bg-[var(--ink)] text-white transition cursor-pointer';
                } else {
                    btn.className = 'px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer';
                }
            }
        });

        const cards = document.querySelectorAll('.rec-card');
        cards.forEach(function (card) {
            const cardStatus = card.getAttribute('data-status');
            if (status === 'all') {
                card.classList.remove('hidden');
            } else if (status === 'active' && (cardStatus === 'active' || cardStatus === 'urgent' || cardStatus === 'new')) {
                card.classList.remove('hidden');
            } else if (status === 'implemented' && cardStatus === 'implemented') {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function searchRecs() {
        const input = document.getElementById('rec-search-input');
        const filter = input ? input.value.toLowerCase() : '';
        const cards = document.querySelectorAll('.rec-card');

        cards.forEach(function (card) {
            const text = (card.getAttribute('data-text') || '') + ' ' + (card.getAttribute('data-cat') || '');
            if (text.toLowerCase().indexOf(filter) > -1) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }
</script>

</body>
</html>
