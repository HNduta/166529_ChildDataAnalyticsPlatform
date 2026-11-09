<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specialist Assessment · {{ $record->child->first_name }} · AutiSync</title>

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

        .field-label{ display:block; font-size:13px; font-weight:600; margin-bottom:4px; color:var(--ink); }
        .field-input{
            width:100%; border:1px solid var(--line); border-radius:10px; padding:10px 12px;
            font-size:14px; background:#fff; color:var(--ink); outline:none;
            transition:border-color .2s ease, box-shadow .2s ease;
        }
        .field-input:focus{ border-color:var(--ink); box-shadow:0 0 0 2px rgba(18,115,90,.12); }
    </style>
</head>
<body class="min-h-screen">
@php
    $clinician = auth()->user();
    $clinicianAvatarUrl = $clinician?->profilePictureUrl();
    $childAvatarUrl = $record->child?->profilePictureUrl();
@endphp

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
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
                    <a href="{{ route('clinician.children.show', $record->child) }}" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        {{ $record->child->first_name }} · Monitoring
                    </a>
                    <span class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                        Specialist Assessment
                    </span>
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

    {{-- MAIN --}}
    <div class="flex-1 min-w-0">

        {{-- Topbar --}}
        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('clinician.dashboard') }}" class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap">
                    ← Clinical Dashboard
                </a>
                <span class="text-[var(--line)]">|</span>
                <p class="font-semibold text-[15px]">Specialist Clinical Assessment</p>
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

        <main class="px-4 sm:px-8 py-8">
            <div class="max-w-4xl mx-auto space-y-6">

                {{-- Child & Observation Header --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 shrink-0 rounded-2xl bg-[var(--accent-tint)] border-2 border-[var(--accent-soft)] flex items-center justify-center overflow-hidden text-sm font-extrabold text-[var(--accent)]">
                            @if ($childAvatarUrl)
                                <img src="{{ $childAvatarUrl }}" alt="{{ $record->child->first_name }}" class="w-full h-full object-cover">
                            @else
                                {{ $record->child->initials() }}
                            @endif
                        </div>
                        <div>
                            <p class="eyebrow uppercase mb-0.5">Specialist Clinical Assessment</p>
                            <h1 class="text-2xl font-extrabold tracking-tight">Child Assessment Inputs · {{ $record->child->first_name }}</h1>
                            <p class="text-xs text-[var(--ink-soft)] mt-1">
                                Child: <strong>{{ $record->child->first_name }}</strong> · Observation date: <strong>{{ $record->observation_date?->format('M d, Y') ?? $record->observation_date }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button
                            type="button"
                            onclick="document.getElementById('clinical-reference-guide').classList.toggle('hidden')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-[var(--line)] bg-[var(--canvas)] px-3.5 py-2 text-xs font-semibold text-[var(--ink)] hover:bg-[#efe7dc] transition"
                        >
                            <svg class="w-4 h-4 text-[var(--ink)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v4h1"/></svg>
                            Clinical Reference &amp; Sample Profiles
                        </button>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-xs text-[var(--ink)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <span>Enter verified assessment results. Do not estimate specialist assessment scores or substitute behavioural ratings.</span>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button type="button" onclick="fillReferenceProfile('A')" class="px-2.5 py-1 rounded-md bg-white border border-[var(--line)] text-[11px] font-semibold text-[var(--ink)] hover:border-[var(--ink)] transition">
                            Auto-Fill Profile A (On-Track)
                        </button>
                        <button type="button" onclick="fillReferenceProfile('B')" class="px-2.5 py-1 rounded-md bg-white border border-[var(--line)] text-[11px] font-semibold text-[var(--accent)] hover:border-[var(--accent)] transition">
                            Auto-Fill Profile B (Needs Support)
                        </button>
                    </div>
                </div>

                {{-- Collapsible Clinical Definitions & Sample Profiles Guide --}}
                <div id="clinical-reference-guide" class="hidden bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="eyebrow uppercase">Standard Diagnostic Terminology Guide</p>
                            <h2 class="text-base font-bold tracking-tight mt-0.5">14-Domain Specialist Clinical Assessment Reference</h2>
                            <p class="text-xs text-[var(--ink-soft)] mt-1">
                                Use standard diagnostic terminology to input verified test results quickly and accurately. Two clinical reference profiles are provided for training and verification:
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('clinical-reference-guide').classList.add('hidden')" class="text-xs font-bold text-[var(--ink-soft)] hover:text-[var(--ink)]">✕</button>
                    </div>

                    <div class="overflow-x-auto -mx-1">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="text-left text-[var(--ink-soft)] font-semibold border-b border-[var(--line)]">
                                    <th class="py-2 px-2">Form Field Label</th>
                                    <th class="py-2 px-2">Clinical Definition</th>
                                    <th class="py-2 px-2">Valid Entry Format</th>
                                    <th class="py-2 px-2">Profile A (On-Track)</th>
                                    <th class="py-2 px-2">Profile B (Needs Support)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--line)] text-[11.5px]">
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Average success in first two sessions (%)</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The child's average task completion and engagement rate across their first two clinical observation or therapy sessions.</td>
                                    <td class="py-2 px-2">Number (0 to 100)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">68</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">35</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Age in years</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The child's chronological age. You can use decimals to represent months (e.g., type 4.0 or 4.5).</td>
                                    <td class="py-2 px-2">Number (1 to 18)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">4</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">3</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">ADOS-2 social affect score</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The raw score from the ADOS-2 Social Affect (SA) domain, measuring social reciprocity, joint attention, and communication.</td>
                                    <td class="py-2 px-2">Number (0 to 20)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">8</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">15</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">ADOS-2 restricted and repetitive behaviour score</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The raw score from the ADOS-2 Restricted and Repetitive Behaviors (RRB) domain, measuring sensory interests and rigid routines.</td>
                                    <td class="py-2 px-2">Number (0 to 10)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">3</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">6</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Cognitive verbal percentile</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The standardized percentile rank for verbal reasoning and language comprehension, compared to same-age peers.</td>
                                    <td class="py-2 px-2">Number (0 to 100)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">55</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">28</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Cognitive nonverbal percentile</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The standardized percentile rank for visual-spatial problem solving and nonverbal reasoning.</td>
                                    <td class="py-2 px-2">Number (0 to 100)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">62</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">34</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Cognitive GCA percentile</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The General Conceptual Ability (GCA) overall cognitive composite percentile.</td>
                                    <td class="py-2 px-2">Number (0 to 100)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">58</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">30</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Adaptive composite percentile</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The standardized adaptive behavior percentile (e.g., from the Vineland-3), assessing everyday self-care and socialization.</td>
                                    <td class="py-2 px-2">Number (0 to 100)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">52</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">25</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Number of current services</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The total number of active therapeutic interventions the child is receiving (e.g., Speech Therapy + Occupational Therapy = 2).</td>
                                    <td class="py-2 px-2">Whole number (0 to 10)</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">2</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">3</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Assessment domain</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The specific developmental area being evaluated during this assessment.</td>
                                    <td class="py-2 px-2">Communication, Social, Adaptive, Behavioral, Motor</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">Communication</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">Communication</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Sex / Gender</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The child's biological sex for standardized clinical norming.</td>
                                    <td class="py-2 px-2">Female or Male</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">Female</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">Female</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Support level</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The DSM-5 Autism Spectrum Disorder severity classification based on the required level of support.</td>
                                    <td class="py-2 px-2">Level 1, Level 2, or Level 3</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">Level 1</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">Level 2</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">Primary domain</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The child's main area of clinical focus for their long-term intervention plan.</td>
                                    <td class="py-2 px-2">Communication, Social, Adaptive, Behavioral, Sensory</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">Communication</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">Communication</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-semibold">ADOS-2 comparison score category</td>
                                    <td class="py-2 px-2 text-[var(--ink-soft)]">The overall ADOS-2 calibrated severity band indicating the level of autism spectrum-related symptoms.</td>
                                    <td class="py-2 px-2">Minimal to No, Low, Moderate, or High</td>
                                    <td class="py-2 px-2 font-bold text-emerald-700">Low</td>
                                    <td class="py-2 px-2 font-bold text-[var(--accent)]">High</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (session('status'))
                    <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="font-semibold">Please correct the following errors:</p>
                        <ul class="mt-2 list-disc list-inside text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8">
                    <form method="POST" action="{{ route('clinician.assessments.update', $record) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        @php
                            $numericFields = [
                                'avg_success_first_2' => [
                                    'label' => 'Average success in first two sessions (%)',
                                    'hint' => 'Number (0 to 100) · Average task completion and engagement rate across the first two sessions.',
                                    'step' => 'any',
                                    'min' => '0',
                                    'max' => '100',
                                ],
                                'age_years' => [
                                    'label' => 'Age in years',
                                    'hint' => 'Number (1 to 18) · Chronological age in years (decimals allowed for months, e.g. 4.0 or 4.5).',
                                    'step' => 'any',
                                    'min' => '0',
                                    'max' => '120',
                                ],
                                'ados2_social_affect' => [
                                    'label' => 'ADOS-2 social affect score',
                                    'hint' => 'Number (0 to 20) · Raw score from ADOS-2 Social Affect (SA) measuring social reciprocity and joint attention.',
                                    'step' => 'any',
                                    'min' => '0',
                                    'max' => '30',
                                ],
                                'ados2_rrb' => [
                                    'label' => 'ADOS-2 restricted and repetitive behaviour score',
                                    'hint' => 'Number (0 to 10) · Raw score from ADOS-2 RRB domain measuring sensory interests and rigid routines.',
                                    'step' => 'any',
                                    'min' => '0',
                                    'max' => '20',
                                ],
                                'cognitive_verbal_percentile' => [
                                    'label' => 'Cognitive verbal percentile',
                                    'hint' => 'Percentile (0 to 100) · Standardized verbal reasoning and language comprehension rank.',
                                    'step' => 'any',
                                    'min' => '0',
                                    'max' => '100',
                                ],
                                'cognitive_nonverbal_percentile' => [
                                    'label' => 'Cognitive nonverbal percentile',
                                    'hint' => 'Percentile (0 to 100) · Standardized visual-spatial problem solving and nonverbal reasoning rank.',
                                    'step' => 'any',
                                    'min' => '0',
                                    'max' => '100',
                                ],
                                'cognitive_gca_percentile' => [
                                    'label' => 'Cognitive GCA percentile',
                                    'hint' => 'Percentile (0 to 100) · General Conceptual Ability (GCA) overall cognitive composite percentile.',
                                    'step' => 'any',
                                    'min' => '0',
                                    'max' => '100',
                                ],
                                'adaptive_composite_percentile' => [
                                    'label' => 'Adaptive composite percentile',
                                    'hint' => 'Percentile (0 to 100) · Standardized adaptive behavior percentile (e.g., Vineland-3 everyday self-care).',
                                    'step' => 'any',
                                    'min' => '0',
                                    'max' => '100',
                                ],
                                'num_current_services' => [
                                    'label' => 'Number of current services',
                                    'hint' => 'Whole number (0 to 10) · Total active therapeutic interventions (e.g., Speech + OT = 2).',
                                    'step' => '1',
                                    'min' => '0',
                                    'max' => '20',
                                ],
                            ];

                            $selectFields = [
                                'domain' => [
                                    'label' => 'Assessment domain',
                                    'hint' => 'Specific developmental area being evaluated during this assessment.',
                                    'options' => ['Communication', 'Social', 'Adaptive', 'Behavioral', 'Motor', 'Cognitive'],
                                ],
                                'gender' => [
                                    'label' => 'Sex / Gender',
                                    'hint' => 'Biological sex for standardized clinical norming.',
                                    'options' => ['Female', 'Male', 'Other'],
                                ],
                                'support_level' => [
                                    'label' => 'Support level',
                                    'hint' => 'DSM-5 ASD severity classification based on required level of support.',
                                    'options' => ['Level 1', 'Level 2', 'Level 3'],
                                ],
                                'primary_domain' => [
                                    'label' => 'Primary domain',
                                    'hint' => 'Main area of clinical focus for the long-term intervention plan.',
                                    'options' => ['Communication', 'Social', 'Adaptive', 'Behavioral', 'Sensory', 'Cognitive'],
                                ],
                                'ados2_comparison_score' => [
                                    'label' => 'ADOS-2 comparison score category',
                                    'hint' => 'Overall ADOS-2 calibrated severity band.',
                                    'options' => ['Minimal to No', 'Low', 'Moderate', 'High'],
                                ],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            @foreach ($numericFields as $name => $meta)
                                <div>
                                    <label for="{{ $name }}" class="field-label">
                                        {{ $meta['label'] }}
                                    </label>
                                    <input
                                        id="{{ $name }}"
                                        name="{{ $name }}"
                                        type="number"
                                        step="{{ $meta['step'] }}"
                                        min="{{ $meta['min'] }}"
                                        max="{{ $meta['max'] }}"
                                        value="{{ old($name, $inputs[$name] ?? '') }}"
                                        required
                                        class="field-input"
                                    >
                                    <p class="text-[11px] text-[var(--ink-soft)] mt-1 leading-snug">{{ $meta['hint'] }}</p>
                                </div>
                            @endforeach

                            @foreach ($selectFields as $name => $meta)
                                @php
                                    $currentVal = (string) old($name, $inputs[$name] ?? '');
                                    $optionsList = $meta['options'];
                                    if ($currentVal !== '' && !in_array($currentVal, $optionsList, true)) {
                                        $optionsList[] = $currentVal;
                                    }
                                @endphp
                                <div>
                                    <label for="{{ $name }}" class="field-label">
                                        {{ $meta['label'] }}
                                    </label>
                                    <select id="{{ $name }}" name="{{ $name }}" required class="field-input">
                                        <option value="">Select {{ strtolower($meta['label']) }}...</option>
                                        @foreach ($optionsList as $opt)
                                            <option value="{{ $opt }}" {{ strtolower($currentVal) === strtolower($opt) ? 'selected' : '' }}>
                                                {{ $opt }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] text-[var(--ink-soft)] mt-1 leading-snug">{{ $meta['hint'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4 border-t border-[var(--line)] flex flex-wrap items-center justify-end gap-3">
                            <a href="{{ route('clinician.children.show', $record->child) }}" class="px-4 py-2.5 rounded-lg border border-[var(--line)] bg-[var(--canvas)] text-xs font-semibold text-[var(--ink)] hover:bg-[#efe7dc] transition">
                                Monitor {{ $record->child->first_name }} &amp; Goals
                            </a>
                            <a href="{{ route('clinician.dashboard') }}" class="px-4 py-2.5 rounded-lg border border-[var(--line)] bg-[var(--canvas)] text-xs font-semibold text-[var(--ink)] hover:bg-[#efe7dc] transition">
                                Back to Dashboard
                            </a>
                            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[var(--ink)] text-white text-sm font-semibold hover:opacity-90 transition">
                                Save assessment
                            </button>
                        </div>
                    </form>

                    @if (\App\Http\Controllers\ClinicianDashboardController::hasCompleteAssessment($record))
                        <div class="mt-6 pt-5 border-t border-[var(--line)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-[var(--canvas)]/60 rounded-xl p-4">
                            <div>
                                <p class="text-xs font-bold text-[var(--ink)]">14-Domain Assessment Complete</p>
                                <p class="text-xs text-[var(--ink-soft)] mt-0.5">
                                    You can now calculate or update {{ $record->child->first_name }}'s clinical progress status and caregiver home support guides.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('behavioural-records.predict', $record) }}" class="shrink-0">
                                @csrf
                                <input type="hidden" name="redirect_to" value="/clinician/children/{{ $record->child->id }}">
                                <button type="submit" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Calculate Clinical Progress Status
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>
</div>

<script>
    function fillReferenceProfile(profile) {
        const profiles = {
            A: {
                avg_success_first_2: 68,
                age_years: 4,
                ados2_social_affect: 8,
                ados2_rrb: 3,
                cognitive_verbal_percentile: 55,
                cognitive_nonverbal_percentile: 62,
                cognitive_gca_percentile: 58,
                adaptive_composite_percentile: 52,
                num_current_services: 2,
                domain: 'Communication',
                gender: 'Female',
                support_level: 'Level 1',
                primary_domain: 'Communication',
                ados2_comparison_score: 'Low'
            },
            B: {
                avg_success_first_2: 35,
                age_years: 3,
                ados2_social_affect: 15,
                ados2_rrb: 6,
                cognitive_verbal_percentile: 28,
                cognitive_nonverbal_percentile: 34,
                cognitive_gca_percentile: 30,
                adaptive_composite_percentile: 25,
                num_current_services: 3,
                domain: 'Communication',
                gender: 'Female',
                support_level: 'Level 2',
                primary_domain: 'Communication',
                ados2_comparison_score: 'High'
            }
        };

        const values = profiles[profile];
        if (!values) return;

        Object.keys(values).forEach(function (field) {
            const el = document.getElementById(field);
            if (!el) return;
            el.value = values[field];
        });
    }
</script>
</body>
</html>
