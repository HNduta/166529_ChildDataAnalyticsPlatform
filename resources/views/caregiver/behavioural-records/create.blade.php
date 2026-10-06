<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Observation Form · AutiSync</title>

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

        button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible { outline:2px solid var(--ink); outline-offset:2px; }

        .field-label{ display:block; font-size:13px; font-weight:600; color:var(--ink); margin-bottom:6px; }

        .field-input{
            width:100%; border:1px solid var(--line); border-radius:10px; padding:10px 12px 10px 38px;
            font-size:14px; background:#fff; color:var(--ink); outline:none;
            transition:border-color .2s ease, box-shadow .2s ease;
        }
        .field-input:focus{ border-color:var(--ink); box-shadow:0 0 0 2px rgba(18,115,90,.12); }
        .field-input::placeholder{ color:#a89f93; }

        .field-icon{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--ink-soft); pointer-events:none; }

        select.field-input{
            appearance:none;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%236f6a62' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat:no-repeat; background-position:right 12px center; padding-right:36px;
        }

        textarea.field-input{ resize:vertical; padding-left:12px; }
    </style>
</head>
<body class="min-h-screen">
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
                    <a href="{{ url('/caregiver/dashboard') }}" class="navlink">
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
                    <a href="{{ route('behavioural-records.create') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                        Observation Form
                    </a>
                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19V6M10 19V10M16 19V4M22 19V13"/></svg>
                        Records
                    </a>
                </div>
            </div>

            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Progress</p>
                <div class="space-y-1">
                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r=".5"/></svg>
                        Goals &amp; Milestones
                    </a>
                </div>
            </div>

            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Insights</p>
                <div class="space-y-1">
                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18h6M10 22h4M12 2a6 6 0 00-4 10.4c.6.6.9 1 .9 1.8V15h6.2v-.8c0-.8.3-1.2.9-1.8A6 6 0 0012 2z"/></svg>
                        ML Predictions
                    </a>
                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
                        Analytics
                    </a>
                </div>
            </div>

            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Support</p>
                <div class="space-y-1">
                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 10-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/></svg>
                        Recommendations
                    </a>
                </div>
            </div>
        </nav>
    </aside>


    {{-- ============================= MAIN ============================= --}}
    <div class="flex-1 min-w-0">

        {{-- Topbar --}}
        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white sticky top-0 z-10">

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('caregiver.dashboard') }}"
                    class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap"
                >
                    ← Dashboard
                </a>

                <span class="text-[var(--line)]">|</span>

                <span class="text-sm font-bold text-[var(--ink)] whitespace-nowrap">
                    Record Observation
                </span>
            </div>

            <div class="hidden md:block flex-1 max-w-md ml-2">
                <label class="relative block">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input type="text" placeholder="Search records..." disabled
                           class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-3 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:outline-none">
                </label>
            </div>

            <div class="ml-auto flex items-center gap-3">

                <button type="button" class="relative w-9 h-9 rounded-lg border border-[var(--line)] flex items-center justify-center" aria-label="Notifications">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8a6 6 0 1112 0c0 5 2 6 2 6H4s2-1 2-6z"/><path d="M10 21a2 2 0 004 0"/></svg>
                </button>

                <div class="flex items-center gap-2.5">

                    <div class="w-8 h-8 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>

                    <div class="hidden sm:block leading-tight">
                        <p class="text-[13px] font-semibold">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-[11px] text-[var(--ink-soft)]">
                            {{ ucfirst(auth()->user()->role) }}
                        </p>
                    </div>

                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition whitespace-nowrap"
                    >
                        Logout
                    </button>
                </form>

            </div>

        </header>


        {{-- Main Content --}}
        <main class="px-4 sm:px-8 py-8">
        <div class="max-w-6xl mx-auto">

            <div class="mb-6 pb-6 border-b border-[var(--line)]">
                <p class="flex items-center gap-2 text-[11px] uppercase tracking-[0.12em] font-semibold text-[var(--ink-soft)] mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                    Session Documentation
                </p>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[var(--ink)]">
                    Behavioural Observation Form
                </h1>

                <p class="mt-2 text-sm text-[var(--ink-soft)] max-w-2xl">
                    Record how your child did during today’s session. Every entry helps track their unique growth and tailor insights directly for them.
                </p>
            </div>

            {{-- Section jump links (anchors only) --}}
            <nav class="bg-white border border-[var(--line)] rounded-xl mb-6 flex items-center overflow-x-auto" aria-label="Form sections">
                <a href="#session-details" class="flex-1 text-center px-6 py-3 text-sm font-semibold text-[var(--ink-soft)] hover:text-[var(--accent)] whitespace-nowrap">Session</a>
                <a href="#success-rate-section" class="flex-1 text-center px-6 py-3 text-sm font-semibold text-[var(--ink-soft)] hover:text-[var(--accent)] whitespace-nowrap">Success Rate</a>
                <a href="#additional-metrics" class="flex-1 text-center px-6 py-3 text-sm font-semibold text-[var(--ink-soft)] hover:text-[var(--accent)] whitespace-nowrap">Metrics</a>
                <a href="#notes-section" class="flex-1 text-center px-6 py-3 text-sm font-semibold text-[var(--ink-soft)] hover:text-[var(--accent)] whitespace-nowrap">Notes</a>
            </nav>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-semibold text-red-700 mb-2">
                        Please correct the following errors:
                    </p>
                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-6 items-start">

                {{-- ===================== FORM ===================== --}}
                <form
                    id="observation-form"
                    method="POST"
                    action="{{ route('behavioural-records.store') }}"
                    class="space-y-6"
                >
                    @csrf

                    {{-- ----- Session Details ----- --}}
                    <section id="session-details" class="bg-white border border-[var(--line)] rounded-2xl scroll-mt-24">

                        <div class="px-5 sm:px-7 pt-5 sm:pt-6 pb-4 border-b border-[var(--line)]">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/></svg>
                                </span>
                                <h2 class="text-lg font-bold tracking-tight text-[var(--ink)]">Session Details</h2>
                            </div>
                            <p class="text-[12.5px] text-[var(--ink-soft)] mt-1.5">Who this observation is about and when it happened.</p>
                        </div>

                        <div class="p-5 sm:p-7">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                                {{-- Child --}}
                                <div class="sm:col-span-2">
                                    <label for="child_id" class="field-label">Child</label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                                        <select id="child_id" name="child_id" required class="field-input">
                                            <option value="">Select child</option>
                                            @foreach ($children as $child)
                                                <option
                                                    value="{{ $child->id }}"
                                                    data-name="{{ $child->first_name }}"
                                                    data-age="{{ $child->age_in_months }}"
                                                    {{ old('child_id') == $child->id ? 'selected' : '' }}
                                                >
                                                    {{ $child->first_name }} — {{ $child->age_in_months }} months
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if ($children->isEmpty())
                                        <p class="mt-2 text-sm text-[var(--ink-soft)]">
                                            You do not have any child profiles yet.
                                            <a href="{{ route('children.create') }}" class="font-semibold text-[var(--accent)] underline">
                                                Add a child profile first.
                                            </a>
                                        </p>
                                    @endif
                                </div>

                                {{-- Observation Date --}}
                                <div>
                                    <label for="observation_date" class="field-label">Observation Date</label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/></svg>
                                        <input
                                            type="date"
                                            id="observation_date"
                                            name="observation_date"
                                            value="{{ old('observation_date', now()->format('Y-m-d')) }}"
                                            max="{{ now()->format('Y-m-d') }}"
                                            required
                                            class="field-input"
                                        >
                                    </div>
                                </div>

                                {{-- Session Duration --}}
                                <div>
                                    <label for="session_duration_minutes" class="field-label">
                                        Session Duration <span class="font-normal text-[var(--ink-soft)]">(optional)</span>
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex-1">
                                            <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                            <input
                                                type="number"
                                                id="session_duration_minutes"
                                                name="session_duration_minutes"
                                                value="{{ old('session_duration_minutes') }}"
                                                min="1"
                                                max="480"
                                                placeholder="e.g. 30"
                                                class="field-input"
                                            >
                                        </div>
                                        <span class="text-sm text-[var(--ink-soft)] whitespace-nowrap">minutes</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    {{-- ----- Success Rate (the model input) ----- --}}
                    <section id="success-rate-section" class="border border-[var(--accent-soft)] rounded-2xl p-5 sm:p-7 bg-[var(--accent-tint)] scroll-mt-24">
                        <div class="flex items-center gap-2.5 mb-4">
                            <span class="w-8 h-8 rounded-lg bg-white border border-[var(--accent-soft)] text-[var(--accent)] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r=".5"/></svg>
                            </span>
                            <label for="success_rate" class="field-label !mb-0">
                                Success Rate
                                <span class="font-normal text-[var(--ink-soft)]">— % of tasks/prompts completed successfully this session</span>
                            </label>
                        </div>
                        <div class="flex items-center gap-3 max-w-xs">
                            <div class="relative flex-1">
                                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17l6-6 4 4 8-8"/></svg>
                                <input
                                    type="number"
                                    id="success_rate"
                                    name="success_rate"
                                    value="{{ old('success_rate') }}"
                                    min="0"
                                    max="100"
                                    step="0.1"
                                    required
                                    placeholder="e.g. 75"
                                    class="field-input"
                                >
                            </div>
                            <span class="text-sm text-[var(--ink-soft)]">%</span>
                        </div>
                        <p class="mt-3 text-[12px] text-[var(--ink-soft)]">
                            An approximate estimate works great! Consistency matters more than perfection.
                        </p>
                    </section>

                    {{-- ----- Additional Behavioural Metrics (optional) ----- --}}
                    <section id="additional-metrics" class="bg-white border border-[var(--line)] rounded-2xl scroll-mt-24">

                        <div class="px-5 sm:px-7 pt-5 sm:pt-6 pb-4 border-b border-[var(--line)]">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V6M10 19V10M16 19V4M22 19V13"/></svg>
                                </span>
                                <h2 class="text-lg font-bold tracking-tight text-[var(--ink)]">Additional Observations</h2>
                            </div>
                            <p class="text-[12.5px] text-[var(--ink-soft)] mt-1.5">
                                Add extra details about engagement or prompts to enrich your child's progress profile. Fill in as much or as little as you'd like.
                            </p>
                        </div>

                        <div class="p-5 sm:p-7">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                                {{-- Engagement Level --}}
                                <div>
                                    <label for="engagement_level" class="field-label">Engagement Level <span class="font-normal text-[var(--ink-soft)]">(1–5)</span></label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h4l2-7 4 14 2-7h6"/></svg>
                                        <select id="engagement_level" name="engagement_level" class="field-input">
                                            <option value="">Not recorded</option>
                                            @for ($score = 1; $score <= 5; $score++)
                                                <option value="{{ $score }}" {{ old('engagement_level') == $score ? 'selected' : '' }}>{{ $score }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                {{-- Eye Contact Rating --}}
                                <div>
                                    <label for="eye_contact_rating" class="field-label">Eye Contact Rating <span class="font-normal text-[var(--ink-soft)]">(1–5)</span></label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <select id="eye_contact_rating" name="eye_contact_rating" class="field-input">
                                            <option value="">Not recorded</option>
                                            @for ($score = 1; $score <= 5; $score++)
                                                <option value="{{ $score }}" {{ old('eye_contact_rating') == $score ? 'selected' : '' }}>{{ $score }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                {{-- Prompts Required --}}
                                <div>
                                    <label for="prompts_required" class="field-label">Prompts Required</label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                        <input
                                            type="number"
                                            id="prompts_required"
                                            name="prompts_required"
                                            value="{{ old('prompts_required') }}"
                                            min="0"
                                            step="1"
                                            placeholder="e.g. 4"
                                            class="field-input"
                                        >
                                    </div>
                                </div>

                                <div class="hidden sm:block"></div>

                                {{-- Communication --}}
                                <div>
                                    <label for="communication_score" class="field-label">Communication Score <span class="font-normal text-[var(--ink-soft)]">(1–5)</span></label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                        <select id="communication_score" name="communication_score" class="field-input">
                                            <option value="">Not recorded</option>
                                            @for ($score = 1; $score <= 5; $score++)
                                                <option value="{{ $score }}" {{ old('communication_score') == $score ? 'selected' : '' }}>{{ $score }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                {{-- Social Interaction --}}
                                <div>
                                    <label for="social_interaction_score" class="field-label">Social Interaction Score <span class="font-normal text-[var(--ink-soft)]">(1–5)</span></label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M17 11a3 3 0 100-6 3 3 0 000 6zM21 21c0-3-2-5-4.5-5.5"/></svg>
                                        <select id="social_interaction_score" name="social_interaction_score" class="field-input">
                                            <option value="">Not recorded</option>
                                            @for ($score = 1; $score <= 5; $score++)
                                                <option value="{{ $score }}" {{ old('social_interaction_score') == $score ? 'selected' : '' }}>{{ $score }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                {{-- Repetitive Behaviour --}}
                                <div>
                                    <label for="repetitive_behaviour_score" class="field-label">Repetitive Behaviour Score <span class="font-normal text-[var(--ink-soft)]">(1–5)</span></label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a3 3 0 013-3h15"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a3 3 0 01-3 3H3"/></svg>
                                        <select id="repetitive_behaviour_score" name="repetitive_behaviour_score" class="field-input">
                                            <option value="">Not recorded</option>
                                            @for ($score = 1; $score <= 5; $score++)
                                                <option value="{{ $score }}" {{ old('repetitive_behaviour_score') == $score ? 'selected' : '' }}>{{ $score }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    {{-- ----- Notes ----- --}}
                    <section id="notes-section" class="bg-white border border-[var(--line)] rounded-2xl scroll-mt-24">

                        <div class="px-5 sm:px-7 pt-5 sm:pt-6 pb-4 border-b border-[var(--line)]">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                                </span>
                                <h2 class="text-lg font-bold tracking-tight text-[var(--ink)]">
                                    <label for="notes">
                                        Notes <span class="text-[13px] font-normal text-[var(--ink-soft)]">(optional)</span>
                                    </label>
                                </h2>
                            </div>
                        </div>

                        <div class="p-5 sm:p-7">
                            <textarea
                                id="notes"
                                name="notes"
                                rows="5"
                                maxlength="2000"
                                placeholder="Add any relevant observations about the session..."
                                class="field-input"
                            >{{ old('notes') }}</textarea>
                        </div>
                    </section>

                    {{-- Actions --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                        <a
                            href="{{ route('caregiver.dashboard') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-[var(--accent-soft)] bg-white text-sm font-semibold text-[var(--accent)] hover:bg-[var(--accent-tint)] transition"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            @disabled($children->isEmpty())
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-[var(--ink)] text-white text-sm font-semibold hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Save Observation
                        </button>
                    </div>

                </form>

                {{-- ===================== SIDE COLUMN ===================== --}}
                <div class="space-y-5">

                    {{-- Selected child snapshot --}}
                    <div id="child-snapshot" class="bg-white border border-[var(--line)] rounded-2xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)] flex items-center justify-center text-sm font-bold" id="child-snapshot-avatar">
                                —
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[var(--ink)]" id="child-snapshot-name">No child selected</p>
                                <p class="text-[12px] text-[var(--ink-soft)]" id="child-snapshot-age">Choose a child above</p>
                            </div>
                        </div>
                    </div>

                    {{-- Why success rate matters --}}
                    <div class="bg-white border border-[var(--accent-soft)] rounded-2xl p-5 flex gap-3">
                        <span class="w-9 h-9 shrink-0 rounded-lg bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v5h1"/></svg>
                        </span>
                        <div>
                            <h3 class="text-[13px] font-bold text-[var(--ink)] mb-1.5">How This Helps</h3>
                            <p class="text-[12.5px] text-[var(--ink-soft)] leading-relaxed">
                                Tracking completion rates consistently helps us spot trends over time. 
                                Even if you don't have time to fill out the rest of the form, logging this key number helps generate accurate
                                progress insights for your child.
                            </p>
                        </div>
                    </div>

                    {{-- Recording guidelines (native accordion, no JS needed) --}}
                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                        <h3 class="flex items-center gap-2 text-[12px] font-bold uppercase tracking-wide text-[var(--ink)] mb-3">
                            <svg class="w-4 h-4 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 .5c0 1.5-2.5 2-2.5 3.5M12 17h.01"/></svg>
                            Quick Tips & Advice
                        </h3>

                        <details class="group border-t border-[var(--line)] py-2.5">
                            <summary class="flex items-center justify-between text-[13px] font-semibold text-[var(--ink)] cursor-pointer list-none">
                                What counts as a successful task?
                                <svg class="w-3.5 h-3.5 text-[var(--accent)] transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </summary>
                            <p class="mt-2 text-[12.5px] text-[var(--ink-soft)] leading-relaxed">
                                Mark a task as completed if your child finished it with the usual amount of support you give during this activity.
                            </p>
                        </details>

                        <details class="group border-t border-[var(--line)] py-2.5">
                            <summary class="flex items-center justify-between text-[13px] font-semibold text-[var(--ink)] cursor-pointer list-none">
                                Engagement vs. Eye Contact
                                <svg class="w-3.5 h-3.5 text-[var(--accent)] transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </summary>
                            <p class="mt-2 text-[12.5px] text-[var(--ink-soft)] leading-relaxed">
                                Focus on your child's interest and participation in the activity. 
                                Eye contact is logged separately and doesn't affect a high engagement rating.
                            </p>
                        </details>

                        <details class="group border-t border-b border-[var(--line)] py-2.5">
                            <summary class="flex items-center justify-between text-[13px] font-semibold text-[var(--ink)] cursor-pointer list-none">
                                Writing Helpful Notes
                                <svg class="w-3.5 h-3.5 text-[var(--accent)] transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </summary>
                            <p class="mt-2 text-[12.5px] text-[var(--ink-soft)] leading-relaxed">
                                Try describing specific actions you saw rather than guessing intent. For example, "turned away during transitions" gives a clearer picture than "didn't want to participate."
                            </p>
                        </details>
                    </div>

                </div>

            </div>

        </div>
        </main>

    </div>

</div>

{{-- Lightweight progressive enhancement — purely cosmetic, reads only the already-selected option's own text --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('child_id');
        const avatar = document.getElementById('child-snapshot-avatar');
        const name = document.getElementById('child-snapshot-name');
        const age = document.getElementById('child-snapshot-age');

        if (!select) return;

        function updateSnapshot() {
            const option = select.options[select.selectedIndex];
            const childName = option ? option.dataset.name : null;
            const childAge = option ? option.dataset.age : null;

            if (childName) {
                avatar.textContent = childName.substring(0, 2).toUpperCase();
                name.textContent = childName;
                age.textContent = childAge ? childAge + ' months old' : '';
            } else {
                avatar.textContent = '—';
                name.textContent = 'No child selected';
                age.textContent = 'Choose a child above';
            }
        }

        select.addEventListener('change', updateSnapshot);
        updateSnapshot();
    });
</script>

</body>
</html>