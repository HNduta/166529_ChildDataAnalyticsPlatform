<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Dashboard · AutiSync</title>

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
                <p class="eyebrow px-3 mb-2 uppercase">Caregiver</p>
                <div class="space-y-1">
                    <a href="{{ url('/caregiver/dashboard') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        Dashboard
                    </a>
                    <a href="#" class="navlink">
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

        <main class="px-4 sm:px-8 py-6 grid grid-cols-1 xl:grid-cols-[1fr_340px] gap-6">

            {{-- ===================== LEFT COLUMN ===================== --}}
            <div class="space-y-6 min-w-0">

                {{-- Greeting banner --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_260px]">
                        <div class="p-6 sm:p-8">
                            <span class="inline-block text-[11px] font-semibold tracking-wide bg-[var(--canvas)] border border-[var(--line)] px-2.5 py-1 rounded-full mb-4">
                                AutiSync Dashboard
                            </span>
                            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight mb-2">Good morning.</h1>
                            {{-- DATA: dynamic summary (new observations / upcoming sessions) --}}
                            <p class="text-[var(--ink-soft)] text-sm max-w-sm mb-6">
                                Your dashboard is set up and ready. Once child profiles and observations are added, a summary will appear here.
                            </p>
                            <div class="flex flex-wrap items-center gap-3">
                                <a href="#" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                                    <span class="text-base leading-none">+</span> Log Observation
                                </a>
                                <a href="#" class="inline-flex items-center gap-1.5 border border-[var(--line)] text-sm font-semibold px-4 py-2.5 rounded-lg">
                                    View Analytics
                                </a>
                            </div>
                        </div>
                        <div class="hidden sm:block bg-[var(--canvas)] skel"></div>
                    </div>
                </section>

                {{-- Child Profiles --}}
                <section>
                    <div class="flex items-end justify-between mb-3">
                        <div>
                            <h2 class="text-lg font-bold tracking-tight">Child Profiles</h2>
                            <p class="text-[13px] text-[var(--ink-soft)]">Quick summary of current activities and weekly progress.</p>
                        </div>
                        <button class="hidden sm:inline-flex items-center gap-1.5 text-[13px] font-semibold border border-[var(--line)] bg-white px-3.5 py-2 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M9 18h6"/></svg>
                            Manage Profiles
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- DATA: @foreach($childProfiles as $child) ... @endforeach --}}

                        {{-- Empty state: no child profiles yet --}}
                        <div class="sm:col-span-2 border border-dashed border-[var(--line)] rounded-2xl bg-white p-8 text-center">
                            <div class="w-11 h-11 mx-auto rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                            </div>
                            <p class="text-sm font-semibold mb-1">No child profiles yet</p>
                            <p class="text-[13px] text-[var(--ink-soft)] mb-4">Add a profile to start tracking observations and progress.</p>
                            <a href="#" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2 rounded-lg">
                                <span class="text-base leading-none">+</span> Add Profile
                            </a>
                        </div>
                    </div>
                </section>

                {{-- Engagement Trends chart --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-bold tracking-tight">Engagement Trends</h2>
                            <p class="text-[13px] text-[var(--ink-soft)]">Social interaction frequency vs distress levels (weekly).</p>
                        </div>
                        <select disabled class="text-[13px] border border-[var(--line)] rounded-lg px-3 py-1.5 bg-white text-[var(--ink-soft)]">
                            <option>This Week</option>
                        </select>
                    </div>

                    {{-- DATA: chart component fed by weekly engagement dataset --}}
                    <div class="h-56 rounded-xl skel flex items-center justify-center">
                        <p class="text-[13px] text-[var(--ink-soft)] font-medium bg-white/70 px-3 py-1 rounded-md">
                            Chart will appear here once observation data is available
                        </p>
                    </div>
                </section>
            </div>

            {{-- ===================== RIGHT COLUMN ===================== --}}
            <div class="space-y-6 min-w-0">

                {{-- Priority Alerts --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <h2 class="text-[15px] font-bold tracking-tight mb-3">Priority Alerts</h2>

                    {{-- DATA: @foreach($priorityAlerts as $alert) ... @endforeach --}}
                    <div class="border border-dashed border-[var(--line)] rounded-xl p-5 text-center">
                        <div class="w-9 h-9 mx-auto rounded-full bg-[var(--canvas)] flex items-center justify-center mb-2.5">
                            <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.3 3.9L2.7 17a2 2 0 001.7 3h15.2a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold">No alerts right now</p>
                        <p class="text-[12.5px] text-[var(--ink-soft)] mt-1">Flagged patterns needing validation will show up here.</p>
                    </div>
                </section>

                {{-- AI Insights --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <h2 class="text-[15px] font-bold tracking-tight">AI Insights</h2>
                        <span class="text-[10px] font-bold tracking-wide bg-[var(--ink)] text-white px-2 py-0.5 rounded-full">PREMIUM</span>
                    </div>

                    {{-- DATA: @foreach($aiInsights as $insight) ... @endforeach — never populate with fabricated predictions; render only once a real ML pipeline supplies results. --}}
                    <div class="space-y-3">
                        <div class="border border-[var(--line)] rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="h-3 w-32 skel"></div>
                                <div class="h-4 w-14 skel rounded-full"></div>
                            </div>
                            <div class="space-y-1.5">
                                <div class="h-2.5 w-full skel"></div>
                                <div class="h-2.5 w-4/5 skel"></div>
                            </div>
                        </div>
                        <div class="border border-[var(--line)] rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="h-3 w-24 skel"></div>
                                <div class="h-4 w-14 skel rounded-full"></div>
                            </div>
                            <div class="space-y-1.5">
                                <div class="h-2.5 w-full skel"></div>
                                <div class="h-2.5 w-3/5 skel"></div>
                            </div>
                        </div>
                        <p class="text-[12.5px] text-[var(--ink-soft)] text-center pt-1">
                            Insights generate automatically once enough observations are logged.
                        </p>
                    </div>
                </section>

                {{-- Monthly Goal Status --}}
                <section class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <h2 class="text-[15px] font-bold tracking-tight mb-3">Monthly Goal Status</h2>
                    {{-- DATA: goal progress bar bound to $monthlyGoal --}}
                    <div class="h-2 w-full rounded-full skel mb-2"></div>
                    <div class="flex items-center justify-between text-[11px] text-[var(--ink-soft)] font-medium">
                        <span>TARGET: —</span>
                        <span>CURRENT: —</span>
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>
</body>
</html>