<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->first_name }} · Everyday Support Ideas · AutiSync</title>

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
    $userAvatarUrl = auth()->user()?->profilePictureUrl();
    $childAvatarUrl = $child->profilePictureUrl();
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

            {{-- Account Settings --}}
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
                    <input type="text" id="topbar-rec-search" oninput="document.getElementById('rec-search-input').value = this.value; searchRecs();" placeholder="Search home activities..."
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
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 hover:opacity-90 transition" title="Profile Settings">
                    <div class="w-8 h-8 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold overflow-hidden">
                        @if ($userAvatarUrl)
                            <img src="{{ $userAvatarUrl }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            {{ auth()->user()->initials() }}
                        @endif
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <p class="text-[13px] font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </a>

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

            {{-- Flash Status Messages --}}
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 flex items-center gap-2.5" role="status">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- ===================== CHILD PROFILE HEADER ===================== --}}
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5 mb-6">

                <div class="flex items-start gap-4">
                    <div class="w-20 h-20 shrink-0 border-2 border-[var(--accent-soft)] bg-[var(--accent-tint)] rounded-2xl flex items-center justify-center overflow-hidden">
                        @if ($childAvatarUrl)
                            <img src="{{ $childAvatarUrl }}" alt="{{ $child->first_name }} profile picture" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        @endif
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
                    <p class="eyebrow uppercase mb-1">Everyday Home Support Guide</p>
                    <h2 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">Support Recommendations for {{ $child->first_name }}</h2>
                    <p class="text-[13px] text-[var(--ink-soft)] mt-1 max-w-3xl leading-relaxed">
                        Practical, step-by-step activities you can try during everyday routines at home — tailored to {{ $child->first_name }}'s recorded observations and developmental focus areas. Every child grows at their own pace, and even small moments of connection make a real difference.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}"
                       class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-xs font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition">
                        <span>+</span> Log Home Observation Note
                    </a>
                </div>
            </div>


            {{-- ===================== 3 CAREGIVER SUMMARY CARDS ===================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                {{-- Activities to Try --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Activities to Try</span>
                        <div class="w-7 h-7 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center text-[var(--accent)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight">
                        {{ str_pad($activeCount, 2, '0', STR_PAD_LEFT) }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Home support ideas ready to practise
                    </p>
                </div>

                {{-- Part of Our Routine --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Part of Our Routine</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight text-emerald-700">
                        {{ str_pad($implementedCount, 2, '0', STR_PAD_LEFT) }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Activities you have tried and built into daily care
                    </p>
                </div>

                {{-- Recorded Home Observations --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Observations Logged</span>
                        <div class="w-7 h-7 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--ink)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight text-[var(--ink)]">
                        {{ $childTotalRecords }} {{ Str::plural('Note', $childTotalRecords) }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Everyday observation notes guiding {{ $child->first_name }}'s support plan
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
                            All Support Ideas ({{ $recommendations->count() }})
                        </button>
                        <button
                            type="button"
                            onclick="filterRecs('active')"
                            id="rec-filter-active"
                            class="px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer"
                        >
                            Activities to Try ({{ $activeCount }})
                        </button>
                        <button
                            type="button"
                            onclick="filterRecs('implemented')"
                            id="rec-filter-implemented"
                            class="px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer"
                        >
                            Part of Our Routine ({{ $implementedCount }})
                        </button>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <input
                            type="text"
                            id="rec-search-input"
                            onkeyup="searchRecs()"
                            placeholder="Filter by area or activity..."
                            class="w-full text-xs rounded-lg border border-[var(--line)] pl-8 pr-3 py-2 bg-[var(--canvas)] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[var(--ink)]"
                        >
                        <svg class="w-3.5 h-3.5 text-[var(--ink-soft)] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </div>
                </div>
            </div>


            {{-- ===================== RECOMMENDATIONS CARDS ===================== --}}
            @if ($recommendations->isNotEmpty())
                <div id="recs-container" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    @foreach ($recommendations as $rec)
                        @php
                            $status = strtolower($rec->status ?? 'active');
                            $isImplemented = $status === 'implemented';
                            $guide = $rec->caregiverActivityGuide($child->first_name);
                            $searchBlob = strtolower($guide['area'] . ' ' . $rec->recommendation_text . ' ' . $guide['why_it_matters'] . ' ' . implode(' ', $guide['what_you_can_do']));
                        @endphp
                        <article
                            class="rec-card bg-white border border-[var(--line)] rounded-2xl p-6 flex flex-col justify-between hover:border-[var(--ink)] transition shadow-sm"
                            data-status="{{ $status }}"
                            data-cat="{{ strtolower($guide['area']) }}"
                            data-text="{{ $searchBlob }}"
                        >
                            <div class="space-y-4">
                                {{-- Top Badge Row: Area & Practice Status --}}
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">
                                        Area: {{ $guide['area'] }}
                                    </span>

                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full {{ $isImplemented ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-[var(--canvas)] border border-[var(--line)] text-[var(--ink)]' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isImplemented ? 'bg-emerald-500' : 'bg-[var(--accent)]' }}"></span>
                                        {{ $isImplemented ? 'Part of Our Routine' : 'Activity to Try' }}
                                    </span>
                                </div>

                                {{-- Main Activity Summary --}}
                                <div>
                                    <h3 class="text-lg font-extrabold leading-snug text-[var(--ink)]">
                                        {{ $rec->recommendation_text }}
                                    </h3>
                                </div>

                                {{-- 1. Why this matters --}}
                                <div class="p-3.5 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <p class="text-xs font-bold uppercase tracking-wide text-[var(--ink)] mb-1">
                                        Why this matters
                                    </p>
                                    <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                        {{ $guide['why_it_matters'] }}
                                    </p>
                                </div>

                                {{-- 2. What you can do at home --}}
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-[var(--ink)] mb-2">
                                        What you can do at home
                                    </p>
                                    <ul class="space-y-1.5 text-xs text-[var(--ink-soft)] leading-relaxed list-disc list-inside">
                                        @foreach ($guide['what_you_can_do'] as $bullet)
                                            <li>{{ $bullet }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- 3. How to practise it & 4. What to look out for --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="p-3.5 rounded-xl border border-[var(--line)] bg-white">
                                        <p class="text-xs font-bold text-[var(--ink)] mb-1">
                                            How to practise it
                                        </p>
                                        <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                            {{ $guide['how_to_practise'] }}
                                        </p>
                                    </div>

                                    <div class="p-3.5 rounded-xl border border-[var(--line)] bg-white">
                                        <p class="text-xs font-bold text-[var(--ink)] mb-1">
                                            What to look out for
                                        </p>
                                        <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                            {{ $guide['what_to_look_out_for'] }}
                                        </p>
                                    </div>
                                </div>

                                {{-- 5. Helpful reminder & When to seek guidance --}}
                                <div class="p-3.5 rounded-xl bg-[var(--accent-tint)]/60 border border-[var(--accent-soft)] text-xs space-y-1.5">
                                    <p class="text-[var(--ink)] leading-relaxed">
                                        <strong class="font-bold">Helpful reminder:</strong> {{ $guide['helpful_reminder'] }}
                                    </p>
                                    <p class="text-[var(--ink-soft)] leading-relaxed">
                                        <strong class="font-semibold text-[var(--ink)]">When to talk with your clinician:</strong> {{ $guide['when_to_seek_guidance'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Progress Tracking Footer --}}
                            <div class="pt-4 border-t border-[var(--line)] mt-5 flex flex-wrap items-center justify-between gap-3">
                                <form method="POST" action="{{ route('children.recommendations.update-status', [$child, $rec]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $isImplemented ? 'active' : 'implemented' }}">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-lg transition {{ $isImplemented ? 'border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink-soft)] hover:text-[var(--ink)]' : 'bg-[var(--ink)] text-white hover:opacity-90' }}"
                                    >
                                        @if ($isImplemented)
                                            <span>Move Back to "To Try" List</span>
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                            <span>Mark as Tried at Home</span>
                                        @endif
                                    </button>
                                </form>

                                <a
                                    href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}"
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-[var(--accent)] hover:underline"
                                >
                                    <span>Record how this went →</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @elseif ($childTotalRecords > 0)
                {{-- Observation-Guided Home Support Cards (when observations exist before a specialist prediction is run) --}}
                @php
                    $observationGuides = [
                        (new \App\Models\Recommendation([
                            'category' => 'Communication',
                            'recommendation_text' => "Offer two simple visual or gesture choices during {$child->first_name}'s daily snack and story routines.",
                            'status' => 'active',
                        ])),
                        (new \App\Models\Recommendation([
                            'category' => 'Social Interaction',
                            'recommendation_text' => "Spend 10 relaxed minutes in side-by-side play following {$child->first_name}'s favourite toy or interest.",
                            'status' => 'active',
                        ])),
                        (new \App\Models\Recommendation([
                            'category' => 'Calm Routines & Transitions',
                            'recommendation_text' => "Give a gentle 5-minute and 2-minute heads-up before switching from playtime to daily routines.",
                            'status' => 'active',
                        ])),
                        (new \App\Models\Recommendation([
                            'category' => 'Everyday Independence',
                            'recommendation_text' => "Break one daily self-care routine into 3 simple steps and celebrate when {$child->first_name} finishes the last step.",
                            'status' => 'active',
                        ])),
                    ];
                @endphp

                <div class="space-y-4 mb-6">
                    <div class="p-4 rounded-xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-start gap-3 text-xs">
                        <svg class="w-5 h-5 text-[var(--accent)] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="font-bold text-[var(--ink)]">Home Activities Based on {{ $child->first_name }}'s Recorded Observations</p>
                            <p class="mt-0.5 text-[var(--ink-soft)] leading-relaxed">
                                These practical home activities are tailored to the {{ $childTotalRecords }} {{ Str::plural('observation', $childTotalRecords) }} you have logged for {{ $child->first_name }}. They offer gentle ideas for everyday routines and do not replace advice from your child's clinician.
                            </p>
                        </div>
                    </div>

                    <div id="recs-container" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach ($observationGuides as $obsRec)
                            @php
                                $guide = $obsRec->caregiverActivityGuide($child->first_name);
                                $searchBlob = strtolower($guide['area'] . ' ' . $obsRec->recommendation_text . ' ' . $guide['why_it_matters']);
                            @endphp
                            <article
                                class="rec-card bg-white border border-[var(--line)] rounded-2xl p-6 flex flex-col justify-between hover:border-[var(--ink)] transition shadow-sm"
                                data-status="active"
                                data-cat="{{ strtolower($guide['area']) }}"
                                data-text="{{ $searchBlob }}"
                            >
                                <div class="space-y-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">
                                            Area: {{ $guide['area'] }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-[var(--canvas)] border border-[var(--line)] text-[var(--ink)]">
                                            Everyday Home Activity
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-extrabold leading-snug text-[var(--ink)]">
                                        {{ $obsRec->recommendation_text }}
                                    </h3>

                                    <div class="p-3.5 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                        <p class="text-xs font-bold uppercase tracking-wide text-[var(--ink)] mb-1">
                                            Why this matters
                                        </p>
                                        <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                            {{ $guide['why_it_matters'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-[var(--ink)] mb-2">
                                            What you can do at home
                                        </p>
                                        <ul class="space-y-1.5 text-xs text-[var(--ink-soft)] leading-relaxed list-disc list-inside">
                                            @foreach ($guide['what_you_can_do'] as $bullet)
                                                <li>{{ $bullet }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="p-3.5 rounded-xl border border-[var(--line)] bg-white">
                                            <p class="text-xs font-bold text-[var(--ink)] mb-1">
                                                How to practise it
                                            </p>
                                            <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                                {{ $guide['how_to_practise'] }}
                                            </p>
                                        </div>

                                        <div class="p-3.5 rounded-xl border border-[var(--line)] bg-white">
                                            <p class="text-xs font-bold text-[var(--ink)] mb-1">
                                                What to look out for
                                            </p>
                                            <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                                {{ $guide['what_to_look_out_for'] }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="p-3.5 rounded-xl bg-[var(--accent-tint)]/60 border border-[var(--accent-soft)] text-xs space-y-1.5">
                                        <p class="text-[var(--ink)] leading-relaxed">
                                            <strong class="font-bold">Helpful reminder:</strong> {{ $guide['helpful_reminder'] }}
                                        </p>
                                        <p class="text-[var(--ink-soft)] leading-relaxed">
                                            <strong class="font-semibold text-[var(--ink)]">When to talk with your clinician:</strong> {{ $guide['when_to_seek_guidance'] }}
                                        </p>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-[var(--line)] mt-5 flex items-center justify-between text-xs">
                                    <a href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}" class="font-semibold text-[var(--ink)] hover:underline">
                                        + Log how this activity went at home
                                    </a>
                                    <a href="{{ route('children.show', ['child' => $child, 'tab' => 'behavioral-logs']) }}" class="font-semibold text-[var(--accent)] hover:underline">
                                        View Observation Notes →
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Clean Empty State Card --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-10 sm:p-14 text-center mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center mx-auto text-[var(--accent)] mb-4 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 10-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold">
                        No Home Support Ideas Yet for {{ $child->first_name }}
                    </h3>
                    <p class="text-sm text-[var(--ink-soft)] mt-2 max-w-md mx-auto leading-relaxed">
                        Start by logging a short observation about {{ $child->first_name }}'s everyday communication, play, or routines. Practical home activity ideas will appear here automatically.
                    </p>
                    <div class="mt-6 flex items-center justify-center gap-3">
                        <a
                            href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-[var(--ink)] px-4 py-2.5 text-xs font-bold text-white hover:opacity-90 transition"
                        >
                            Record First Home Observation
                        </a>
                    </div>
                </div>
            @endif


            {{-- ===================== EVERYDAY CAREGIVER TIPS BY AREA ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                    <div>
                        <h3 class="text-lg font-bold tracking-tight">
                            Quick Home Guides by Developmental Area
                        </h3>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Simple ways to make daily routines feel calmer, clearer, and more connected for {{ $child->first_name }}.
                        </p>
                    </div>
                    <span class="text-xs font-semibold text-[var(--ink)]">
                        Caregiver Reference
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Guide 1 --}}
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-white flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--ink)] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Picture &amp; Choice Cards</h4>
                                <span class="text-[10px] text-[var(--ink-soft)] uppercase font-semibold">Communication</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-3 leading-relaxed">
                            Hold up two real objects or pictures during meals or play so your child can point, look, or speak to choose.
                        </p>
                    </div>

                    {{-- Guide 2 --}}
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-white flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--accent)] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Calm Transition Warnings</h4>
                                <span class="text-[10px] text-[var(--ink-soft)] uppercase font-semibold">Routines &amp; Comfort</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-3 leading-relaxed">
                            Use a "First–Then" phrase ("First tidy blocks, then snack") and a 2-minute countdown before changing activities.
                        </p>
                    </div>

                    {{-- Guide 3 --}}
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-white flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-teal-700 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Side-by-Side Play</h4>
                                <span class="text-[10px] text-[var(--ink-soft)] uppercase font-semibold">Social Interaction</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-3 leading-relaxed">
                            Join your child's favourite game warmly without pressure, taking gentle turns rolling a ball or stacking blocks.
                        </p>
                    </div>

                    {{-- Guide 4 --}}
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-white flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-emerald-700 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold leading-tight">Small Step Independence</h4>
                                <span class="text-[10px] text-[var(--ink-soft)] uppercase font-semibold">Daily Living</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-3 leading-relaxed">
                            Do the first steps of dressing or handwashing together, and let your child proudly complete the final step.
                        </p>
                    </div>
                </div>
            </div>


            {{-- ===================== SUPPORTIVE DISCLAIMER BANNER ===================== --}}
            <div class="border border-[var(--line)] rounded-2xl p-5 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[var(--canvas)] border border-[var(--line)] text-[var(--ink)] flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold">Supportive Home Guidance — Not a Medical Diagnosis</h4>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-0.5 max-w-2xl leading-relaxed">
                            These recommendations are practical everyday suggestions to support {{ $child->first_name }} at home. They are not a medical diagnosis or a replacement for a qualified clinician, speech therapist, or occupational therapist. Always discuss developmental questions or concerns with your child's specialist team.
                        </p>
                    </div>
                </div>

                <a
                    href="{{ route('children.predictions-analytics', $child) }}"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-[var(--line)] bg-[var(--canvas)] px-4 py-2 text-xs font-semibold text-[var(--ink)] hover:bg-[#efe7dc] transition shrink-0"
                >
                    View Developmental Progress →
                </a>
            </div>

        </main>

    </div>

</div>

<script>
    let currentStatusFilter = 'all';

    function filterRecs(status) {
        currentStatusFilter = status;
        const buttons = {
            all: document.getElementById('rec-filter-all'),
            active: document.getElementById('rec-filter-active'),
            implemented: document.getElementById('rec-filter-implemented')
        };

        Object.keys(buttons).forEach(function (key) {
            if (!buttons[key]) return;
            if (key === status) {
                buttons[key].className = 'px-3.5 py-2 rounded-lg bg-[var(--ink)] text-white transition cursor-pointer';
            } else {
                buttons[key].className = 'px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer';
            }
        });

        applyRecFilters();
    }

    function searchRecs() {
        applyRecFilters();
    }

    function applyRecFilters() {
        const q = (document.getElementById('rec-search-input')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.rec-card');

        cards.forEach(function (card) {
            const cardStatus = (card.getAttribute('data-status') || 'active').toLowerCase();
            const cardText = (card.getAttribute('data-text') || '') + ' ' + (card.textContent || '').toLowerCase();

            let statusMatch = true;
            if (currentStatusFilter === 'active') {
                statusMatch = (cardStatus === 'active' || cardStatus === 'urgent' || cardStatus === 'new');
            } else if (currentStatusFilter === 'implemented') {
                statusMatch = (cardStatus === 'implemented');
            }

            const searchMatch = !q || cardText.indexOf(q) > -1;

            if (statusMatch && searchMatch) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }
</script>

</body>
</html>
