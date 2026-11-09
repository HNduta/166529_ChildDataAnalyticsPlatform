<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->first_name }} · Goals &amp; Milestones · AutiSync</title>

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
    $therapyGoals = $therapyGoals ?? $child->therapyGoals()->with(['milestones', 'clinician'])->latest()->get();
    $milestones = $milestones ?? $child->milestones()->with('goal')->latest()->get();
    $activeGoalsCount = $activeGoalsCount ?? $therapyGoals->whereIn('status', ['active', 'in_progress', 'on_track'])->count();
    $completedGoalsCount = $completedGoalsCount ?? $therapyGoals->where('status', 'completed')->count();
    $completedMilestonesCount = $completedMilestonesCount ?? $milestones->where('achieved_flag', true)->count();
    $clinicalValidations = $clinicalValidations ?? $child->predictions()->where('validated_by_clinician', true)->latest('date_generated')->get();
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
                    <input type="text" id="topbar-goal-search" oninput="document.getElementById('goal-search-input').value = this.value; searchGoals();" placeholder="Search goals or milestones..."
                           class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-20 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:bg-white focus:outline-none">
                    <button type="button" onclick="document.getElementById('goal-search-input').value = document.getElementById('topbar-goal-search').value; searchGoals();" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-[var(--ink)] text-white text-xs font-semibold px-2.5 py-1 rounded-md hover:opacity-90 transition">
                        Search
                    </button>
                </div>
            </div>

            {{-- Right side --}}
            <div class="ml-auto flex items-center gap-3">

                <button type="button" class="relative w-9 h-9 rounded-lg border border-[var(--line)] flex items-center justify-center" aria-label="Notifications">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 8a6 6 0 1112 0c0 5 2 6 2 6H4s2-1 2-6z"/><path d="M10 21a2 2 0 004 0"/></svg>
                </button>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 hover:opacity-90 transition" title="Profile Settings">
                    @php
                        $userAvatarUrl = auth()->user()?->profilePictureUrl();
                    @endphp
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

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap">Logout</button>
                </form>

            </div>

        </header>


        {{-- ============================= PAGE CONTENT ============================= --}}
        <main class="px-4 sm:px-8 py-8">

            {{-- ===================== CHILD PROFILE HEADER ===================== --}}
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5 mb-6">

                <div class="flex items-start gap-4">
                    @php
                        $childAvatarUrl = $child->profilePictureUrl();
                    @endphp
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
                       class="px-6 py-3 text-sm font-semibold whitespace-nowrap border-b-2 border-[var(--ink)] text-[var(--ink)]">
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


            {{-- ===================== PAGE HEADING ===================== --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6 pb-6 border-b border-[var(--line)]">
                <div>
                    <p class="eyebrow uppercase mb-1">Developmental Targets</p>
                    <h2 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">Goals &amp; Milestones</h2>
                    <p class="text-[13px] text-[var(--ink-soft)] mt-1 max-w-3xl">
                        Track and manage developmental progress across key clinical domains for {{ $child->first_name }}.
                    </p>
                </div>
            </div>


            {{-- ===================== SUMMARY KPI CARDS ===================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <p class="eyebrow uppercase">Active Goals</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight">{{ str_pad($activeGoalsCount, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">Actively monitored therapy goals</p>
                </div>

                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <p class="eyebrow uppercase">Milestones Completed</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight">{{ str_pad($completedMilestonesCount, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">Verified developmental milestones</p>
                </div>

                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <p class="eyebrow uppercase">Total Targets</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight">{{ str_pad($therapyGoals->count(), 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">Configured clinical domain objectives</p>
                </div>

                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <p class="eyebrow uppercase">Clinical Status</p>
                    <p class="mt-2 text-2xl font-extrabold tracking-tight text-[var(--ink)]">
                        {{ $clinicalValidations->count() > 0 ? 'Validated' : 'Awaiting Review' }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">Authorized clinician oversight</p>
                </div>

            </div>


            {{-- ===================== FILTER BAR ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-4 sm:p-5 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2 overflow-x-auto text-xs font-semibold">
                        <button type="button" onclick="filterGoals('all')" id="filter-btn-all"
                                class="px-3.5 py-2 rounded-lg bg-[var(--ink)] text-white transition cursor-pointer">
                            All Goals ({{ $therapyGoals->count() }})
                        </button>
                        <button type="button" onclick="filterGoals('active')" id="filter-btn-active"
                                class="px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer">
                            Active ({{ $activeGoalsCount }})
                        </button>
                        <button type="button" onclick="filterGoals('completed')" id="filter-btn-completed"
                                class="px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer">
                            Completed ({{ $completedGoalsCount }})
                        </button>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="goal-search-input" onkeyup="searchGoals()" placeholder="Search goals..."
                               class="w-full text-xs rounded-lg border border-[var(--line)] pl-8 pr-3 py-2 bg-[var(--canvas)] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[var(--ink)]">
                        <svg class="w-3.5 h-3.5 text-[var(--ink-soft)] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    </div>
                </div>
            </div>


            {{-- ===================== GOALS CARDS GRID ===================== --}}
            @if($therapyGoals->isNotEmpty())
                <div id="goals-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
                    @foreach($therapyGoals as $goal)
                        @php
                            $goalStatus = strtolower($goal->status ?? 'active');
                            $progressPct = is_numeric($goal->baseline_level) && is_numeric($goal->target_level) && $goal->target_level > 0
                                ? min(100, round(($goal->baseline_level / $goal->target_level) * 100))
                                : ($goalStatus === 'completed' ? 100 : 50);
                        @endphp
                        <div
                            class="goal-card bg-white border border-[var(--line)] rounded-2xl p-5 flex flex-col justify-between"
                            data-status="{{ $goalStatus }}"
                            data-domain="{{ strtolower($goal->goal_domain) }}"
                            data-text="{{ strtolower($goal->goal_description) }}"
                        >
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)]">
                                        {{ $goal->goal_domain }}
                                    </span>
                                    <span class="text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-full uppercase {{ $goalStatus === 'completed' ? 'border border-emerald-300 bg-emerald-50 text-emerald-800' : 'border border-[var(--accent-soft)] bg-[var(--accent-tint)] text-[var(--accent)]' }}">
                                        {{ ucfirst($goal->status ?? 'Active') }}
                                    </span>
                                </div>

                                <h3 class="text-sm font-bold leading-snug">{{ $goal->goal_description }}</h3>

                                {{-- Progress Bar --}}
                                <div class="mt-4">
                                    <div class="flex justify-between text-[11px] font-semibold mb-1.5">
                                        <span class="text-[var(--ink-soft)]">Progress</span>
                                        <span class="text-[var(--ink)]">{{ $progressPct }}%</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-[var(--canvas)] border border-[var(--line)] overflow-hidden">
                                        <div class="h-full bg-[var(--ink)] rounded-full transition-all duration-500" style="width: {{ $progressPct }}%;"></div>
                                    </div>
                                </div>

                                {{-- Milestones Checklist --}}
                                <div class="mt-4 pt-4 border-t border-[var(--line)]">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)] mb-2.5">
                                        Milestones ({{ $goal->milestones->count() }})
                                    </p>
                                    @if($goal->milestones->isNotEmpty())
                                        <div class="space-y-2">
                                            @foreach($goal->milestones as $ms)
                                                <div class="flex items-start gap-2.5 text-[12px]">
                                                    @if($ms->achieved_flag)
                                                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                                        </div>
                                                        <span class="text-[var(--ink-soft)] line-through">{{ $ms->milestone_description }}</span>
                                                    @else
                                                        <div class="w-4 h-4 rounded-full border border-[var(--line)] shrink-0 mt-0.5"></div>
                                                        <span>{{ $ms->milestone_description }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[12px] text-[var(--ink-soft)] italic">No milestones attached yet.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-4 border-t border-[var(--line)] mt-5 flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                <span>Target: {{ $goal->target_date?->format('M d, Y') ?? 'Ongoing' }}</span>
                                <span class="font-semibold">{{ $goal->clinician?->name ?? 'Authorised Clinician' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-10 sm:p-14 text-center mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r=".5"/></svg>
                    </div>
                    <h3 class="text-lg font-bold">No Goals or Milestones Recorded Yet</h3>
                    <p class="text-sm text-[var(--ink-soft)] mt-2 max-w-md mx-auto leading-relaxed">
                        Individualized therapy goals, milestone checklists, and attainment indicators configured by authorized clinicians will appear here for {{ $child->first_name }}.
                    </p>
                    <div class="mt-6 flex items-center justify-center gap-3">
                        <a href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-[var(--ink)] px-4 py-2 text-xs font-semibold text-white hover:opacity-90 transition">
                            Record Observation Session
                        </a>
                        <a href="{{ route('children.show', $child) }}"
                           class="inline-flex items-center gap-2 rounded-lg border border-[var(--line)] bg-white px-4 py-2 text-xs font-semibold text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition">
                            Return to Child Profile
                        </a>
                    </div>
                </div>
            @endif


            {{-- ===================== RECENT CLINICAL VALIDATIONS ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 mb-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-[15px] font-bold tracking-tight">Recent Clinical Validation</h3>
                        <p class="text-[12px] text-[var(--ink-soft)] mt-0.5">Formal validation sign-offs from authorized clinical practitioners.</p>
                    </div>
                </div>

                @if($clinicalValidations->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($clinicalValidations->take(3) as $val)
                            <div class="border border-[var(--line)] rounded-xl p-4 bg-white">
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <span class="text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-full border border-[var(--ink)] bg-[var(--canvas)] text-[var(--ink)] uppercase">Validated</span>
                                    <span class="text-[11px] text-[var(--ink-soft)]">{{ $val->date_generated?->format('M d, Y') }}</span>
                                </div>
                                <h4 class="text-sm font-bold mt-2">{{ $val->caregiverIndicatorLabel() }}</h4>
                                <p class="text-[12px] text-[var(--ink-soft)] mt-1.5 leading-relaxed italic">
                                    "{{ $val->clinician_feedback ?? 'Clinical observation validated against current therapy progress targets.' }}"
                                </p>
                                <div class="pt-3 border-t border-[var(--line)] mt-4 flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                    <span>Model Score: {{ $val->confidence_score !== null ? number_format($val->confidence_score * 100, 1) . '%' : 'Uncalibrated' }}</span>
                                    <span class="font-semibold text-[var(--ink)]">Clinician Reviewed</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="border border-dashed border-[var(--accent-soft)] rounded-xl p-8 text-center bg-[var(--canvas)]">
                        <svg class="w-6 h-6 mx-auto text-[var(--ink-soft)] mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p class="text-sm font-semibold">No Clinical Validations Recorded Yet</p>
                        <p class="text-[12px] text-[var(--ink-soft)] mt-1 max-w-md mx-auto leading-relaxed">
                            Authorized clinicians have not yet submitted formal validation reviews for {{ $child->first_name }}.
                        </p>
                    </div>
                @endif
            </div>


            {{-- ===================== SMART GUIDANCE CARD ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="max-w-2xl">
                        <h3 class="text-[15px] font-bold tracking-tight">Need Guidance on Setting Goals?</h3>
                        <p class="text-[12px] text-[var(--ink-soft)] mt-2 leading-relaxed">
                            The SMART framework ensures therapy goals are <strong>Specific</strong>, <strong>Measurable</strong>, <strong>Achievable</strong>, <strong>Relevant</strong>, and <strong>Time-bound</strong>. Our authorized clinicians collaborate with caregivers to establish clear, personalized milestones tailored to each child's developmental profile.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('behavioural-records.create') }}?child_id={{ $child->id }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-[var(--ink)] text-sm font-semibold px-4 py-2.5 text-white hover:opacity-90 transition">
                            Record New Observation
                        </a>
                    </div>
                </div>
            </div>

        </main>

    </div>

</div>

<script>
    function filterGoals(status) {
        ['all', 'active', 'completed'].forEach(function (b) {
            const btn = document.getElementById('filter-btn-' + b);
            if (!btn) return;
            btn.className = b === status
                ? 'px-3.5 py-2 rounded-lg bg-[var(--ink)] text-white transition cursor-pointer'
                : 'px-3.5 py-2 rounded-lg bg-white border border-[var(--line)] text-[var(--ink-soft)] hover:bg-[var(--canvas)] transition cursor-pointer';
        });

        document.querySelectorAll('.goal-card').forEach(function (card) {
            const s = card.getAttribute('data-status');
            const show = status === 'all'
                || (status === 'active' && ['active', 'in_progress', 'on_track'].includes(s))
                || (status === 'completed' && s === 'completed');
            card.classList.toggle('hidden', !show);
        });
    }

    function searchGoals() {
        const filter = (document.getElementById('goal-search-input')?.value || '').toLowerCase();
        document.querySelectorAll('.goal-card').forEach(function (card) {
            const text = (card.getAttribute('data-text') || '') + ' ' + (card.getAttribute('data-domain') || '');
            card.classList.toggle('hidden', text.toLowerCase().indexOf(filter) === -1);
        });
    }
</script>

</body>
</html>
