{{-- resources/views/caregiver/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AutiSync') }} — Caregiver Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
        html,body{
            font-family:'Inter',system-ui,-apple-system,sans-serif;
            background:var(--canvas);
            color:var(--ink);
            -webkit-font-smoothing:antialiased;
        }
        .skel{
            background:linear-gradient(90deg,#f1ebe0 25%,#f7f2e9 37%,#f1ebe0 63%);
            background-size:400% 100%;
            animation:shimmer 1.6s infinite linear;
            border-radius:.5rem;
        }
        @keyframes shimmer{0%{background-position:100% 0}100%{background-position:0 0}}

        .eyebrow{ font-size:11px; letter-spacing:.06em; color:var(--ink-soft); font-weight:600; }
        .scrollbar-thin::-webkit-scrollbar{ width:6px; height:6px; }
        .scrollbar-thin::-webkit-scrollbar-thumb{ background:#e6dfd4; border-radius:999px; }

        .navlink{ display:flex; align-items:center; gap:10px; padding:8px 12px; border-radius:8px; font-size:14px; color:#5b574f; font-weight:500; }
        .navlink:hover{ background:#f3ede3; color:var(--ink); }
        .navlink.active{ background:var(--ink); color:#fff; }

        button:focus-visible, a:focus-visible, input:focus-visible { outline:2px solid var(--ink); outline-offset:2px; }
    </style>
</head>
<body class="min-h-screen">
@php
    $caregiver = auth()->user();
    $userAvatarUrl = $caregiver?->profilePictureUrl();

    $children = $children ?? $caregiver->children()->with([
        'behaviouralRecords' => fn ($q) => $q->latest('observation_date')->latest('id'),
        'predictions' => fn ($q) => $q->latest('date_generated')->latest('id'),
        'recommendations' => fn ($q) => $q->latest('date_generated')->latest('id'),
        'therapyGoals.milestones',
    ])->latest()->get();

    $childIds = $children->pluck('id');

    $recentRecords = $recentRecords ?? \App\Models\BehaviouralRecord::with('child')
        ->whereIn('child_id', $childIds)
        ->latest('observation_date')
        ->latest('id')
        ->take(8)
        ->get();

    $totalObservationsCount = $totalObservationsCount ?? \App\Models\BehaviouralRecord::whereIn('child_id', $childIds)->count();
    $avgSuccessRate = $avgSuccessRate ?? ($totalObservationsCount > 0 ? round((float) \App\Models\BehaviouralRecord::whereIn('child_id', $childIds)->avg('success_rate'), 1) : null);
    $avgSessionMinutes = $avgSessionMinutes ?? ($totalObservationsCount > 0 ? (int) round((float) \App\Models\BehaviouralRecord::whereIn('child_id', $childIds)->avg('session_duration_minutes')) : null);

    $latestPredictions = $latestPredictions ?? \App\Models\Prediction::with(['child', 'observation'])
        ->whereIn('child_id', $childIds)
        ->latest('date_generated')
        ->latest('id')
        ->take(4)
        ->get();

    $activeRecommendations = $activeRecommendations ?? \App\Models\Recommendation::with('child')
        ->whereIn('child_id', $childIds)
        ->latest('date_generated')
        ->latest('id')
        ->take(5)
        ->get();

    $activeRecommendationsCount = $activeRecommendationsCount ?? \App\Models\Recommendation::whereIn('child_id', $childIds)
        ->whereIn('status', ['active', 'urgent', 'new'])
        ->count();

    $implementedRecommendationsCount = $implementedRecommendationsCount ?? \App\Models\Recommendation::whereIn('child_id', $childIds)
        ->where('status', 'implemented')
        ->count();

    $therapyGoals = $therapyGoals ?? \App\Models\TherapyGoal::with(['child', 'milestones'])
        ->whereIn('child_id', $childIds)
        ->latest()
        ->get();

    $totalMilestonesCount = $totalMilestonesCount ?? \App\Models\Milestone::whereIn('child_id', $childIds)->count();
    $achievedMilestonesCount = $achievedMilestonesCount ?? \App\Models\Milestone::whereIn('child_id', $childIds)->where('achieved_flag', true)->count();

    $firstChild = $children->first();
    $latestObsDate = $recentRecords->first()?->observation_date?->format('M d, Y');
    $caregiverFirstName = explode(' ', trim($caregiver->name))[0] ?? 'Caregiver';
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
            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Caregiver</p>
                <div class="space-y-1">
                    <a href="{{ url('/caregiver/dashboard') }}" class="navlink active">
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

        {{-- Topbar --}}
        <header class="h-16 flex items-center gap-4 px-4 sm:px-8 border-b border-[var(--line)] bg-white sticky top-0 z-10">
            <p class="hidden sm:block font-semibold text-[15px]">Dashboard</p>

            <div class="flex-1 max-w-md ml-0 sm:ml-6">
                <label class="relative block">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input
                        type="text"
                        id="caregiver-dashboard-search"
                        oninput="filterDashboardActivity(this.value)"
                        placeholder="Search child profiles or observations..."
                        class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-3 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:bg-white focus:outline-none"
                    >
                </label>
            </div>

            <div class="ml-auto flex items-center gap-3">
                {{-- Notifications --}}
                <button
                    type="button"
                    class="relative w-9 h-9 rounded-lg border border-[var(--line)] flex items-center justify-center"
                    aria-label="Notifications">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 8a6 6 0 1112 0c0 5 2 6 2 6H4s2-1 2-6z"/>
                        <path d="M10 21a2 2 0 004 0"/>
                    </svg>
                </button>

                {{-- User Information --}}
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 hover:opacity-90 transition" title="Profile Settings">
                    <div class="w-8 h-8 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold overflow-hidden">
                        @if ($userAvatarUrl)
                            <img src="{{ $userAvatarUrl }}" alt="{{ $caregiver->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $caregiver->initials() }}
                        @endif
                    </div>

                    <div class="hidden sm:block leading-tight">
                        <p class="text-[13px] font-semibold">{{ $caregiver->name }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">{{ ucfirst($caregiver->role) }}</p>
                    </div>
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="px-4 sm:px-8 py-6 grid grid-cols-1 xl:grid-cols-[1fr_340px] gap-6">

            {{-- ===================== LEFT COLUMN ===================== --}}
            <div class="space-y-6 min-w-0">

                {{-- Greeting Banner --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8 shadow-sm">
                    <span class="inline-block text-[11px] font-semibold tracking-wide bg-[var(--canvas)] border border-[var(--line)] px-2.5 py-1 rounded-full mb-4">
                        AutiSync Dashboard
                    </span>
                    <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight mb-2">
                        Hello, {{ $caregiverFirstName }}!
                    </h1>

                    @if ($children->isNotEmpty() && $totalObservationsCount > 0)
                        <p class="text-[var(--ink-soft)] text-sm max-w-2xl mb-6 leading-relaxed">
                            You’re currently following progress for <strong class="text-[var(--ink)]">{{ $children->count() }} {{ \Illuminate\Support\Str::plural('child', $children->count()) }}</strong>. You've shared <strong class="text-[var(--ink)]">{{ $totalObservationsCount }} {{ \Illuminate\Support\Str::plural('update', $totalObservationsCount) }}</strong> so far{{ $latestObsDate ? ', with your last note added on ' : '.' }}@if ($latestObsDate)<strong class="text-[var(--ink)]">{{ $latestObsDate }}</strong>.@endif Keep up the great work!
                        </p>
                    @elseif ($children->isNotEmpty())
                        <p class="text-[var(--ink-soft)] text-sm max-w-2xl mb-6 leading-relaxed">
                            You’re currently following progress for <strong class="text-[var(--ink)]">{{ $children->count() }} {{ \Illuminate\Support\Str::plural('child', $children->count()) }}</strong> (<strong class="text-[var(--ink)]">{{ $children->pluck('first_name')->join(', ', ' and ') }}</strong>). Share your first home update whenever you are ready — every small note helps!
                        </p>
                    @else
                        <p class="text-[var(--ink-soft)] text-sm max-w-2xl mb-6 leading-relaxed">
                            Welcome to your dashboard! Add your child's profile and share your first home update to start following their progress here.
                        </p>
                    @endif

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('behavioural-records.create') }}" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition shadow-sm">
                            <span class="text-base leading-none">+</span> Log Observation
                        </a>
                    </div>
                </section>

                {{-- Easy-English Summary Strip --}}
                <section class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white border border-[var(--line)] rounded-2xl p-4">
                        <p class="eyebrow uppercase mb-1">Children You’re Following</p>
                        <p class="text-2xl font-extrabold tracking-tight">{{ $children->count() }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-0.5">
                            {{ $children->isNotEmpty() ? $children->pluck('first_name')->join(', ') : 'No child added yet' }}
                        </p>
                    </div>

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-4">
                        <p class="eyebrow uppercase mb-1">Updates Shared</p>
                        <p class="text-2xl font-extrabold tracking-tight">{{ $totalObservationsCount }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-0.5">
                            {{ $avgSessionMinutes !== null ? 'About ' . $avgSessionMinutes . ' mins each time' : 'No notes shared yet' }}
                        </p>
                    </div>

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-4">
                        <p class="eyebrow uppercase mb-1">How Activities Go</p>
                        <p class="text-2xl font-extrabold tracking-tight">
                            {{ $avgSuccessRate !== null ? number_format($avgSuccessRate, 0) . '%' : '—' }}
                        </p>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-0.5">Average comfortable completion</p>
                    </div>

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-4">
                        <p class="eyebrow uppercase mb-1">Home Ideas to Try</p>
                        <p class="text-2xl font-extrabold tracking-tight text-[var(--accent)]">{{ $activeRecommendationsCount }}</p>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-0.5">
                            {{ $implementedRecommendationsCount }} already practised at home
                        </p>
                    </div>
                </section>

                {{-- Child Profiles --}}
                <section>
                    <div class="flex items-end justify-between mb-3">
                        <div>
                            <h2 class="text-lg font-bold tracking-tight">Child Profiles</h2>
                            <p class="text-[13px] text-[var(--ink-soft)]">
                                Select a child to view their profile, session logs, and developmental support activities.
                            </p>
                        </div>

                        <a
                            href="{{ route('children.create') }}"
                            class="hidden sm:inline-flex items-center gap-1.5 text-[13px] font-semibold border border-[var(--accent-soft)] text-[var(--accent)] bg-white px-3.5 py-2 rounded-lg hover:bg-[var(--accent-tint)] transition"
                        >
                            <span class="text-base leading-none">+</span>
                            Add Profile
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($children as $child)
                            @php
                                $childAvatarUrl = $child->profilePictureUrl();
                                $childObsCount = $child->behaviouralRecords->count();
                                $childAvgSuccess = $childObsCount > 0 ? round((float) $child->behaviouralRecords->avg('success_rate'), 1) : null;
                                $childLatestPrediction = $child->predictions->first();
                                $childIndicatorLabel = $childLatestPrediction?->caregiverIndicatorLabel();
                                $childGoals = $child->therapyGoals;
                                $childMilestones = $childGoals->flatMap->milestones;
                                $childProgressPct = $childMilestones->isNotEmpty()
                                    ? (int) round(($childMilestones->where('achieved_flag', true)->count() / $childMilestones->count()) * 100)
                                    : ($childAvgSuccess !== null ? (int) min(100, max(10, round($childAvgSuccess))) : 0);
                            @endphp

                            <a
                                href="{{ route('children.show', $child) }}"
                                data-dashboard-searchable="{{ strtolower($child->first_name . ' ' . ($childIndicatorLabel ?? '')) }}"
                                class="block bg-white border border-[var(--line)] rounded-2xl p-5 hover:border-[var(--accent-soft)] hover:shadow-sm transition"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 shrink-0 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center overflow-hidden">
                                            @if ($childAvatarUrl)
                                                <img src="{{ $childAvatarUrl }}" alt="{{ $child->first_name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-5 h-5 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="8" r="4"/>
                                                    <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <div>
                                            <h3 class="text-[15px] font-bold tracking-tight">
                                                {{ $child->first_name }}
                                            </h3>
                                            <p class="text-[12px] text-[var(--ink-soft)]">
                                                {{ $child->age_in_months >= 12
                                                    ? floor($child->age_in_months / 12) . ' years' .
                                                      ($child->age_in_months % 12 > 0
                                                          ? ', ' . ($child->age_in_months % 12) . ' months'
                                                          : '')
                                                    : $child->age_in_months . ' months'
                                                }}
                                                · {{ $childObsCount }} {{ \Illuminate\Support\Str::plural('session', $childObsCount) }}
                                            </p>
                                        </div>
                                    </div>

                                    @if ($childLatestPrediction)
                                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap {{ (string) $childLatestPrediction->predicted_outcome === '0' ? 'bg-[#ecf7f3] text-[var(--ink)] border border-[#c5e6dc]' : 'bg-[var(--accent-tint)] text-[var(--accent)] border border-[var(--accent-soft)]' }}">
                                            {{ $childIndicatorLabel }}
                                        </span>
                                    @elseif($child->diagnosis_confirmed)
                                        <span class="text-[10px] font-semibold bg-[var(--ink)] text-white px-2 py-1 rounded-full whitespace-nowrap">
                                            ASD Confirmed
                                        </span>
                                    @endif
                                </div>

                                {{-- Live Progress Bar --}}
                                <div class="mt-5">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[12px] text-[var(--ink-soft)]">
                                            {{ $childMilestones->isNotEmpty() ? 'Steps Completed' : 'How Activities Are Going' }}
                                        </span>
                                        <span class="text-[12px] font-bold text-[var(--ink)]">
                                            {{ $childObsCount > 0 || $childMilestones->isNotEmpty() ? $childProgressPct . '%' : 'No notes yet' }}
                                        </span>
                                    </div>

                                    <div class="h-2 w-full rounded-full bg-[var(--canvas)] border border-[var(--line)] overflow-hidden">
                                        <div class="h-full rounded-full bg-[var(--ink)] transition-all" style="width: {{ $childProgressPct }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-[var(--line)] flex items-center justify-between">
                                    <span class="text-[11px] uppercase tracking-wide text-[var(--accent)] font-semibold">
                                        Open Child Profile
                                    </span>
                                    <svg class="w-4 h-4 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M5 12h14"/>
                                        <path d="M13 6l6 6-6 6"/>
                                    </svg>
                                </div>
                            </a>
                        @empty
                            <div class="sm:col-span-2 border border-dashed border-[var(--accent-soft)] rounded-2xl bg-white p-8 text-center">
                                <div class="w-11 h-11 mx-auto rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="8" r="4"/>
                                        <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold mb-1">No child profiles yet</p>
                                <p class="text-[13px] text-[var(--ink-soft)] mb-4">
                                    Add a profile to start tracking observations and progress.
                                </p>
                                <a
                                    href="{{ route('children.create') }}"
                                    class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition"
                                >
                                    <span class="text-base leading-none">+</span>
                                    Add Profile
                                </a>
                            </div>
                        @endforelse

                        @if($children->isNotEmpty())
                            <a
                                href="{{ route('children.create') }}"
                                class="min-h-[190px] border border-dashed border-[var(--accent-soft)] rounded-2xl bg-white p-8 flex flex-col items-center justify-center text-center hover:border-[var(--accent)] hover:bg-[var(--accent-tint)] transition"
                            >
                                <div class="w-11 h-11 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center mb-3">
                                    <span class="text-xl text-[var(--accent)]">+</span>
                                </div>
                                <p class="text-sm font-semibold mb-1">Add Profile</p>
                                <p class="text-[13px] text-[var(--ink-soft)]">Add another child profile</p>
                            </a>
                        @endif
                    </div>
                </section>

                {{-- Engagement Trends & Recent Behavioural Activity --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
                        <div>
                            <h2 class="text-lg font-bold tracking-tight">Recent Home Updates</h2>
                            <p class="text-[13px] text-[var(--ink-soft)]">
                                A simple look at how your recent daily activities and play sessions went at home.
                            </p>
                        </div>
                        <a href="{{ route('behavioural-records.index') }}" class="text-xs font-bold text-[var(--accent)] hover:underline">
                            See All Notes →
                        </a>
                    </div>

                    @if ($recentRecords->isNotEmpty())
                        {{-- Visual Bar Chart of Recent Observations --}}
                        <div class="bg-[var(--canvas)] border border-[var(--line)] rounded-xl p-4 mb-5">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-[var(--ink)]">How Recent Sessions Went</span>
                                <span class="text-[11px] text-[var(--ink-soft)]">Last {{ $recentRecords->count() }} {{ \Illuminate\Support\Str::plural('update', $recentRecords->count()) }}</span>
                            </div>

                            <div class="grid grid-cols-{{ min(8, max(3, $recentRecords->count())) }} gap-3 items-end h-36 pt-4 px-2">
                                @foreach ($recentRecords->reverse()->values() as $obs)
                                    @php
                                        $barHeightPct = max(14, min(100, (int) round((float) $obs->success_rate)));
                                    @endphp
                                    <div class="flex flex-col items-center justify-end h-full group">
                                        <span class="text-[10px] font-bold text-[var(--ink)] mb-1">
                                            {{ number_format((float) $obs->success_rate, 0) }}%
                                        </span>
                                        <div
                                            class="w-full max-w-[38px] rounded-t-lg transition-all {{ $obs->success_rate >= 70 ? 'bg-[var(--ink)]' : 'bg-[var(--accent)]' }}"
                                            style="height: {{ $barHeightPct }}%"
                                            title="{{ $obs->child?->first_name }} · {{ $obs->observation_date?->format('M d, Y') }} ({{ number_format((float) $obs->success_rate, 1) }}%)"
                                        ></div>
                                        <span class="text-[10px] text-[var(--ink-soft)] mt-1.5 truncate max-w-full">
                                            {{ $obs->observation_date?->format('M d') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Recent Activity Feed --}}
                        <div class="space-y-2.5">
                            @foreach ($recentRecords->take(4) as $obs)
                                <div
                                    data-dashboard-searchable="{{ strtolower(($obs->child?->first_name ?? '') . ' ' . ($obs->notes ?? '') . ' ' . ($obs->observation_date?->format('M d Y') ?? '')) }}"
                                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl border border-[var(--line)] hover:bg-[var(--canvas)]/60 transition"
                                >
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-xs font-extrabold text-[var(--ink)]">
                                                {{ $obs->child?->first_name ?? 'Child' }}
                                            </span>
                                            <span class="text-[11px] text-[var(--ink-soft)]">
                                                · {{ $obs->observation_date?->format('M d, Y') }}
                                            </span>
                                            <span class="text-[10px] font-semibold bg-[var(--canvas)] border border-[var(--line)] px-2 py-0.5 rounded-full">
                                                {{ $obs->session_duration_minutes }} mins
                                            </span>
                                        </div>
                                        <p class="text-xs text-[var(--ink-soft)] mt-1 truncate">
                                            Comm: <strong class="text-[var(--ink)]">{{ $obs->communication_score }}/10</strong> ·
                                            Social: <strong class="text-[var(--ink)]">{{ $obs->social_interaction_score }}/10</strong> ·
                                            Engagement: <strong class="text-[var(--ink)]">{{ $obs->engagement_level }}/10</strong>
                                            @if ($obs->notes)
                                                — "{{ \Illuminate\Support\Str::limit($obs->notes, 60) }}"
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg {{ $obs->success_rate >= 70 ? 'bg-[#ecf7f3] text-[var(--ink)]' : 'bg-[var(--accent-tint)] text-[var(--accent)]' }}">
                                            {{ number_format((float) $obs->success_rate, 0) }}% success
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-48 rounded-xl bg-[var(--canvas)] border border-dashed border-[var(--line)] flex flex-col items-center justify-center text-center p-6">
                            <p class="text-sm font-semibold text-[var(--ink)] mb-1">
                                No observation sessions logged yet
                            </p>
                            <p class="text-xs text-[var(--ink-soft)] mb-3 max-w-sm">
                                Once you log your first behavioural observation session, your child's engagement trend and recent activity will appear here.
                            </p>
                            <a href="{{ route('behavioural-records.create') }}" class="text-xs font-bold text-[var(--accent)] hover:underline">
                                + Log First Observation →
                            </a>
                        </div>
                    @endif
                </section>
            </div>

            {{-- ===================== RIGHT COLUMN ===================== --}}
            <div class="space-y-6 min-w-0">

                {{-- Priority Alerts & Recommended Next Steps --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-[15px] font-bold tracking-tight">Priority Alerts &amp; Focus</h2>
                        @if ($activeRecommendationsCount > 0)
                            <span class="text-[10px] font-bold bg-[var(--accent-tint)] text-[var(--accent)] border border-[var(--accent-soft)] px-2 py-0.5 rounded-full">
                                {{ $activeRecommendationsCount }} Active
                            </span>
                        @endif
                    </div>

                    @php
                        $supportPredictions = $latestPredictions->where('predicted_outcome', '1');
                    @endphp

                    @if ($supportPredictions->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($supportPredictions->take(2) as $flaggedPred)
                                <div class="border border-[var(--accent-soft)] bg-[var(--accent-tint)]/60 rounded-xl p-3.5">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-bold text-[var(--ink)]">{{ $flaggedPred->child?->first_name }}</span>
                                        <span class="text-[10px] font-bold text-[var(--accent)] uppercase">May Need Extra Support</span>
                                    </div>
                                    <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                        Recent observations suggest trying structured home support activities and discussing progress with your clinician.
                                    </p>
                                    @if ($flaggedPred->child)
                                        <a href="{{ route('children.recommendations', $flaggedPred->child) }}" class="inline-block text-[11px] font-bold text-[var(--accent)] hover:underline mt-2">
                                            Open Home Activity Guide →
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif ($activeRecommendations->isNotEmpty())
                        <div class="space-y-2.5">
                            @foreach ($activeRecommendations->take(2) as $rec)
                                <div class="border border-[var(--line)] bg-[var(--canvas)]/70 rounded-xl p-3.5">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-[11px] font-bold text-[var(--ink)]">{{ $rec->category }}</span>
                                        <span class="text-[10px] text-[var(--ink-soft)]">{{ $rec->child?->first_name }}</span>
                                    </div>
                                    <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($rec->recommendation_text, 110) }}
                                    </p>
                                    @if ($rec->child)
                                        <a href="{{ route('children.recommendations', $rec->child) }}" class="inline-block text-[11px] font-bold text-[var(--ink)] hover:underline mt-1.5">
                                            View Activity Steps →
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="border border-dashed border-[var(--accent-soft)] rounded-xl p-5 text-center">
                            <div class="w-9 h-9 mx-auto rounded-full bg-[var(--accent-tint)] flex items-center justify-center mb-2.5">
                                <svg class="w-4 h-4 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.3 3.9L2.7 17a2 2 0 001.7 3h15.2a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
                            </div>
                            <p class="text-sm font-semibold">No alerts right now</p>
                            <p class="text-[12.5px] text-[var(--ink-soft)] mt-1">Flagged patterns and home activity reminders will show up here.</p>
                        </div>
                    @endif
                </section>

                {{-- Developmental Insights (Real Predictions) --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <h2 class="text-[15px] font-bold tracking-tight">Developmental Insights</h2>
                        @if ($firstChild)
                            <a href="{{ route('children.predictions-analytics', $firstChild) }}" class="text-[11px] font-bold text-[var(--accent)] hover:underline">
                                Full View →
                            </a>
                        @endif
                    </div>

                    @if ($latestPredictions->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($latestPredictions->take(3) as $pred)
                                @php
                                    $isOnTrack = (string) $pred->predicted_outcome === '0';
                                @endphp
                                <div class="border border-[var(--line)] rounded-xl p-3.5">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <span class="text-xs font-extrabold text-[var(--ink)]">
                                            {{ $pred->child?->first_name ?? 'Child' }}
                                        </span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isOnTrack ? 'bg-[#ecf7f3] text-[var(--ink)]' : 'bg-[var(--accent-tint)] text-[var(--accent)]' }}">
                                            {{ $pred->caregiverIndicatorLabel() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($pred->caregiverIndicatorDescription(), 120) }}
                                    </p>
                                    <div class="mt-2 pt-2 border-t border-[var(--line)] flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                        <span>{{ $pred->date_generated?->format('M d, Y') }}</span>
                                        <span class="font-semibold {{ $pred->validated_by_clinician ? 'text-[var(--ink)]' : 'text-[var(--accent)]' }}">
                                            {{ $pred->validated_by_clinician ? '✓ Clinician Reviewed' : 'Awaiting Clinician Review' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="border border-dashed border-[var(--line)] rounded-xl p-4 text-center bg-[var(--canvas)]/60">
                            <p class="text-xs font-semibold text-[var(--ink)] mb-1">No developmental insights yet</p>
                            <p class="text-[12px] text-[var(--ink-soft)]">
                                Developmental progress indicators generate automatically once observations and specialist inputs are recorded.
                            </p>
                        </div>
                    @endif
                </section>

                {{-- Monthly Goal Status --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-[15px] font-bold tracking-tight">Therapy &amp; Goal Status</h2>
                        @if ($firstChild)
                            <a href="{{ route('children.goals-milestones', $firstChild) }}" class="text-[11px] font-bold text-[var(--ink)] hover:underline">
                                View Goals →
                            </a>
                        @endif
                    </div>

                    @php
                        $goalPct = $totalMilestonesCount > 0
                            ? (int) round(($achievedMilestonesCount / $totalMilestonesCount) * 100)
                            : ($totalObservationsCount > 0 ? min(100, (int) round(($totalObservationsCount / 10) * 100)) : 0);
                    @endphp

                    <div class="h-2.5 w-full rounded-full bg-[var(--canvas)] border border-[var(--line)] overflow-hidden mb-2.5">
                        <div class="h-full rounded-full bg-[var(--ink)] transition-all" style="width: {{ $goalPct }}%"></div>
                    </div>

                    @if ($totalMilestonesCount > 0)
                        <div class="flex items-center justify-between text-[11px] text-[var(--ink-soft)] font-semibold">
                            <span>TARGET: {{ $totalMilestonesCount }} MILESTONES</span>
                            <span class="text-[var(--ink)]">ACHIEVED: {{ $achievedMilestonesCount }} ({{ $goalPct }}%)</span>
                        </div>
                    @else
                        <div class="flex items-center justify-between text-[11px] text-[var(--ink-soft)] font-semibold">
                            <span>TARGET: 10 SESSIONS</span>
                            <span class="text-[var(--ink)]">CURRENT: {{ $totalObservationsCount }} LOGGED</span>
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>
</div>

<script>
    function filterDashboardActivity(query) {
        const normalized = (query || '').trim().toLowerCase();
        document.querySelectorAll('[data-dashboard-searchable]').forEach((el) => {
            const haystack = (el.getAttribute('data-dashboard-searchable') || '').toLowerCase();
            el.style.display = (!normalized || haystack.includes(normalized)) ? '' : 'none';
        });
    }
</script>
</body>
</html>
