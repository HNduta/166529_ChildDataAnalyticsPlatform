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

        .field-label{ display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:var(--ink); }
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
                <p class="font-semibold text-[15px]">Child Assessment Inputs</p>
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
                </div>

                <div class="p-4 rounded-xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-xs text-[var(--ink)]">
                    Enter verified assessment results. Do not estimate specialist assessment scores or substitute behavioural ratings.
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
                            $fields = [
                                'avg_success_first_2' => 'Average success in first two sessions (%)',
                                'age_years' => 'Age in years',
                                'ados2_social_affect' => 'ADOS-2 social affect score',
                                'ados2_rrb' => 'ADOS-2 restricted and repetitive behaviour score',
                                'cognitive_verbal_percentile' => 'Cognitive verbal percentile',
                                'cognitive_nonverbal_percentile' => 'Cognitive nonverbal percentile',
                                'cognitive_gca_percentile' => 'Cognitive GCA percentile',
                                'adaptive_composite_percentile' => 'Adaptive composite percentile',
                                'num_current_services' => 'Number of current services',
                                'domain' => 'Assessment domain',
                                'gender' => 'Gender category used by the model',
                                'support_level' => 'Support level',
                                'primary_domain' => 'Primary domain',
                                'ados2_comparison_score' => 'ADOS-2 comparison score category',
                            ];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            @foreach ($fields as $name => $label)
                                <div>
                                    <label for="{{ $name }}" class="field-label">
                                        {{ $label }}
                                    </label>

                                    <input
                                        id="{{ $name }}"
                                        name="{{ $name }}"
                                        type="{{ in_array($name, [
                                            'domain',
                                            'gender',
                                            'support_level',
                                            'primary_domain',
                                            'ados2_comparison_score'
                                        ]) ? 'text' : 'number' }}"
                                        value="{{ old($name, $inputs[$name] ?? '') }}"
                                        @if (!in_array($name, [
                                            'domain',
                                            'gender',
                                            'support_level',
                                            'primary_domain',
                                            'ados2_comparison_score'
                                        ]))
                                            step="any"
                                        @endif
                                        required
                                        class="field-input"
                                    >
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4 border-t border-[var(--line)] flex flex-wrap items-center justify-end gap-3">
                            <a href="{{ route('clinician.dashboard') }}" class="px-4 py-2.5 rounded-lg border border-[var(--line)] bg-[var(--canvas)] text-xs font-semibold text-[var(--ink)] hover:bg-[#efe7dc] transition">
                                Back to Dashboard
                            </a>
                            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[var(--ink)] text-white text-sm font-semibold hover:opacity-90 transition">
                                Save assessment
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>
</div>
</body>
</html>
