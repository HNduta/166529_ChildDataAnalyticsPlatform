<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->first_name }} · ML Predictions &amp; Analytics · AutiSync</title>

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
    $childTotalRecords = $totalRecords ?? $child->behaviouralRecords()->count();
    $childAvgSuccess = $averageSuccessRate ?? ($childTotalRecords > 0 ? $child->behaviouralRecords()->avg('success_rate') : null);
    $childAvgDuration = $averageDuration ?? ($childTotalRecords > 0 ? $child->behaviouralRecords()->avg('session_duration_minutes') : null);
    $trajectoryRecords = $trajectoryRecords ?? $child->behaviouralRecords()->oldest('observation_date')->oldest('id')->get();
    $latestPrediction = $latestPrediction ?? $child->predictions()->latest('date_generated')->first();
    $clinicalValidations = $clinicalValidations ?? $child->predictions()->where('validated_by_clinician', true)->with('observation')->latest('date_generated')->get();
    $therapyGoals = $therapyGoals ?? $child->therapyGoals()->with('milestones')->get();
    $assessmentInputs = $assessmentInputs ?? ($child->behaviouralRecords()->whereNotNull('ml_assessment_inputs')->latest('observation_date')->first()?->ml_assessment_inputs ?? []);
    $avgSuccessFirst2 = $avgSuccessFirst2 ?? ($child->behaviouralRecords()->oldest('observation_date')->take(2)->count() >= 2 ? $child->behaviouralRecords()->oldest('observation_date')->take(2)->avg('success_rate') : ($child->behaviouralRecords()->count() == 1 ? $child->behaviouralRecords()->first()->success_rate : null));
    $avgCommunication = $avgCommunication ?? $child->behaviouralRecords()->avg('communication_score');
    $avgSocial = $avgSocial ?? $child->behaviouralRecords()->avg('social_interaction_score');
    $avgEngagement = $avgEngagement ?? $child->behaviouralRecords()->avg('engagement_level');
    $avgRepetitive = $avgRepetitive ?? $child->behaviouralRecords()->avg('repetitive_behaviour_score');
    $recommendations = $recommendations ?? $child->recommendations()->with('prediction')->latest('date_generated')->get();
    $latestObservation = $latestObservation ?? $child->behaviouralRecords()->latest('observation_date')->latest('id')->first();
    $latestRecordWithAssessment = $latestRecordWithAssessment ?? $child->behaviouralRecords()->whereNotNull('ml_assessment_inputs')->latest('observation_date')->latest('id')->first();

    $activePredictedClass = isset($mlServiceResult['predicted_class'])
        ? (string) $mlServiceResult['predicted_class']
        : ($latestPrediction ? (string) $latestPrediction->predicted_outcome : null);

    $activeIndicatorLabel = match ($activePredictedClass) {
        '0' => 'On-Track',
        '1' => 'May Need Additional Support',
        default => 'Pending Assessment',
    };

    $activeIndicatorSubtitle = match ($activePredictedClass) {
        '0' => 'Combines Improving and Slow Progress patterns in the developmental support dataset.',
        '1' => 'At-Risk indicator — combines Plateau and Regression patterns that may benefit from targeted support.',
        default => 'Automatic indicator will be generated once complete specialist assessment inputs are recorded.',
    };

    $activeIndicatorExplanation = match ($activePredictedClass) {
        '0' => 'Your child\'s recorded sessions and specialist assessment inputs currently align with steady or gradual developmental progress. Importantly, an On-Track indicator is not proof that a child has no developmental difficulties—continued observation and support remain important.',
        '1' => 'Your child\'s recorded sessions and specialist assessment inputs suggest areas where progress may have plateaued or where additional structured support could help. Importantly, this indicator is not a medical diagnosis—it is an exploratory guide to discuss with your child\'s clinician.',
        default => 'No developmental progress indicator has been generated yet because specialist clinician assessment inputs are still pending.',
    };

    $class0Score = isset($mlServiceResult['class_scores']['0'])
        ? (float) $mlServiceResult['class_scores']['0']
        : ($latestPrediction && $latestPrediction->confidence_score !== null
            ? ($activePredictedClass === '0' ? (float) $latestPrediction->confidence_score : max(0, 1 - (float) $latestPrediction->confidence_score))
            : null);
    $class1Score = isset($mlServiceResult['class_scores']['1'])
        ? (float) $mlServiceResult['class_scores']['1']
        : ($latestPrediction && $latestPrediction->confidence_score !== null
            ? ($activePredictedClass === '1' ? (float) $latestPrediction->confidence_score : max(0, 1 - (float) $latestPrediction->confidence_score))
            : null);
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
                    <input type="text" id="topbar-analytics-search" oninput="filterAnalyticsPage()" placeholder="Search features, predictions..."
                           class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-20 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:bg-white focus:outline-none">
                    <button type="button" onclick="filterAnalyticsPage()" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-[var(--ink)] text-white text-xs font-semibold px-2.5 py-1 rounded-md hover:opacity-90 transition">
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
                       class="px-6 py-3 text-sm text-[var(--ink-soft)] whitespace-nowrap border-b-2 border-transparent hover:text-[var(--accent)]">
                        Recommendations
                    </a>
                    <a href="{{ route('children.predictions-analytics', $child) }}"
                       class="px-6 py-3 text-sm font-semibold whitespace-nowrap border-b-2 border-[var(--ink)] text-[var(--ink)]">
                        ML Predictions and Analytics
                    </a>
                </div>
            </div>


            {{-- ===================== DASHBOARD HEADER ===================== --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6 pb-6 border-b border-[var(--line)]">
                <div>
                    <p class="eyebrow uppercase mb-1">Caregiver &amp; Clinical Intelligence</p>
                    <h2 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">Behavioral Intelligence Dashboard</h2>
                    <p class="text-[13px] text-[var(--ink-soft)] mt-1 max-w-3xl">
                        Aggregated behavior patterns, prediction history and developmental progress tracking for {{ $child->first_name }}. Data-driven insights to support personalized clinical care.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 border border-[var(--line)] bg-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-[var(--canvas)] transition"
                    >
                        <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Report
                    </button>

                    <div class="inline-flex items-center gap-2 border border-[var(--line)] bg-white text-xs font-semibold px-3 py-2 rounded-lg text-[var(--ink-soft)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        {{ $childTotalRecords }} {{ Str::plural('Session', $childTotalRecords) }} Logged
                    </div>
                </div>
            </div>


            {{-- ===================== 4 KPI SUMMARY CARDS ===================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Observation Density --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Observation Density</span>
                        @if($childTotalRecords > 0)
                            <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                Active Tracking
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                0 Logged
                            </span>
                        @endif
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight">
                        {{ number_format($childTotalRecords) }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Recorded behavioral {{ Str::plural('session', $childTotalRecords) }} for {{ $child->first_name }}
                    </p>
                </div>

                {{-- Active Domains --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Active Domains</span>
                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            4 Tracked
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight">
                        4
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Communication, Social, Engagement, Regulation
                    </p>
                </div>

                {{-- Prediction Status / Developmental Progress Indicator --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Prediction Status</span>
                        @if($latestPrediction)
                            <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                {{ $latestPrediction->confidence_score !== null ? number_format($latestPrediction->confidence_score * 100, 1) . '%' : 'Updated' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                Pending
                            </span>
                        @endif
                    </div>
                    <p class="mt-3 text-xl sm:text-2xl font-extrabold tracking-tight text-[var(--ink)]">
                        {{ $latestPrediction ? $latestPrediction->caregiverIndicatorLabel() : 'Pending Assessment' }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        {{ $latestPrediction ? 'Updated ' . ($latestPrediction->date_generated?->format('M d, Y') ?? 'automatically') : 'Awaiting clinician assessment' }}
                    </p>
                </div>

                {{-- Average Session Duration --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span class="eyebrow uppercase">Average Session</span>
                        @if($childAvgSuccess !== null)
                            <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                {{ number_format($childAvgSuccess, 0) }}% success
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-[var(--ink-soft)] bg-[var(--canvas)] px-2 py-0.5 rounded-full border border-[var(--line)]">
                                —
                            </span>
                        @endif
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight">
                        {{ $childAvgDuration !== null ? round($childAvgDuration) . 'm' : '—' }}
                    </p>
                    <p class="mt-1 text-xs text-[var(--ink-soft)]">
                        Average observation length
                    </p>
                </div>
            </div>


            {{-- ===================== AUTOMATIC DEVELOPMENTAL PROGRESS INDICATOR ===================== --}}
            <div id="prediction-loading-indicator" class="hidden mb-6 p-4 rounded-xl bg-[var(--canvas)] border border-[var(--line)] text-xs font-semibold text-[var(--ink)] flex items-center gap-3" role="status" aria-live="polite">
                <svg class="w-4 h-4 animate-spin text-[var(--ink)]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>Checking for updated assessment information and refreshing developmental insights...</span>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(!empty($mlServiceError))
                <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3" role="alert">
                    <svg class="w-4 h-4 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <div>
                        <p class="font-bold">Developmental Progress Analysis Temporarily Unavailable</p>
                        <p class="mt-0.5 text-amber-800">{{ $mlServiceError }}</p>
                    </div>
                </div>
            @endif

            {{-- QUESTION 1 & 5: WHAT DOES THE LATEST ASSESSMENT INDICATE & WHEN WAS IT UPDATED? --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <p class="eyebrow uppercase mb-1">1. What Does the Latest Assessment Indicate?</p>
                        <h3 class="text-lg font-bold tracking-tight">Developmental Progress Indicator</h3>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Generated automatically when complete observation and clinician assessment records are available for {{ $child->first_name }}.
                        </p>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--canvas)] border border-[var(--line)] text-xs font-semibold text-[var(--ink-soft)] shrink-0">
                        <span class="w-2 h-2 rounded-full {{ $activePredictedClass !== null ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                        <span>{{ $activePredictedClass !== null ? 'Automatic Indicator Active' : 'Awaiting Complete Assessment' }}</span>
                    </div>
                </div>

                @if($activePredictedClass !== null)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
                        {{-- Main Developmental Progress Indicator --}}
                        <div class="p-5 rounded-xl bg-[var(--canvas)] border border-[var(--line)] flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="eyebrow uppercase">Developmental Progress Indicator</span>
                                    @if($activePredictedClass === '0')
                                        <span class="text-[10px] font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 uppercase">
                                            Steady / Gradual Progress
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 uppercase">
                                            Targeted Support Advised
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-3 text-2xl sm:text-3xl font-extrabold {{ $activePredictedClass === '0' ? 'text-[var(--ink)]' : 'text-[var(--accent)]' }}">
                                    {{ $activeIndicatorLabel }}
                                </p>
                                <p class="mt-1.5 text-xs font-medium text-[var(--ink-soft)] leading-relaxed">
                                    {{ $activeIndicatorSubtitle }}
                                </p>
                                <p class="mt-2.5 text-xs text-[var(--ink-soft)] leading-relaxed">
                                    {{ $activeIndicatorExplanation }}
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-[var(--line)] flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                <span>5. Last Updated: <strong>{{ $latestPrediction?->date_generated?->format('M d, Y H:i') ?? now()->format('M d, Y H:i') }}</strong></span>
                                <span>{{ $latestPrediction?->validated_by_clinician ? 'Clinician Validated' : 'Pending Clinician Review' }}</span>
                            </div>
                        </div>

                        {{-- Simplified Model Assessment Scores --}}
                        <div class="p-5 rounded-xl bg-[var(--canvas)] border border-[var(--line)] flex flex-col justify-between">
                            <div>
                                <span class="eyebrow uppercase">Model Assessment Scores</span>
                                <p class="text-xs text-[var(--ink-soft)] mt-1">
                                    Relative alignment with each support pattern based on recorded sessions and assessment inputs:
                                </p>
                                <div class="mt-3.5 space-y-3">
                                    <div>
                                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                            <span>On-Track Alignment Score</span>
                                            <span>{{ $class0Score !== null ? number_format($class0Score * 100, 1) . '%' : '—' }}</span>
                                        </div>
                                        <div class="w-full h-2 bg-[var(--line)] rounded-full overflow-hidden">
                                            <div class="h-full bg-[var(--ink)] rounded-full" style="width: {{ $class0Score !== null ? round($class0Score * 100) : 0 }}%;"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                            <span>Additional Support Alignment Score</span>
                                            <span>{{ $class1Score !== null ? number_format($class1Score * 100, 1) . '%' : '—' }}</span>
                                        </div>
                                        <div class="w-full h-2 bg-[var(--line)] rounded-full overflow-hidden">
                                            <div class="h-full bg-[var(--accent)] rounded-full" style="width: {{ $class1Score !== null ? round($class1Score * 100) : 0 }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-4 pt-3 border-t border-[var(--line)] text-[11px] text-[var(--ink-soft)] leading-relaxed">
                                <strong>How to read these scores:</strong> These are uncalibrated model scores, not confirmed clinical probabilities or a percentage guarantee of developmental success.
                            </p>
                        </div>

                        {{-- Caregiver Guidance & Clinician Consultation --}}
                        <div class="p-5 rounded-xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex flex-col justify-between">
                            <div>
                                <span class="eyebrow uppercase text-[var(--accent)]">6. Should You Discuss This with a Clinician?</span>
                                <p class="mt-2 text-xs font-semibold leading-relaxed text-[var(--ink)]">
                                    Yes. Share this progress indicator with {{ $child->first_name }}'s clinician during regular reviews so they can interpret it alongside in-person developmental assessments.
                                </p>
                                <ul class="mt-2.5 space-y-1.5 text-xs text-[var(--ink-soft)]">
                                    <li>&bull; <strong>On-Track</strong> does not mean a child has no developmental difficulties.</li>
                                    <li>&bull; <strong>May Need Additional Support</strong> is an exploratory prompt, not a medical diagnosis.</li>
                                </ul>
                            </div>
                            <div class="mt-4 pt-3 border-t border-[var(--accent-soft)] flex items-center justify-between text-[11px] font-semibold text-[var(--accent)]">
                                <span>Exploratory Decision Support</span>
                                <span>Practitioner Interpretation Required</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-6 rounded-xl bg-[var(--canvas)] border border-dashed border-[var(--line)] text-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-white border border-[var(--line)] flex items-center justify-center mx-auto text-[var(--accent)] mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-bold">Developmental Progress Indicator Pending Clinician Assessment</p>
                        <p class="text-xs text-[var(--ink-soft)] mt-1.5 max-w-xl mx-auto leading-relaxed">
                            Complete specialist assessment inputs and at least two behavioural observation sessions are needed to generate an accurate progress indicator for {{ $child->first_name }}. No indicator is shown until verified data is recorded—once your clinician completes the assessment, the result will appear here automatically.
                        </p>
                    </div>
                @endif

                {{-- Historical Predictions Table --}}
                @if(isset($predictions) && $predictions->isNotEmpty())
                    <div class="mt-5 pt-5 border-t border-[var(--line)]">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[var(--ink-soft)]">Prediction History &amp; Update Timeline ({{ $predictions->count() }})</h4>
                            <span class="text-[11px] text-[var(--ink-soft)]">
                                Latest behavioral observation: {{ $latestObservation?->observation_date?->format('M d, Y') ?? 'None recorded' }}
                            </span>
                        </div>
                        <div class="overflow-x-auto border border-[var(--line)] rounded-xl">
                            <table class="min-w-full text-xs">
                                <thead class="bg-[var(--canvas)]">
                                    <tr class="border-b border-[var(--line)] text-left font-semibold uppercase text-[var(--ink-soft)]">
                                        <th class="py-2.5 px-4">Date Updated</th>
                                        <th class="py-2.5 px-3">Developmental Progress Indicator</th>
                                        <th class="py-2.5 px-3">Model Assessment Score</th>
                                        <th class="py-2.5 px-3">Linked Observation</th>
                                        <th class="py-2.5 px-4 text-right">Clinician Review</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--line)] bg-white">
                                    @foreach($predictions as $predItem)
                                        <tr class="analytics-searchable hover:bg-[var(--canvas)] transition">
                                            <td class="py-2.5 px-4 font-medium">{{ $predItem->date_generated?->format('M d, Y H:i') ?? '—' }}</td>
                                            <td class="py-2.5 px-3 font-bold text-[var(--ink)]">
                                                {{ $predItem->caregiverIndicatorLabel() }}
                                            </td>
                                            <td class="py-2.5 px-3">{{ $predItem->confidence_score !== null ? number_format($predItem->confidence_score * 100, 1) . '% (uncalibrated)' : 'Uncalibrated' }}</td>
                                            <td class="py-2.5 px-3 text-[var(--ink-soft)]">
                                                {{ $predItem->observation?->observation_date?->format('M d, Y') ?? 'Observation #' . $predItem->observation_id }}
                                            </td>
                                            <td class="py-2.5 px-4 text-right">
                                                @if($predItem->validated_by_clinician)
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-50 border border-teal-300 text-teal-800 uppercase">Validated</span>
                                                @else
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 uppercase">Pending Review</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>


            {{-- ===================== QUESTIONS 2 & 3: PROGRESSING WELL VS AREAS NEEDING SUPPORT ===================== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Question 2: What areas appear to be progressing well? --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="eyebrow uppercase">2. What Areas Appear to Be Progressing Well?</p>
                            <h3 class="text-base font-bold tracking-tight">Observed Developmental Strengths</h3>
                        </div>
                    </div>

                    @if($childTotalRecords > 0)
                        <ul class="space-y-2.5 text-xs text-[var(--ink-soft)] mt-3">
                            @if($avgCommunication >= 5)
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Communication ({{ number_format($avgCommunication, 1) }}/10):</span>
                                    {{ $child->first_name }} is showing encouraging responsiveness during communication activities across {{ $childTotalRecords }} recorded {{ Str::plural('session', $childTotalRecords) }}.
                                </li>
                            @endif
                            @if($avgEngagement >= 5)
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Activity Engagement ({{ number_format($avgEngagement, 1) }}/10):</span>
                                    Maintains steady attention and participation during structured learning intervals.
                                </li>
                            @endif
                            @if($avgSocial >= 5)
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Social Interaction ({{ number_format($avgSocial, 1) }}/10):</span>
                                    Demonstrates positive turn-taking and shared interaction with caregivers or peers.
                                </li>
                            @endif
                            @if($avgRepetitive !== null && $avgRepetitive <= 3.5)
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Behavioural Regulation ({{ number_format($avgRepetitive, 1) }}/10 repetition):</span>
                                    Low frequency of repetitive behaviours during daily routines and transitions.
                                </li>
                            @endif
                            @if($avgCommunication < 5 && $avgEngagement < 5 && $avgSocial < 5 && ($avgRepetitive === null || $avgRepetitive > 3.5))
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Consistent Observation Tracking:</span>
                                    {{ $childTotalRecords }} {{ Str::plural('session', $childTotalRecords) }} logged with an average task completion of {{ number_format($childAvgSuccess ?? 0, 0) }}%, providing a clear baseline for support planning.
                                </li>
                            @endif
                        </ul>
                    @else
                        <p class="text-xs text-[var(--ink-soft)] mt-2 leading-relaxed">
                            No behavioural observations have been logged yet. Record daily observations for {{ $child->first_name }} to highlight areas of steady progress.
                        </p>
                    @endif
                </div>

                {{-- Question 3: Which areas may need additional support? --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>
                        </div>
                        <div>
                            <p class="eyebrow uppercase">3. Which Areas May Need Additional Support?</p>
                            <h3 class="text-base font-bold tracking-tight">Focus Areas for Extra Scaffolding</h3>
                        </div>
                    </div>

                    @if($childTotalRecords > 0 || !empty($assessmentInputs))
                        <ul class="space-y-2.5 text-xs text-[var(--ink-soft)] mt-3">
                            @if(!empty($assessmentInputs['primary_domain']) || !empty($assessmentInputs['domain']))
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Clinician Focus Domain ({{ ucfirst($assessmentInputs['primary_domain'] ?? $assessmentInputs['domain']) }}):</span>
                                    Identified in the latest assessment as a priority developmental area for structured activities and routine support.
                                </li>
                            @endif
                            @if($avgCommunication !== null && $avgCommunication < 6)
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Expressive &amp; Receptive Communication ({{ number_format($avgCommunication, 1) }}/10):</span>
                                    May benefit from visual cards, gesture pairing, and step-by-step verbal prompts.
                                </li>
                            @endif
                            @if($avgSocial !== null && $avgSocial < 6)
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Social Reciprocity ({{ number_format($avgSocial, 1) }}/10):</span>
                                    May benefit from short, guided turn-taking games in calm environments.
                                </li>
                            @endif
                            @if($avgRepetitive !== null && $avgRepetitive > 3.5)
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Sensory &amp; Transition Pacing ({{ number_format($avgRepetitive, 1) }}/10):</span>
                                    Scheduled sensory breaks and visual countdown timers can help ease routine transitions.
                                </li>
                            @endif
                            @if(empty($assessmentInputs['primary_domain']) && empty($assessmentInputs['domain']) && ($avgCommunication === null || $avgCommunication >= 6) && ($avgSocial === null || $avgSocial >= 6) && ($avgRepetitive === null || $avgRepetitive <= 3.5))
                                <li class="p-3 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                    <span class="font-bold text-[var(--ink)]">Skill Generalization Across Settings:</span>
                                    Continue practising communication and social routines across home and community environments with your clinician's guidance.
                                </li>
                            @endif
                        </ul>
                    @else
                        <p class="text-xs text-[var(--ink-soft)] mt-2 leading-relaxed">
                            Focus areas will appear here automatically once behavioural observations or clinician assessments are recorded for {{ $child->first_name }}.
                        </p>
                    @endif
                </div>
            </div>


            {{-- ===================== QUESTION 4: WHAT ACTIVITIES OR RECOMMENDATIONS ARE AVAILABLE? ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <p class="eyebrow uppercase mb-1">4. What Activities or Recommendations Are Available?</p>
                        <h3 class="text-lg font-bold tracking-tight">Suggested Support Activities &amp; Recommendations</h3>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Practical, non-diagnostic activity suggestions linked to {{ $child->first_name }}'s recorded observations and progress indicator. Please discuss these suggestions with your child's clinician.
                        </p>
                    </div>
                    <a href="{{ route('children.recommendations', $child) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--ink)] bg-[var(--canvas)] border border-[var(--line)] px-3.5 py-2 rounded-lg hover:border-[var(--ink)] transition shrink-0">
                        View Full Recommendations Page &rarr;
                    </a>
                </div>

                @if($recommendations->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($recommendations->take(4) as $recItem)
                            <div class="analytics-searchable p-4 rounded-xl bg-[var(--canvas)] border border-[var(--line)] flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">
                                            Developmental Area: {{ $recItem->category }}
                                        </span>
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-white border border-[var(--line)] text-[var(--ink)]">
                                            {{ ucfirst($recItem->status ?? 'Active') }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-semibold text-[var(--ink)] leading-relaxed">
                                        {{ $recItem->recommendation_text }}
                                    </p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-[var(--line)] flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                    <span>Generated {{ $recItem->date_generated?->format('M d, Y') ?? 'Recently' }}</span>
                                    <span>Discuss with your clinician</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-5 rounded-xl bg-[var(--canvas)] border border-dashed border-[var(--line)] text-center">
                        <p class="text-sm font-bold">No Specific Activity Recommendations Recorded Yet</p>
                        <p class="text-xs text-[var(--ink-soft)] mt-1 max-w-lg mx-auto">
                            Support recommendations will be generated automatically once a developmental progress indicator or behavioural observations are recorded for {{ $child->first_name }}.
                        </p>
                    </div>
                @endif
            </div>


            {{-- ===================== TRAJECTORIES & GOAL PROGRESSION ===================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

                {{-- Developmental Trajectories Chart (2 cols) --}}
                <div class="lg:col-span-2 bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-lg font-bold tracking-tight">
                                    Developmental Trajectories
                                </h3>
                                <p id="traj-current-label" class="text-[12px] text-[var(--ink-soft)] mt-0.5">
                                    Observed session success progression across recorded dates for {{ $child->first_name }}
                                </p>
                            </div>

                            {{-- Trajectory Domain Filters --}}
                            <div class="flex items-center gap-1.5 bg-[var(--canvas)] p-1 rounded-lg border border-[var(--line)] text-xs">
                                <button
                                    type="button"
                                    id="traj-btn-all"
                                    onclick="setTrajectoryDomain('all')"
                                    class="px-2.5 py-1 rounded-md text-xs font-semibold bg-[var(--ink)] text-white transition cursor-pointer"
                                >
                                    All (Success)
                                </button>
                                <button
                                    type="button"
                                    id="traj-btn-soc"
                                    onclick="setTrajectoryDomain('soc')"
                                    class="px-2.5 py-1 rounded-md text-xs font-medium text-[var(--ink-soft)] bg-white border border-[var(--line)] hover:bg-[var(--canvas)] transition cursor-pointer"
                                >
                                    Social
                                </button>
                                <button
                                    type="button"
                                    id="traj-btn-ver"
                                    onclick="setTrajectoryDomain('ver')"
                                    class="px-2.5 py-1 rounded-md text-xs font-medium text-[var(--ink-soft)] bg-white border border-[var(--line)] hover:bg-[var(--canvas)] transition cursor-pointer"
                                >
                                    Communication
                                </button>
                            </div>
                        </div>

                        @if($trajectoryRecords->isNotEmpty())
                            @php
                                $trajCount = $trajectoryRecords->count();
                                $trajPoints = [];
                                $socPoints = [];
                                $verPoints = [];
                                $svgWidth = 600;
                                $svgHeight = 200;
                                $padX = 40;
                                $padYTop = 25;
                                $padYBottom = 35;
                                $usableW = $svgWidth - ($padX * 2);
                                $usableH = $svgHeight - $padYTop - $padYBottom;

                                foreach ($trajectoryRecords as $idx => $rec) {
                                    $x = $trajCount > 1 ? $padX + ($idx / ($trajCount - 1)) * $usableW : $svgWidth / 2;
                                    $sr = is_numeric($rec->success_rate) ? max(0, min(100, $rec->success_rate)) : 50;
                                    $y = ($svgHeight - $padYBottom) - (($sr / 100) * $usableH);
                                    $trajPoints[] = [
                                        'x' => round($x, 1),
                                        'y' => round($y, 1),
                                        'sr' => $sr,
                                        'date' => $rec->observation_date?->format('M d') ?? ('S' . ($idx + 1)),
                                        'comm' => $rec->communication_score,
                                        'soc' => $rec->social_interaction_score,
                                        'duration' => $rec->session_duration_minutes,
                                    ];

                                    $socScore = is_numeric($rec->social_interaction_score) ? max(0, min(100, $rec->social_interaction_score * 10)) : 50;
                                    $socY = ($svgHeight - $padYBottom) - (($socScore / 100) * $usableH);
                                    $socPoints[] = ['x' => round($x, 1), 'y' => round($socY, 1)];

                                    $verScore = is_numeric($rec->communication_score) ? max(0, min(100, $rec->communication_score * 10)) : 50;
                                    $verY = ($svgHeight - $padYBottom) - (($verScore / 100) * $usableH);
                                    $verPoints[] = ['x' => round($x, 1), 'y' => round($verY, 1)];
                                }

                                $firstPt = $trajPoints[0];
                                $lastPt = end($trajPoints);
                                $pathD = 'M ' . $firstPt['x'] . ' ' . $firstPt['y'];
                                foreach (array_slice($trajPoints, 1) as $pt) {
                                    $pathD .= ' L ' . $pt['x'] . ' ' . $pt['y'];
                                }
                                $fillD = $pathD . ' L ' . $lastPt['x'] . ' ' . ($svgHeight - $padYBottom) . ' L ' . $firstPt['x'] . ' ' . ($svgHeight - $padYBottom) . ' Z';

                                $socPathD = 'M ' . $socPoints[0]['x'] . ' ' . $socPoints[0]['y'];
                                foreach (array_slice($socPoints, 1) as $pt) {
                                    $socPathD .= ' L ' . $pt['x'] . ' ' . $pt['y'];
                                }
                                $socFillD = $socPathD . ' L ' . end($socPoints)['x'] . ' ' . ($svgHeight - $padYBottom) . ' L ' . $socPoints[0]['x'] . ' ' . ($svgHeight - $padYBottom) . ' Z';

                                $verPathD = 'M ' . $verPoints[0]['x'] . ' ' . $verPoints[0]['y'];
                                foreach (array_slice($verPoints, 1) as $pt) {
                                    $verPathD .= ' L ' . $pt['x'] . ' ' . $pt['y'];
                                }
                                $verFillD = $verPathD . ' L ' . end($verPoints)['x'] . ' ' . ($svgHeight - $padYBottom) . ' L ' . $verPoints[0]['x'] . ' ' . ($svgHeight - $padYBottom) . ' Z';
                            @endphp

                            <script>
                                window.childTrajectoryPaths = {
                                    'all': {
                                        d: '{{ $pathD }}',
                                        fill: '{{ $fillD }}',
                                        label: 'Observed session success progression across recorded dates for {{ $child->first_name }}'
                                    },
                                    'soc': {
                                        d: '{{ $socPathD }}',
                                        fill: '{{ $socFillD }}',
                                        label: 'Social interaction score trajectory across recorded sessions'
                                    },
                                    'ver': {
                                        d: '{{ $verPathD }}',
                                        fill: '{{ $verFillD }}',
                                        label: 'Verbal communication score trajectory across recorded sessions'
                                    }
                                };
                            </script>

                            {{-- Dynamic SVG Trajectory Curve --}}
                            <div class="relative w-full h-64 mt-2">
                                <svg class="w-full h-full overflow-visible" viewBox="0 0 600 200" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="trajGradientDynamic" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#12735A" stop-opacity="0.25" />
                                            <stop offset="100%" stop-color="#12735A" stop-opacity="0.0" />
                                        </linearGradient>
                                    </defs>

                                    {{-- Horizontal Grid Lines --}}
                                    <line x1="0" y1="25" x2="600" y2="25" stroke="#f0ebe1" stroke-dasharray="3 3" />
                                    <text x="5" y="29" fill="#a49e95" font-size="9">100%</text>

                                    <line x1="0" y1="60" x2="600" y2="60" stroke="#f0ebe1" stroke-dasharray="3 3" />
                                    <text x="5" y="64" fill="#a49e95" font-size="9">75%</text>

                                    <line x1="0" y1="95" x2="600" y2="95" stroke="#f0ebe1" stroke-dasharray="3 3" />
                                    <text x="5" y="99" fill="#a49e95" font-size="9">50%</text>

                                    <line x1="0" y1="130" x2="600" y2="130" stroke="#f0ebe1" stroke-dasharray="3 3" />
                                    <text x="5" y="134" fill="#a49e95" font-size="9">25%</text>

                                    <line x1="0" y1="165" x2="600" y2="165" stroke="#f0ebe1" />
                                    <text x="5" y="169" fill="#a49e95" font-size="9">0%</text>

                                    {{-- Area Shading Under Trajectory --}}
                                    <path
                                        id="traj-path-fill"
                                        d="{{ $fillD }}"
                                        fill="url(#trajGradientDynamic)"
                                    />

                                    {{-- Primary Trajectory Line --}}
                                    <path
                                        id="traj-path-main"
                                        d="{{ $pathD }}"
                                        fill="none"
                                        stroke="#12735A"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />

                                    {{-- Data Points on Trajectory --}}
                                    @foreach($trajPoints as $pt)
                                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#12735A" stroke="#ffffff" stroke-width="2.5" />
                                        <text x="{{ $pt['x'] }}" y="{{ $pt['y'] - 10 }}" text-anchor="middle" fill="#12735A" font-size="10" font-weight="bold">{{ number_format($pt['sr'], 0) }}%</text>
                                        <text x="{{ $pt['x'] }}" y="185" text-anchor="middle" fill="#716b62" font-size="10">{{ $pt['date'] }}</text>
                                    @endforeach
                                </svg>
                            </div>

                            {{-- Chart Session Timeline Pills --}}
                            <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-[var(--line)] mt-4 text-xs">
                                <span class="text-[11px] font-semibold text-[var(--ink-soft)] uppercase tracking-wider">Timeline:</span>
                                @foreach($trajectoryRecords as $idx => $tRec)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[var(--canvas)] border border-[var(--line)] text-xs">
                                        <span class="w-2 h-2 rounded-full bg-[var(--ink)]"></span>
                                        <span class="font-bold">{{ $tRec->observation_date?->format('M d, Y') ?? 'Session ' . ($idx + 1) }}</span>
                                        <span class="text-[var(--ink-soft)]">| {{ number_format($tRec->success_rate ?? 0, 0) }}% success ({{ $tRec->session_duration_minutes ?? '—' }}m)</span>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            {{-- Empty State Placeholder for Trajectories --}}
                            <div class="h-64 flex flex-col items-center justify-center border border-dashed border-[var(--line)] rounded-xl bg-[var(--canvas)] p-6 text-center my-4">
                                <div class="w-10 h-10 rounded-full bg-white border border-[var(--line)] flex items-center justify-center text-[var(--accent)] mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold">No Developmental Trajectories Yet</p>
                                <p class="text-xs text-[var(--ink-soft)] mt-1 max-w-sm">
                                    Trajectory curves will plot automatically as behavioral observations and session success rates are recorded for {{ $child->first_name }}.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Chart Footer Legend --}}
                    <div class="flex items-center justify-between pt-4 border-t border-[var(--line)] mt-3 text-xs text-[var(--ink-soft)]">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1.5">
                                <span class="w-3 h-1 bg-[var(--ink)] rounded-full"></span>
                                Recorded Success Rate
                            </span>
                        </div>
                        <span class="text-[11px] font-medium text-[var(--ink-soft)]">
                            Updated from database records
                        </span>
                    </div>
                </div>

                {{-- Goal Progression Card (1 col) --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold tracking-tight">Goal Progression</h3>
                                <p class="text-xs text-[var(--ink-soft)] mt-0.5">Progress towards existing therapy goals</p>
                            </div>
                            <a href="{{ route('children.goals-milestones', $child) }}" class="text-xs font-semibold text-[var(--accent)] hover:underline">
                                View All →
                            </a>
                        </div>

                        @if($therapyGoals->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($therapyGoals->take(4) as $goal)
                                    @php
                                        $totalMs = $goal->milestones->count();
                                        $achievedMs = $goal->milestones->where('achieved_flag', true)->count();
                                        $progressPct = $totalMs > 0
                                            ? round(($achievedMs / $totalMs) * 100)
                                            : ($goal->status === 'achieved' ? 100 : ($goal->status === 'active' ? 35 : 0));
                                    @endphp
                                    <div class="p-3.5 rounded-xl bg-[var(--canvas)] border border-[var(--line)]">
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--accent)]">{{ $goal->goal_domain }}</span>
                                            <span class="text-xs font-extrabold">{{ $progressPct }}%</span>
                                        </div>
                                        <p class="text-xs font-semibold leading-snug mb-2">{{ $goal->goal_description }}</p>
                                        <div class="w-full h-1.5 bg-[var(--line)] rounded-full overflow-hidden">
                                            <div class="h-full bg-[var(--ink)] rounded-full" style="width: {{ $progressPct }}%;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="h-56 flex flex-col items-center justify-center border border-dashed border-[var(--line)] rounded-xl bg-[var(--canvas)] p-6 text-center">
                                <p class="text-sm font-bold">No Active Therapy Goals</p>
                                <p class="text-xs text-[var(--ink-soft)] mt-1">
                                    Goal progression metrics will appear here once clinicians establish therapy goals for {{ $child->first_name }}.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-[var(--line)] mt-4 flex items-center justify-between text-xs text-[var(--ink-soft)]">
                        <span>Total Goals: {{ $therapyGoals->count() }}</span>
                        <a href="{{ route('children.goals-milestones', $child) }}" class="font-semibold text-[var(--ink)] hover:underline">Manage Goals</a>
                    </div>
                </div>

            </div>


            {{-- ===================== AI-DRIVEN BEHAVIORAL INSIGHTS ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center text-[var(--accent)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold tracking-tight">
                                AI-Driven Behavioral Insights
                            </h3>
                            <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                                Dynamic behavioral patterns synthesized from {{ $child->first_name }}'s recorded observations.
                            </p>
                        </div>
                    </div>

                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[var(--canvas)] border border-[var(--line)] text-[var(--ink-soft)] self-start sm:self-auto">
                        Derived from {{ $childTotalRecords }} {{ Str::plural('Session', $childTotalRecords) }}
                    </span>
                </div>

                @if($childTotalRecords > 0)
                    {{-- 3 Dynamic Insight Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Insight 1: Communication Dynamics --}}
                        <div class="border border-[var(--line)] rounded-xl p-4 bg-white flex flex-col justify-between hover:border-[var(--ink)] transition">
                            <div>
                                <div class="flex items-center gap-2 mb-2 text-emerald-700">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <h4 class="text-sm font-bold">
                                        {{ $avgCommunication >= 7 ? 'Consistent Communication Response' : ($avgCommunication >= 4 ? 'Developing Communication Fluency' : 'Prompt-Assisted Communication') }}
                                    </h4>
                                </div>
                                <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                    Communication score averages {{ number_format($avgCommunication, 1) }}/10 across {{ $childTotalRecords }} recorded observations. {{ $avgCommunication >= 6 ? 'Shows solid responsiveness to verbal cues and structured interaction.' : 'Requires visual scaffolds and targeted verbal prompts.' }}
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-[var(--line)] flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                <span>Domain: Communication</span>
                                <span class="font-semibold text-emerald-700">Verified Pattern</span>
                            </div>
                        </div>

                        {{-- Insight 2: Social & Task Engagement --}}
                        <div class="border border-[var(--line)] rounded-xl p-4 bg-white flex flex-col justify-between hover:border-[var(--accent)] transition">
                            <div>
                                <div class="flex items-center gap-2 mb-2 text-[var(--accent)]">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <h4 class="text-sm font-bold">
                                        {{ $avgEngagement >= 7 ? 'High Session Engagement' : ($avgEngagement >= 4 ? 'Moderate Activity Focus' : 'Variable Focus Needs') }}
                                    </h4>
                                </div>
                                <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                    Engagement level averages {{ number_format($avgEngagement, 1) }}/10, with social interaction score at {{ number_format($avgSocial, 1) }}/10. Task engagement is highest during early session intervals.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-[var(--line)] flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                <span>Domain: Social &amp; Engagement</span>
                                <span class="font-semibold text-[var(--accent)]">Active Trend</span>
                            </div>
                        </div>

                        {{-- Insight 3: Behavioral Stability & Repetition --}}
                        <div class="border border-[var(--line)] rounded-xl p-4 bg-white flex flex-col justify-between hover:border-[var(--ink)] transition">
                            <div>
                                <div class="flex items-center gap-2 mb-2 text-indigo-700">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <h4 class="text-sm font-bold">
                                        {{ $avgRepetitive <= 2.5 ? 'Low Repetitive Behavior Frequency' : ($avgRepetitive <= 4.5 ? 'Moderate Repetitive Behaviors' : 'Heightened Sensory Repetition') }}
                                    </h4>
                                </div>
                                <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                    Repetitive behavior index averages {{ number_format($avgRepetitive, 1) }}/10. Overall session success rate is {{ number_format($childAvgSuccess ?? 0, 0) }}% across an average {{ round($childAvgDuration ?? 0) }} min duration.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-[var(--line)] flex items-center justify-between text-[11px] text-[var(--ink-soft)]">
                                <span>Domain: Behavior Regulation</span>
                                <span class="font-semibold text-indigo-700">Clinical Indicator</span>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Placeholder when no records exist --}}
                    <div class="border border-dashed border-[var(--line)] rounded-xl p-8 text-center bg-[var(--canvas)]">
                        <div class="w-10 h-10 rounded-full bg-white border border-[var(--line)] flex items-center justify-center mx-auto text-[var(--accent)] mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold">No Behavioral Records Yet</h4>
                        <p class="text-xs text-[var(--ink-soft)] mt-1.5 max-w-md mx-auto">
                            AI-driven insights are synthesized from recorded observations. Log behavioral sessions for {{ $child->first_name }} to generate dynamic communication, engagement, and regulation trends.
                        </p>
                    </div>
                @endif
            </div>


            {{-- ===================== FEATURE CONTRIBUTION ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-lg font-bold tracking-tight">
                            Feature Contribution
                        </h3>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Breakdown of the 14 features used in the platform's ML pipeline (<span class="font-mono text-[var(--ink)]">child_intervention_pipeline.joblib</span>), showing {{ $child->first_name }}'s recorded values and relative model influence weights.
                        </p>
                    </div>

                    <span class="text-[11px] font-semibold tracking-wide border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)] px-3 py-1 rounded-full shrink-0">
                        child_intervention_pipeline
                    </span>
                </div>

                {{-- 14 Model Features List --}}
                @php
                    $childAgeYears = $child->date_of_birth ? round($child->date_of_birth->diffInMonths(now()) / 12, 1) : ($child->age_in_months ? round($child->age_in_months / 12, 1) : 4.0);
                    $childGenderVal = ucfirst($child->gender ?? ($child->sex ?? 'Female'));

                    $featuresList = [
                        [
                            'key' => 'avg_success_first_2',
                            'label' => 'Average Success in First 2 Sessions',
                            'category' => 'Behavioral Log',
                            'description' => 'Baseline performance metric computed from the child\'s initial 2 observation sessions.',
                            'value' => $avgSuccessFirst2 !== null ? number_format($avgSuccessFirst2, 1) . '%' : 'Pending (requires >= 2 sessions)',
                            'weight' => '+0.750',
                            'pct' => 100,
                            'direction' => 'positive',
                            'impact_label' => 'Primary Positive Factor',
                        ],
                        [
                            'key' => 'primary_domain',
                            'label' => 'Primary Clinical Domain',
                            'category' => 'Clinical Evaluation',
                            'description' => 'Target clinical area identified for intervention (e.g. Cognitive/Academic, Social Interaction).',
                            'value' => $assessmentInputs['primary_domain'] ?? 'Pending assessment',
                            'weight' => '+0.515',
                            'pct' => 69,
                            'direction' => 'positive',
                            'impact_label' => 'Significant Impact',
                        ],
                        [
                            'key' => 'cognitive_verbal_percentile',
                            'label' => 'Cognitive Verbal Percentile',
                            'category' => 'Standardized Test',
                            'description' => 'Verbal communication aptitude percentile score from standardized developmental assessment.',
                            'value' => isset($assessmentInputs['cognitive_verbal_percentile']) ? $assessmentInputs['cognitive_verbal_percentile'] . 'th percentile' : 'Pending assessment',
                            'weight' => '-0.408',
                            'pct' => 54,
                            'direction' => 'negative',
                            'impact_label' => 'Inverted Predictor',
                        ],
                        [
                            'key' => 'domain',
                            'label' => 'Assessment Domain',
                            'category' => 'Observation Domain',
                            'description' => 'Specific behavioral domain currently being evaluated during session activities.',
                            'value' => $assessmentInputs['domain'] ?? 'Pending assessment',
                            'weight' => '+0.319',
                            'pct' => 43,
                            'direction' => 'positive',
                            'impact_label' => 'Moderate Positive',
                        ],
                        [
                            'key' => 'adaptive_composite_percentile',
                            'label' => 'Adaptive Composite Percentile',
                            'category' => 'Adaptive Functioning',
                            'description' => 'Composite score reflecting independent living and age-appropriate adaptive behaviors.',
                            'value' => isset($assessmentInputs['adaptive_composite_percentile']) ? $assessmentInputs['adaptive_composite_percentile'] . 'th percentile' : 'Pending assessment',
                            'weight' => '-0.231',
                            'pct' => 31,
                            'direction' => 'negative',
                            'impact_label' => 'Moderate Negative',
                        ],
                        [
                            'key' => 'ados2_rrb',
                            'label' => 'ADOS-2 Restricted & Repetitive Behavior',
                            'category' => 'Autism Assessment',
                            'description' => 'Score from Autism Diagnostic Observation Schedule (ADOS-2) Restricted and Repetitive Behaviors domain.',
                            'value' => $assessmentInputs['ados2_rrb'] ?? 'Pending assessment',
                            'weight' => '+0.210',
                            'pct' => 28,
                            'direction' => 'positive',
                            'impact_label' => 'Positive Weight',
                        ],
                        [
                            'key' => 'gender',
                            'label' => 'Biological Sex / Gender',
                            'category' => 'Demographics',
                            'description' => 'Demographic factor calibrated for differential developmental milestone trajectories.',
                            'value' => $childGenderVal,
                            'weight' => '+0.187',
                            'pct' => 25,
                            'direction' => 'positive',
                            'impact_label' => 'Demographic Factor',
                        ],
                        [
                            'key' => 'num_current_services',
                            'label' => 'Concurrent Therapy Services',
                            'category' => 'Care Plan',
                            'description' => 'Total active support services (e.g., Speech, Occupational, ABA therapy).',
                            'value' => isset($assessmentInputs['num_current_services']) ? $assessmentInputs['num_current_services'] . ' active services' : 'Pending assessment',
                            'weight' => '+0.181',
                            'pct' => 24,
                            'direction' => 'positive',
                            'impact_label' => 'Positive Weight',
                        ],
                        [
                            'key' => 'support_level',
                            'label' => 'Required Support Level',
                            'category' => 'Clinical Diagnosis',
                            'description' => 'DSM-5 autism spectrum support classification (Level 1, Level 2, or Level 3).',
                            'value' => $assessmentInputs['support_level'] ?? 'Pending assessment',
                            'weight' => '+0.173',
                            'pct' => 23,
                            'direction' => 'positive',
                            'impact_label' => 'Clinical Support Scale',
                        ],
                        [
                            'key' => 'ados2_comparison_score',
                            'label' => 'ADOS-2 Comparison Score',
                            'category' => 'Autism Assessment',
                            'description' => 'Comparative severity score (Low, Moderate, High) calculated from standard diagnostic rubric.',
                            'value' => $assessmentInputs['ados2_comparison_score'] ?? 'Pending assessment',
                            'weight' => '+0.173',
                            'pct' => 23,
                            'direction' => 'positive',
                            'impact_label' => 'Diagnostic Factor',
                        ],
                        [
                            'key' => 'cognitive_gca_percentile',
                            'label' => 'Cognitive GCA Percentile',
                            'category' => 'Standardized Test',
                            'description' => 'General Conceptual Ability (GCA) percentile evaluating overall cognitive development.',
                            'value' => isset($assessmentInputs['cognitive_gca_percentile']) ? $assessmentInputs['cognitive_gca_percentile'] . 'th percentile' : 'Pending assessment',
                            'weight' => '-0.153',
                            'pct' => 20,
                            'direction' => 'negative',
                            'impact_label' => 'Negative Factor',
                        ],
                        [
                            'key' => 'ados2_social_affect',
                            'label' => 'ADOS-2 Social Affect Score',
                            'category' => 'Autism Assessment',
                            'description' => 'Observation metrics evaluating social reciprocity and non-verbal communication.',
                            'value' => $assessmentInputs['ados2_social_affect'] ?? 'Pending assessment',
                            'weight' => '-0.120',
                            'pct' => 16,
                            'direction' => 'negative',
                            'impact_label' => 'Negative Factor',
                        ],
                        [
                            'key' => 'age_years',
                            'label' => 'Child Age in Years',
                            'category' => 'Demographics',
                            'description' => 'Chronological age in years computed from recorded date of birth.',
                            'value' => $childAgeYears . ' years',
                            'weight' => '-0.041',
                            'pct' => 6,
                            'direction' => 'negative',
                            'impact_label' => 'Age Scaling',
                        ],
                        [
                            'key' => 'cognitive_nonverbal_percentile',
                            'label' => 'Cognitive Nonverbal Percentile',
                            'category' => 'Standardized Test',
                            'description' => 'Non-verbal spatial and perceptual reasoning percentile from standardized testing.',
                            'value' => isset($assessmentInputs['cognitive_nonverbal_percentile']) ? $assessmentInputs['cognitive_nonverbal_percentile'] . 'th percentile' : 'Pending assessment',
                            'weight' => '-0.028',
                            'pct' => 4,
                            'direction' => 'negative',
                            'impact_label' => 'Minor Weight',
                        ],
                    ];
                @endphp

                <div class="space-y-3">
                    @foreach($featuresList as $feat)
                        <div class="analytics-searchable border border-[var(--line)] rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-[var(--canvas)] transition">
                            <div class="max-w-xl">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-bold">{{ $feat['label'] }}</h4>
                                    <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded-full border border-[var(--line)] bg-white text-[var(--ink-soft)]">
                                        {{ $feat['key'] }}
                                    </span>
                                    <span class="text-[10px] uppercase font-bold tracking-wide px-2 py-0.5 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">
                                        {{ $feat['category'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-[var(--ink-soft)] mt-1">
                                    {{ $feat['description'] }}
                                </p>
                                <p class="text-xs font-semibold mt-1">
                                    {{ $child->first_name }}'s Value: <span class="text-[var(--ink)] font-bold">{{ $feat['value'] }}</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-4 self-end sm:self-center shrink-0">
                                <div class="text-right">
                                    <span class="text-xs font-semibold {{ $feat['direction'] === 'positive' ? 'text-emerald-700' : 'text-[var(--ink-soft)]' }}">
                                        {{ $feat['impact_label'] }}
                                    </span>
                                    <div class="w-32 sm:w-36 h-2 bg-[var(--line)] rounded-full overflow-hidden mt-1">
                                        <div
                                            class="h-full rounded-full {{ $feat['direction'] === 'positive' ? 'bg-[var(--ink)]' : 'bg-[var(--accent)]' }}"
                                            style="width: {{ $feat['pct'] }}%;"
                                        ></div>
                                    </div>
                                </div>
                                <span class="text-sm font-bold font-mono {{ $feat['direction'] === 'positive' ? 'text-[var(--ink)]' : 'text-[var(--ink-soft)]' }} w-14 text-right">
                                    {{ $feat['weight'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


            {{-- ===================== RECENT CLINICAL VALIDATIONS TABLE ===================== --}}
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
                    <div>
                        <h3 class="text-lg font-bold tracking-tight">
                            Recent Clinical Validations
                        </h3>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Predictions reviewed by authorised clinicians, including feedback and validation status.
                        </p>
                    </div>

                    <span class="text-xs font-semibold text-[var(--ink-soft)] self-start sm:self-auto">
                        Clinical Audit Trail
                    </span>
                </div>

                {{-- Clinical Validations Table --}}
                <div class="overflow-x-auto border border-[var(--line)] rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[var(--canvas)]">
                            <tr class="border-b border-[var(--line)] text-left text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                <th class="py-3 px-4">Clinician</th>
                                <th class="py-3 px-3">Patient</th>
                                <th class="py-3 px-3">Validation Date</th>
                                <th class="py-3 px-3">Observation Type</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-4 text-right">Notes / Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--line)] bg-white">
                            @if($clinicalValidations->isNotEmpty())
                                @foreach($clinicalValidations as $val)
                                    <tr class="hover:bg-[var(--canvas)] transition">
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <span class="w-7 h-7 rounded-full bg-[var(--canvas)] border border-[var(--line)] text-xs font-bold text-[var(--ink)] flex items-center justify-center">
                                                    {{ strtoupper(substr($val->clinician?->name ?? 'CL', 0, 2)) }}
                                                </span>
                                                <span class="text-sm font-semibold">{{ $val->clinician?->name ?? 'Authorised Clinician' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3 text-xs font-mono text-[var(--ink-soft)] whitespace-nowrap">
                                            {{ $child->first_name }}
                                        </td>
                                        <td class="py-3.5 px-3 text-xs text-[var(--ink-soft)] whitespace-nowrap">
                                            {{ $val->date_generated?->format('Y-m-d') ?? '—' }}
                                        </td>
                                        <td class="py-3.5 px-3 text-xs font-medium whitespace-nowrap">
                                            {{ $val->caregiverIndicatorLabel() }}
                                        </td>
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            <span class="text-[10px] font-bold tracking-wider px-2.5 py-0.5 rounded-full border border-teal-300 bg-teal-50 text-teal-800 uppercase">
                                                Validated
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-xs text-[var(--ink-soft)] text-right">
                                            {{ $val->clinician_feedback ?? 'Validated by clinician' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- Faithful Styled Placeholder for Clinical Validations --}}
                                <tr>
                                    <td colspan="6" class="py-12 px-4 text-center">
                                        <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[var(--canvas)] border border-[var(--line)] text-[var(--accent)] mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold">
                                            No clinical validations recorded yet
                                        </h4>
                                        <p class="text-xs text-[var(--ink-soft)] mt-1.5 max-w-md mx-auto leading-relaxed">
                                            Authorised clinicians have not yet submitted formal validation reviews for {{ $child->first_name }}. Validations and clinical feedback will appear here once reviewed.
                                        </p>
                                        <div class="mt-4 inline-flex items-center gap-1.5 text-[11px] font-semibold text-[var(--ink-soft)] bg-[var(--canvas)] px-3 py-1 rounded-full border border-[var(--line)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Pending Clinical Validation
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>


            {{-- ===================== TRANSPARENCY & REGULATORY COMPLIANCE BANNER ===================== --}}
            <div class="border border-[var(--line)] rounded-2xl p-5 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[var(--canvas)] border border-[var(--line)] text-[var(--ink)] flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold">Clinical Decision Support Disclaimer</h4>
                        <p class="text-[11px] text-[var(--ink-soft)] mt-0.5">
                            This prediction dashboard is intended for exploratory clinical decision support and longitudinal monitoring only. Model outputs are uncalibrated and do not constitute a diagnostic evaluation or replace professional assessment by qualified medical practitioners.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0 text-xs font-semibold text-[var(--ink)]">
                    <span>Non-Diagnostic Model</span>
                </div>
            </div>

        </main>
    </div>

</div>

<script>
    function setTrajectoryDomain(domain) {
        const domains = ['all', 'soc', 'ver'];

        domains.forEach(function (d) {
            const btn = document.getElementById('traj-btn-' + d);
            if (btn) {
                if (d === domain) {
                    btn.className = 'px-2.5 py-1 rounded-md text-xs font-semibold bg-[var(--ink)] text-white transition cursor-pointer';
                } else {
                    btn.className = 'px-2.5 py-1 rounded-md text-xs font-medium text-[var(--ink-soft)] bg-white border border-[var(--line)] hover:bg-[var(--canvas)] transition cursor-pointer';
                }
            }
        });

        const pathMain = document.getElementById('traj-path-main');
        const pathFill = document.getElementById('traj-path-fill');
        const label = document.getElementById('traj-current-label');

        const domainPaths = window.childTrajectoryPaths || {
            'all': {
                d: 'M 30 170 C 130 165, 240 145, 340 105 C 410 80, 500 95, 600 60',
                fill: 'M 30 170 C 130 165, 240 145, 340 105 C 410 80, 500 95, 600 60 L 600 200 L 30 200 Z',
                label: 'Observed session progression'
            },
            'soc': {
                d: 'M 30 180 C 130 170, 240 160, 340 130 C 410 115, 500 100, 600 85',
                fill: 'M 30 180 C 130 170, 240 160, 340 130 C 410 115, 500 100, 600 85 L 600 200 L 30 200 Z',
                label: 'Social interaction score trajectory'
            },
            'ver': {
                d: 'M 30 160 C 130 150, 240 130, 340 90 C 410 70, 500 75, 600 45',
                fill: 'M 30 160 C 130 150, 240 130, 340 90 C 410 70, 500 75, 600 45 L 600 200 L 30 200 Z',
                label: 'Verbal communication score trajectory'
            }
        };

        if (domainPaths[domain] && pathMain && pathFill) {
            pathMain.setAttribute('d', domainPaths[domain].d);
            pathFill.setAttribute('d', domainPaths[domain].fill);
            if (label) {
                label.textContent = domainPaths[domain].label;
            }
        }
    }

    function filterAnalyticsPage() {
        const input = document.getElementById('topbar-analytics-search');
        const q = input ? input.value.toLowerCase().trim() : '';
        const items = document.querySelectorAll('.analytics-searchable');
        items.forEach(function (item) {
            const text = (item.textContent || '').toLowerCase();
            if (!q || text.indexOf(q) > -1) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    window.addEventListener('beforeunload', function () {
        const loader = document.getElementById('prediction-loading-indicator');
        if (loader) {
            loader.classList.remove('hidden');
        }
    });
</script>

</body>
</html>
