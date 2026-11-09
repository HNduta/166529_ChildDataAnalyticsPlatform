<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinician Dashboard · AutiSync</title>

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

        button:focus-visible, a:focus-visible, input:focus-visible, textarea:focus-visible { outline:2px solid var(--ink); outline-offset:2px; }
    </style>
</head>
<body class="min-h-screen">
@php
    $clinician = $clinician ?? auth()->user();
    $assignedChildren = $assignedChildren ?? collect();
    $recentObservations = $recentObservations ?? collect();
    $totalObservationsCount = $totalObservationsCount ?? 0;
    $pendingPredictions = $pendingPredictions ?? collect();
    $validatedPredictions = $validatedPredictions ?? collect();
    $awaitingAssessmentChildren = $awaitingAssessmentChildren ?? collect();
    $clinicianAvatarUrl = $clinician?->profilePictureUrl();
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
                    <a href="{{ route('clinician.dashboard') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        Clinical Dashboard
                    </a>
                    <a href="#caseload-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M17 11a3 3 0 100-6 3 3 0 000 6zM21 21c0-3-2-5-4.5-5.5"/></svg>
                        Assigned Children ({{ $assignedChildren->count() }})
                    </a>
                </div>
            </div>

            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Assessments &amp; Reviews</p>
                <div class="space-y-1">
                    <a href="#pending-reviews-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Predictions to Review ({{ $pendingPredictions->count() }})
                    </a>
                    <a href="#recent-observations-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                        Behavioural Observations ({{ $totalObservationsCount }})
                    </a>
                    <a href="#validated-history-section" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Validated Reviews ({{ $validatedPredictions->count() }})
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
            <p class="hidden sm:block font-semibold text-[15px]">Clinical Overview</p>

            <div class="flex-1 max-w-md ml-0 sm:ml-6">
                <div class="relative flex items-center">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)] pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input
                        type="text"
                        id="clinician-global-search"
                        oninput="filterClinicianCaseload(this.value)"
                        placeholder="Search assigned children, caregivers, or notes..."
                        class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-3 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:bg-white focus:outline-none"
                    >
                </div>
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

            {{-- Flash Status Messages --}}
            @if (session('success') || session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 flex items-center gap-2.5" role="status">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') ?? session('status') }}</span>
                </div>
            @endif

            {{-- ===================== HERO / WELCOME BANNER ===================== --}}
            <section class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold tracking-wide bg-[var(--canvas)] border border-[var(--line)] px-3 py-1 rounded-full mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Clinician Decision-Support Workspace
                    </span>
                    <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">
                        Welcome, {{ $clinician->name }}
                    </h1>
                    <p class="text-sm text-[var(--ink-soft)] mt-1.5 leading-relaxed">
                        Review caregiver behavioural observations, complete specialist ADOS-2 and cognitive assessment inputs, and validate exploratory developmental progress indicators for the children under your care.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="#pending-reviews-section" class="inline-flex items-center gap-2 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Review Predictions ({{ $pendingPredictions->count() }})
                    </a>
                    <a href="#recent-observations-section" class="inline-flex items-center gap-2 border border-[var(--accent-soft)] bg-white text-[var(--accent)] text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-[var(--accent-tint)] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                        Specialist Assessments
                    </a>
                </div>
            </section>

            {{-- ===================== 4 REAL KPI SUMMARY CARDS ===================== --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {{-- Card 1: Assigned Children --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="eyebrow uppercase">Assigned Children</span>
                        <div class="w-8 h-8 rounded-lg bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-[var(--ink)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M17 11a3 3 0 100-6 3 3 0 000 6z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight">{{ $assignedChildren->count() }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">
                        Active child {{ Str::plural('profile', $assignedChildren->count()) }} in your caseload
                    </p>
                </div>

                {{-- Card 2: Children Awaiting Assessment --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="eyebrow uppercase">Awaiting Assessment</span>
                        <div class="w-8 h-8 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center text-[var(--accent)]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight text-[var(--accent)]">{{ $awaitingAssessmentChildren->count() }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">
                        Assigned {{ Str::plural('child', $awaitingAssessmentChildren->count()) }} needing 14-feature specialist inputs
                    </p>
                </div>

                {{-- Card 3: Predictions Awaiting Clinician Review --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="eyebrow uppercase">Pending Validation</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight">{{ $pendingPredictions->count() }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">
                        Generated {{ Str::plural('prediction', $pendingPredictions->count()) }} awaiting clinician review
                    </p>
                </div>

                {{-- Card 4: Validated Predictions --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="eyebrow uppercase">Validated Reviews</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight text-emerald-700">{{ $validatedPredictions->count() }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">
                        Specialist-reviewed {{ Str::plural('indicator', $validatedPredictions->count()) }} across {{ $totalObservationsCount }} logged {{ Str::plural('observation', $totalObservationsCount) }}
                    </p>
                </div>
            </section>

            {{-- ===================== ASSIGNED CASELOAD SECTION ===================== --}}
            <section id="caseload-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                    <div>
                        <p class="eyebrow uppercase mb-0.5">Caseload Management</p>
                        <h2 class="text-lg font-bold tracking-tight">Assigned Children ({{ $assignedChildren->count() }})</h2>
                        <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                            Locate assigned children, review their latest observations and developmental indicators, and open specialist assessments.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select
                            id="caseload-status-filter"
                            onchange="filterClinicianCaseload(document.getElementById('clinician-global-search').value)"
                            class="bg-[var(--canvas)] border border-[var(--line)] rounded-lg px-3 py-1.5 text-xs font-semibold text-[var(--ink)] focus:outline-none"
                        >
                            <option value="all">All Assigned Children</option>
                            <option value="awaiting_assessment">Awaiting Specialist Assessment</option>
                            <option value="needs_support">May Need Additional Support</option>
                            <option value="on_track">On-Track Indicator</option>
                        </select>
                    </div>
                </div>

                @if ($assignedChildren->isNotEmpty())
                    <div class="overflow-x-auto -mx-1">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-[var(--ink-soft)] font-semibold border-b border-[var(--line)]">
                                    <th class="py-2.5 px-2">Child Profile</th>
                                    <th class="py-2.5 px-2">Caregiver</th>
                                    <th class="py-2.5 px-2">Observations</th>
                                    <th class="py-2.5 px-2">Latest Assessment Status</th>
                                    <th class="py-2.5 px-2">Latest Indicator</th>
                                    <th class="py-2.5 px-2 text-right">Clinical Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--line)]">
                                @foreach ($assignedChildren as $assignedChild)
                                    @php
                                        $childPicUrl = $assignedChild->profilePictureUrl();
                                        $latestObs = $assignedChild->behaviouralRecords->first();
                                        $latestPred = $assignedChild->predictions->first();
                                        $hasCompleteAssessment = $latestObs && is_array($latestObs->ml_assessment_inputs) && count($latestObs->ml_assessment_inputs) >= 14;
                                        $indicatorStatusKey = $latestPred
                                            ? ((string) $latestPred->predicted_outcome === '1' ? 'needs_support' : 'on_track')
                                            : 'awaiting_assessment';
                                    @endphp
                                    <tr
                                        class="caseload-row hover:bg-[var(--canvas)]/50 transition"
                                        data-search="{{ strtolower($assignedChild->first_name . ' ' . ($assignedChild->caregiver->name ?? '') . ' ' . $assignedChild->sex) }}"
                                        data-status="{{ $hasCompleteAssessment ? $indicatorStatusKey : 'awaiting_assessment' }}"
                                    >
                                        <td class="py-3.5 px-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 shrink-0 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center overflow-hidden text-xs font-bold text-[var(--accent)]">
                                                    @if ($childPicUrl)
                                                        <img src="{{ $childPicUrl }}" alt="{{ $assignedChild->first_name }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ $assignedChild->initials() }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-1.5">
                                                        <p class="font-bold text-[var(--ink)]">{{ $assignedChild->first_name }}</p>
                                                        @if ($assignedChild->diagnosis_confirmed)
                                                            <span class="text-[9px] font-bold bg-[var(--ink)] text-white px-1.5 py-0.5 rounded-full">ASD</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-[var(--ink-soft)]">
                                                        {{ $assignedChild->age_in_months }} mos · {{ $assignedChild->sex }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-3.5 px-2">
                                            <p class="text-xs font-semibold">{{ $assignedChild->caregiver->name ?? 'Assigned Caregiver' }}</p>
                                            <p class="text-[11px] text-[var(--ink-soft)]">{{ $assignedChild->caregiver->email ?? '' }}</p>
                                        </td>

                                        <td class="py-3.5 px-2">
                                            <span class="font-semibold text-xs">{{ $assignedChild->behaviouralRecords->count() }}</span>
                                            <span class="text-xs text-[var(--ink-soft)]">{{ Str::plural('session', $assignedChild->behaviouralRecords->count()) }}</span>
                                            @if ($latestObs)
                                                <p class="text-[11px] text-[var(--ink-soft)]">
                                                    Latest: {{ $latestObs->observation_date?->format('M d, Y') }}
                                                </p>
                                            @endif
                                        </td>

                                        <td class="py-3.5 px-2">
                                            @if ($hasCompleteAssessment)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    14 Inputs Recorded
                                                </span>
                                            @elseif ($latestObs)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                                    Awaiting Specialist Inputs
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-[var(--ink-soft)] bg-[var(--canvas)] border border-[var(--line)] px-2.5 py-0.5 rounded-full">
                                                    No Observations Yet
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-3.5 px-2">
                                            @if ($latestPred)
                                                <div class="space-y-0.5">
                                                    <span class="inline-block text-xs font-bold px-2.5 py-0.5 rounded-full {{ (string) $latestPred->predicted_outcome === '1' ? 'bg-[var(--accent-tint)] text-[var(--accent)] border border-[var(--accent-soft)]' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                                        {{ $latestPred->caregiverIndicatorLabel() }}
                                                    </span>
                                                    <p class="text-[11px] text-[var(--ink-soft)]">
                                                        {{ $latestPred->validated_by_clinician ? 'Validated by Clinician' : 'Awaiting Review' }}
                                                    </p>
                                                </div>
                                            @else
                                                <span class="text-xs text-[var(--ink-soft)]">Not generated yet</span>
                                            @endif
                                        </td>

                                        <td class="py-3.5 px-2 text-right">
                                            @if ($latestObs)
                                                <a
                                                    href="{{ route('clinician.assessments.edit', $latestObs) }}"
                                                    class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:opacity-90 transition"
                                                >
                                                    {{ $hasCompleteAssessment ? 'Update Assessment' : 'Complete Assessment' }}
                                                </a>
                                            @else
                                                <span class="text-xs text-[var(--ink-soft)] italic">Waiting for caregiver log</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="border border-dashed border-[var(--accent-soft)] rounded-2xl p-10 text-center bg-[var(--canvas)]/40">
                        <div class="w-12 h-12 mx-auto rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center text-[var(--accent)] mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M18 8v6M15 11h6"/></svg>
                        </div>
                        <p class="text-sm font-bold">No children assigned to your caseload yet</p>
                        <p class="text-xs text-[var(--ink-soft)] mt-1 max-w-md mx-auto">
                            Once child profiles are linked to your clinician account, their behavioural observations, specialist assessments, and predictions will appear here automatically.
                        </p>
                    </div>
                @endif
            </section>

            {{-- ===================== TWO-COLUMN CLINICAL WORKFLOW ===================== --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                {{-- LEFT: PREDICTIONS AWAITING CLINICIAN VALIDATION --}}
                <section id="pending-reviews-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="eyebrow uppercase mb-0.5">Clinical Validation Queue</p>
                            <h2 class="text-lg font-bold tracking-tight">Predictions Awaiting Review ({{ $pendingPredictions->count() }})</h2>
                            <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                                Validate generated indicators and record plain-language feedback for the caregiver.
                            </p>
                        </div>
                    </div>

                    @if ($pendingPredictions->isNotEmpty())
                        <div class="space-y-4">
                            @foreach ($pendingPredictions as $pendingPred)
                                <div class="border border-[var(--line)] rounded-xl p-4 bg-[var(--canvas)]/50 space-y-3">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            @php
                                                $predChildPic = $pendingPred->child?->profilePictureUrl();
                                            @endphp
                                            <div class="w-9 h-9 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center overflow-hidden text-xs font-bold text-[var(--accent)]">
                                                @if ($predChildPic)
                                                    <img src="{{ $predChildPic }}" alt="{{ $pendingPred->child->first_name }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ $pendingPred->child?->initials() ?? 'CH' }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold">{{ $pendingPred->child->first_name ?? 'Child' }}</p>
                                                <p class="text-[11px] text-[var(--ink-soft)]">
                                                    Generated {{ $pendingPred->date_generated?->format('M d, Y') ?? 'Recently' }}
                                                    @if ($pendingPred->confidence_score !== null)
                                                        · Model score: {{ number_format((float) $pendingPred->confidence_score * 100, 1) }}%
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ (string) $pendingPred->predicted_outcome === '1' ? 'bg-[var(--accent-tint)] text-[var(--accent)] border border-[var(--accent-soft)]' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                            {{ $pendingPred->caregiverIndicatorLabel() }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-[var(--ink-soft)] leading-relaxed">
                                        {{ $pendingPred->caregiverIndicatorDescription() }}
                                    </p>

                                    <form method="POST" action="{{ route('clinician.predictions.validate', $pendingPred) }}" class="space-y-3 pt-2 border-t border-[var(--line)]">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="validated_by_clinician" value="1">

                                        <div>
                                            <label for="feedback-{{ $pendingPred->id }}" class="block text-xs font-semibold mb-1">
                                                Specialist Feedback &amp; Guidance for Caregiver
                                            </label>
                                            <textarea
                                                id="feedback-{{ $pendingPred->id }}"
                                                name="clinician_feedback"
                                                rows="2"
                                                placeholder="Add supportive clinical notes or adjustments for the caregiver..."
                                                class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2 text-xs text-[var(--ink)] placeholder:text-[var(--ink-soft)] focus:outline-none"
                                            >{{ old('clinician_feedback', $pendingPred->clinician_feedback) }}</textarea>
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            @if ($pendingPred->observation)
                                                <a href="{{ route('clinician.assessments.edit', $pendingPred->observation) }}" class="text-xs font-semibold text-[var(--ink-soft)] hover:text-[var(--ink)] hover:underline">
                                                    Inspect Assessment Inputs →
                                                </a>
                                            @else
                                                <span></span>
                                            @endif

                                            <button type="submit" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-xs font-semibold px-3.5 py-2 rounded-lg hover:opacity-90 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                                Validate Indicator &amp; Save Feedback
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="border border-dashed border-[var(--line)] rounded-xl p-8 text-center">
                            <div class="w-10 h-10 mx-auto rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 mb-2.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="text-sm font-semibold">All generated predictions are reviewed</p>
                            <p class="text-xs text-[var(--ink-soft)] mt-1">
                                New predictions requiring specialist validation will appear here automatically.
                            </p>
                        </div>
                    @endif
                </section>

                {{-- RIGHT: RECENT BEHAVIOURAL OBSERVATIONS & SPECIALIST ASSESSMENTS --}}
                <section id="recent-observations-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="eyebrow uppercase mb-0.5">Observation &amp; Assessment Feed</p>
                            <h2 class="text-lg font-bold tracking-tight">Recent Behavioural Observations</h2>
                            <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                                Caregiver session logs for your assigned children. Open any observation to enter or update specialist assessment scores.
                            </p>
                        </div>
                    </div>

                    @if ($recentObservations->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($recentObservations->take(6) as $obs)
                                @php
                                    $obsChildPic = $obs->child?->profilePictureUrl();
                                    $obsInputsComplete = is_array($obs->ml_assessment_inputs) && count($obs->ml_assessment_inputs) >= 14;
                                @endphp
                                <div class="border border-[var(--line)] rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:border-[var(--ink)] transition">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 shrink-0 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center overflow-hidden text-xs font-bold">
                                            @if ($obsChildPic)
                                                <img src="{{ $obsChildPic }}" alt="{{ $obs->child->first_name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ $obs->child?->initials() ?? 'CH' }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-bold">{{ $obs->child->first_name ?? 'Child' }}</p>
                                                <span class="text-xs text-[var(--ink-soft)]">· {{ $obs->observation_date?->format('M d, Y') }}</span>
                                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $obsInputsComplete ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                                    {{ $obsInputsComplete ? 'Assessment Saved' : 'Needs Assessment' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-[var(--ink-soft)] mt-1">
                                                Comm: {{ $obs->communication_score }}/10 · Social: {{ $obs->social_interaction_score }}/10 · Engagement: {{ $obs->engagement_level }}/10 · Success: {{ number_format((float) $obs->success_rate, 0) }}%
                                            </p>
                                            @if ($obs->notes)
                                                <p class="text-xs text-[var(--ink-soft)] italic mt-1 line-clamp-1">
                                                    "{{ $obs->notes }}"
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <a
                                            href="{{ route('clinician.assessments.edit', $obs) }}"
                                            class="inline-flex items-center gap-1.5 border border-[var(--line)] bg-[var(--canvas)] text-[var(--ink)] text-xs font-semibold px-3 py-2 rounded-lg hover:bg-[var(--ink)] hover:text-white transition"
                                        >
                                            {{ $obsInputsComplete ? 'Edit Assessment' : 'Enter Assessment' }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="border border-dashed border-[var(--line)] rounded-xl p-8 text-center">
                            <p class="text-sm font-semibold">No behavioural observations recorded yet</p>
                            <p class="text-xs text-[var(--ink-soft)] mt-1">
                                When caregivers log behavioural observations for your assigned children, they will appear here for specialist review.
                            </p>
                        </div>
                    @endif
                </section>

            </div>

            {{-- ===================== VALIDATED PREDICTIONS & FEEDBACK HISTORY ===================== --}}
            <section id="validated-history-section" class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                <div class="mb-4">
                    <p class="eyebrow uppercase mb-0.5">Completed Clinical Reviews</p>
                    <h2 class="text-lg font-bold tracking-tight">Recently Validated Developmental Indicators ({{ $validatedPredictions->count() }})</h2>
                    <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                        History of clinician-validated indicators and specialist feedback shared with caregivers.
                    </p>
                </div>

                @if ($validatedPredictions->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($validatedPredictions as $valPred)
                            <div class="border border-[var(--line)] rounded-xl p-4 bg-white flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="text-sm font-bold">{{ $valPred->child->first_name ?? 'Child' }}</span>
                                        <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ (string) $valPred->predicted_outcome === '1' ? 'bg-[var(--accent-tint)] text-[var(--accent)] border border-[var(--accent-soft)]' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                            {{ $valPred->caregiverIndicatorLabel() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-[var(--ink-soft)]">
                                        Validated · Generated {{ $valPred->date_generated?->format('M d, Y') ?? 'Recently' }}
                                    </p>
                                    @if ($valPred->clinician_feedback)
                                        <div class="mt-3 p-3 rounded-lg bg-[var(--canvas)] border border-[var(--line)] text-xs text-[var(--ink)]">
                                            <p class="font-semibold text-[11px] text-[var(--ink-soft)] uppercase mb-1">Specialist Feedback</p>
                                            <p class="leading-relaxed">{{ $valPred->clinician_feedback }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-[var(--ink-soft)] italic">
                        No predictions have been marked as validated yet. Use the Clinical Validation Queue above to validate pending indicators.
                    </p>
                @endif
            </section>

        </main>
    </div>
</div>

<script>
    function filterClinicianCaseload(searchQuery) {
        const q = (searchQuery || '').toLowerCase().trim();
        const statusFilter = document.getElementById('caseload-status-filter')?.value || 'all';
        const rows = document.querySelectorAll('.caseload-row');

        rows.forEach(function (row) {
            const text = (row.getAttribute('data-search') || '') + ' ' + (row.textContent || '').toLowerCase();
            const rowStatus = row.getAttribute('data-status') || '';
            const matchesSearch = !q || text.indexOf(q) > -1;
            const matchesStatus = statusFilter === 'all' || rowStatus === statusFilter;

            if (matchesSearch && matchesStatus) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }
</script>
</body>
</html>
