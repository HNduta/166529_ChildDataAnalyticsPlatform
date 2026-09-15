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
            --ink:#2f4f3a;
            --ink-soft:#6b7d70;
            --line:#dde5df;
            --surface:#ffffff;
            --canvas:#f3f6f3;
            --accent:#2f4f3a;
            --amber-bg:#fdf6e9;
            --amber-line:#f1e2bd;
            --amber-ink:#8a6d1d;
        }
        html,body{ font-family:'Inter',ui-sans-serif,system-ui,sans-serif; background:var(--canvas); color:var(--ink); }
        .eyebrow{ font-size:11px; letter-spacing:.06em; color:var(--ink-soft); font-weight:600; }

        /* Skeleton shimmer for "data will load here" placeholders */
        .skel{
            position:relative;
            overflow:hidden;
            background:#eceef1;
            border-radius:8px;
        }
        .skel::after{
            content:"";
            position:absolute; inset:0;
            transform:translateX(-100%);
            background:linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.75) 50%, rgba(255,255,255,0) 100%);
            animation:shimmer 1.8s infinite;
        }
        @media (prefers-reduced-motion: reduce){ .skel::after{ animation:none; } }
        @keyframes shimmer{ 100%{ transform:translateX(100%); } }

        .scrollbar-thin::-webkit-scrollbar{ width:6px; }
        .scrollbar-thin::-webkit-scrollbar-thumb{ background:#d8d8dc; border-radius:999px; }

        .navlink{ display:flex; align-items:center; gap:10px; padding:8px 12px; border-radius:8px; font-size:14px; color:#45464d; font-weight:500; }
        .navlink:hover{ background:#f1f1f3; color:var(--ink); }
        .navlink.active{ background:var(--ink); color:#fff; }
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
                <p class="eyebrow px-3 mb-2 uppercase">Clinician</p>
                <div class="space-y-1">
                    <a href="{{ url('/clinician/dashboard') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        Clinical Dashboard
                    </a>
                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                        Validation Workflow
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
                        DR
                    </div>
                    <div class="hidden sm:block leading-tight">
                        {{-- DATA: auth()->user()->name --}}
                        <p class="text-[13px] font-semibold">Dr. Clinician</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">Clinician</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-4 sm:px-8 py-6 space-y-6">

            {{-- Welcome banner --}}
            <section class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_260px]">
                    <div class="p-6 sm:p-8">
                        <span class="inline-block text-[11px] font-semibold tracking-wide bg-[var(--canvas)] border border-[var(--line)] px-2.5 py-1 rounded-full mb-4">
                            Clinical Workspace
                        </span>
                        {{-- DATA: auth()->user()->name --}}
                        <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight mb-2">Welcome back.</h1>
                        {{-- DATA: dynamic summary (alert count / pending validations / sync status) --}}
                        <p class="text-[var(--ink-soft)] text-sm max-w-md mb-6">
                            Your clinical workspace is set up and ready. Alerts, validation tasks, and caseload sync status will appear here once caregiver data starts coming in.
                        </p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="#" class="inline-flex items-center gap-1.5 border border-[var(--line)] text-sm font-semibold px-4 py-2.5 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                                Complete Daily Briefing
                            </a>
                            <a href="#" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/></svg>
                                Schedule Reviews
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:block bg-[var(--canvas)] skel"></div>
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-[340px_1fr] gap-6 items-start">

                {{-- ===================== LEFT COLUMN ===================== --}}
                <div class="space-y-6 min-w-0">

                    {{-- Priority Alerts --}}
                    <section class="bg-white border border-[var(--line)] rounded-2xl p-5">
                        <div class="flex items-center justify-between mb-1">
                            <h2 class="text-[15px] font-bold tracking-tight">Priority Alerts</h2>
                            {{-- DATA: count($priorityAlerts) --}}
                            <span class="text-[11px] font-semibold bg-[var(--canvas)] border border-[var(--line)] px-2 py-0.5 rounded-full text-[var(--ink-soft)]">0 New</span>
                        </div>
                        <p class="text-[12.5px] text-[var(--ink-soft)] mb-4">Requiring immediate attention</p>

                        {{-- DATA: @foreach($priorityAlerts as $alert) ... @endforeach --}}
                        <div class="border border-dashed border-[var(--line)] rounded-xl p-5 text-center">
                            <div class="w-9 h-9 mx-auto rounded-full bg-[var(--canvas)] flex items-center justify-center mb-2.5">
                                <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.3 3.9L2.7 17a2 2 0 001.7 3h15.2a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
                            </div>
                            <p class="text-sm font-semibold">No alerts right now</p>
                            <p class="text-[12.5px] text-[var(--ink-soft)] mt-1">Flagged caregiver observations needing validation will show up here.</p>
                        </div>

                        <a href="#" class="mt-4 flex items-center justify-center gap-1 text-[13px] font-semibold text-[var(--ink-soft)] hover:text-[var(--ink)]">
                            View All Notifications
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </a>
                    </section>

                    {{-- Quick Actions --}}
                    <section>
                        <h2 class="text-lg font-bold tracking-tight mb-3">Quick Actions</h2>
                        <div class="space-y-3">
                            <a href="#" class="flex items-start gap-3 bg-white border border-[var(--line)] rounded-xl p-4">
                                <div class="w-9 h-9 shrink-0 rounded-lg bg-[var(--canvas)] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M18 8v6M15 11h6"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Onboard New Patient</p>
                                    <p class="text-[12.5px] text-[var(--ink-soft)]">Create profile and invite caregiver to AutiSync.</p>
                                </div>
                            </a>
                            <a href="#" class="flex items-start gap-3 bg-white border border-[var(--line)] rounded-xl p-4">
                                <div class="w-9 h-9 shrink-0 rounded-lg bg-[var(--canvas)] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Batch Validation</p>
                                    <p class="text-[12.5px] text-[var(--ink-soft)]">Review and validate pending AI behavior predictions.</p>
                                </div>
                            </a>
                            <a href="#" class="flex items-start gap-3 bg-white border border-[var(--line)] rounded-xl p-4">
                                <div class="w-9 h-9 shrink-0 rounded-lg bg-[var(--canvas)] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Generate Progress Report</p>
                                    <p class="text-[12.5px] text-[var(--ink-soft)]">Export comprehensive clinical reports for insurance.</p>
                                </div>
                            </a>
                        </div>
                    </section>
                </div>

                {{-- ===================== RIGHT COLUMN ===================== --}}
                <div class="min-w-0">
                    <section class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
                            <div>
                                <h2 class="text-lg font-bold tracking-tight">Assigned Caseload</h2>
                                {{-- DATA: count($patients) --}}
                                <p class="text-[13px] text-[var(--ink-soft)]">Managing 0 active neuro-profiles</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="relative block">
                                    <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                                    <input type="text" placeholder="Find patient..." disabled
                                           class="bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-8 pr-3 py-1.5 text-[13px] placeholder:text-[var(--ink-soft)] focus:outline-none w-40">
                                </label>
                                <button class="inline-flex items-center gap-1.5 text-[13px] font-semibold border border-[var(--line)] bg-white px-3 py-1.5 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5h18l-7 8v6l-4 2v-8z"/></svg>
                                    Filter
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto -mx-1">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-[12px] text-[var(--ink-soft)] font-semibold border-b border-[var(--line)]">
                                        <th class="py-2.5 px-1 font-semibold">Patient Name</th>
                                        <th class="py-2.5 px-1 font-semibold">Clinical Status</th>
                                        <th class="py-2.5 px-1 font-semibold">Diagnosis</th>
                                        <th class="py-2.5 px-1 font-semibold">Last Sync</th>
                                        <th class="py-2.5 px-1 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- DATA: @forelse($patients as $patient) ... row per patient ... @empty --}}
                                    <tr>
                                        <td colspan="5" class="py-12 text-center">
                                            <div class="w-11 h-11 mx-auto rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center mb-3">
                                                <svg class="w-5 h-5 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M18 8v6M15 11h6"/></svg>
                                            </div>
                                            <p class="text-sm font-semibold mb-1">No patients assigned yet</p>
                                            <p class="text-[13px] text-[var(--ink-soft)]">Onboard a patient to populate your caseload.</p>
                                        </td>
                                    </tr>
                                    {{-- @endforelse --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between mt-5 pt-4 border-t border-[var(--line)]">
                            <p class="text-[12.5px] text-[var(--ink-soft)] italic">All patient data is encrypted and confidential</p>
                            <div class="flex items-center gap-4 text-[13px] font-medium text-[var(--ink-soft)]">
                                <button disabled class="disabled:opacity-50">Previous</button>
                                {{-- DATA: pagination state --}}
                                <span>Page 1 of 1</span>
                                <button disabled class="disabled:opacity-50">Next</button>
                            </div>
                        </div>
                    </section>

                    {{-- Stats strip --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                        <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                            <p class="eyebrow uppercase mb-2">Data Reliability</p>
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8"/></svg>
                                {{-- DATA: reliability metric from monitoring pipeline --}}
                                <div class="h-5 w-16 skel"></div>
                            </div>
                            <p class="text-[12.5px] text-[var(--ink-soft)]">Across current caseload logs</p>
                        </div>
                        <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                            <p class="eyebrow uppercase mb-2">Model Accuracy</p>
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18h6M10 22h4M12 2a6 6 0 00-4 10.4c.6.6.9 1 .9 1.8V15h6.2v-.8c0-.8.3-1.2.9-1.8A6 6 0 0012 2z"/></svg>
                                {{-- DATA: real evaluated model accuracy — never fabricate --}}
                                <div class="h-5 w-16 skel"></div>
                            </div>
                            <p class="text-[12.5px] text-[var(--ink-soft)]">Behavioral pattern prediction</p>
                        </div>
                        <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                            <p class="eyebrow uppercase mb-2">Data Protection</p>
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
                                {{-- DATA: real compliance/encryption status from security config --}}
                                <div class="h-5 w-20 skel"></div>
                            </div>
                            <p class="text-[12.5px] text-[var(--ink-soft)]">Session encryption status</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>