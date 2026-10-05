<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Child · AutiSync</title>

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

        button:focus-visible, a:focus-visible, input:focus-visible { outline:2px solid var(--ink); outline-offset:2px; }

        .field-label{ display:block; font-size:13px; font-weight:600; margin-bottom:6px; }
        .field-input{
            width:100%; border:1px solid var(--line); border-radius:10px; padding:10px 12px 10px 38px;
            font-size:14px; background:#fff; color:var(--ink); outline:none;
            transition:border-color .2s ease, box-shadow .2s ease;
        }
        .field-input:focus{ border-color:var(--ink); box-shadow:0 0 0 2px rgba(18,115,90,.12); }
        .field-input::placeholder{ color:#a89f93; }
        .field-icon{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--ink-soft); pointer-events:none; }
        select.field-input{ appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%236f6a62' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:36px; }
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
                    <a href="{{ route('children.index') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        Child Profile
                    </a>
                </div>
            </div>

            <div>
                <p class="eyebrow px-3 mb-2 uppercase">Behavior</p>
                <div class="space-y-1">
                    <a href="#" class="navlink">
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
        <header class="h-16 flex items-center gap-4 px-4 sm:px-8 border-b border-[var(--line)] bg-white sticky top-0 z-10">
            <p class="hidden sm:block font-semibold text-[15px]">Child Profile</p>

            <div class="flex-1 max-w-md ml-0 sm:ml-6">
                <label class="relative block">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input type="text" placeholder="Search records..." disabled
                           class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-3 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:outline-none">
                </label>
            </div>

            <div class="ml-auto flex items-center gap-4">
                <button class="relative w-9 h-9 rounded-lg border border-[var(--line)] flex items-center justify-center" aria-label="Notifications">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 8a6 6 0 1112 0c0 5 2 6 2 6H4s2-1 2-6z"/><path d="M10 21a2 2 0 004 0"/></svg>
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center text-xs font-semibold">
                        {{-- DATA: auth()->user() initials --}}
                        CG
                    </div>
                    <div class="hidden sm:block leading-tight">
                        {{-- DATA: auth()->user()->name --}}
                        <p class="text-[13px] font-semibold">Caregiver</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">Caregiver</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-4 sm:px-8 py-8">

            {{-- Page heading --}}
            <div class="mb-6">
                <h1 class="text-2xl font-extrabold tracking-tight">Add Child</h1>
                <p class="text-sm text-[var(--ink-soft)] mt-1">Create a profile to begin monitoring developmental progress.</p>
            </div>

            <div class="max-w-3xl">

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                        <p class="text-sm font-semibold text-red-700">Please correct the following errors:</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form card --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8">

                    <form method="POST" action="{{ route('children.store') }}">
                        @csrf

                        {{-- Child's name --}}
                        <div class="mb-6">
                            <label for="first_name" class="field-label">Child's First Name</label>
                            <div class="relative">
                                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                                       required maxlength="255" placeholder="Enter child's first name" class="field-input">
                            </div>
                        </div>

                        {{-- Age --}}
                        <div class="mb-6">
                            <label for="age_in_months" class="field-label">Age in Months</label>
                            <div class="relative">
                                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/></svg>
                                <input id="age_in_months" type="number" name="age_in_months" value="{{ old('age_in_months') }}"
                                       required min="0" max="216" placeholder="e.g. 48" class="field-input">
                            </div>
                            <p class="mt-1.5 text-xs text-[var(--ink-soft)]">Enter the child's age in months.</p>
                        </div>

                        {{-- Sex --}}
                        <div class="mb-6">
                            <label for="sex" class="field-label">Sex</label>
                            <div class="relative">
                                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M17 11a3 3 0 100-6 3 3 0 000 6zM21 21c0-3-2-5-4.5-5.5"/></svg>
                                <select id="sex" name="sex" required class="field-input">
                                    <option value="">Select sex</option>
                                    <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('sex') === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        {{-- Background information --}}
                        <div class="border-t border-[var(--line)] pt-6 mb-6">
                            <h2 class="text-base font-bold tracking-tight">Background Information</h2>
                            <p class="text-sm text-[var(--ink-soft)] mt-1 mb-5">Provide the following information where known.</p>

                            {{-- Jaundice --}}
                            <div class="mb-5">
                                <label for="born_with_jaundice" class="field-label">Was the child born with jaundice?</label>
                                <div class="relative">
                                    <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3c3 4 6 7 6 10a6 6 0 11-12 0c0-3 3-6 6-10z"/></svg>
                                    <select id="born_with_jaundice" name="born_with_jaundice" class="field-input">
                                        <option value="">Not specified</option>
                                        <option value="1" {{ old('born_with_jaundice') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('born_with_jaundice') === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Family member with ASD --}}
                            <div class="mb-5">
                                <label for="family_member_with_asd" class="field-label">Is there a family member with ASD?</label>
                                <div class="relative">
                                    <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 10-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/></svg>
                                    <select id="family_member_with_asd" name="family_member_with_asd" class="field-input">
                                        <option value="">Not specified</option>
                                        <option value="1" {{ old('family_member_with_asd') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('family_member_with_asd') === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Diagnosis --}}
                            <div>
                                <label for="diagnosis_confirmed" class="field-label">Has the child's ASD diagnosis been confirmed?</label>
                                <div class="relative">
                                    <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>
                                    <select id="diagnosis_confirmed" name="diagnosis_confirmed" class="field-input">
                                        <option value="0" {{ old('diagnosis_confirmed', '0') === '0' ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('diagnosis_confirmed') === '1' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <a href="{{ route('children.index') }}"
                               class="flex-1 text-center px-5 py-2.5 border border-[var(--accent-soft)] text-[var(--accent)] rounded-lg text-sm font-semibold hover:bg-[var(--accent-tint)] transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="flex-1 px-5 py-2.5 bg-[var(--ink)] text-white rounded-lg text-sm font-semibold hover:opacity-90 transition">
                                Create Child Profile
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