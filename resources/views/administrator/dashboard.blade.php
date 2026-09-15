{{--
    Administrator Dashboard
    ---------------------------------------------------------------
    Purely presentational, built on the same visual system as
    resources/views/caregiver/dashboard.blade.php and
    resources/views/clinician/dashboard.blade.php. No database
    queries, no fake data. Every metric / list / table area is
    rendered as an explicit "empty state" or skeleton placeholder.
    Swap the placeholder blocks (marked with {{-- DATA: ... --}})
    once the backend endpoints exist.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard · AutiSync</title>

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

        .tab{ display:flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; color:var(--ink-soft); }
        .tab.active{ background:var(--canvas); color:var(--ink); border:1px solid var(--line); }
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
                <p class="eyebrow px-3 mb-2 uppercase">System</p>
                <div class="space-y-1">
                    <a href="{{ url('/administrator/dashboard') }}" class="navlink active">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.9-.3 1.7 1.7 0 00-1 1.6V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.6 1.7 1.7 0 00-1.9.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.9 1.7 1.7 0 00-1.6-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.6-1 1.7 1.7 0 00-.3-1.9l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.9.3H9a1.7 1.7 0 001-1.6V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.6 1.7 1.7 0 001.9-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.9V9a1.7 1.7 0 001.6 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.6 1z"/></svg>
                        Admin Control
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
                        AD
                    </div>
                    <div class="hidden sm:block leading-tight">
                        {{-- DATA: auth()->user()->name --}}
                        <p class="text-[13px] font-semibold">Admin</p>
                        <p class="text-[11px] text-[var(--ink-soft)]">Admin</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-4 sm:px-8 py-6 space-y-6">

            {{-- Banner --}}
            <section class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_260px]">
                    <div class="p-6 sm:p-8">
                        <span class="inline-block text-[11px] font-semibold tracking-wide bg-[var(--canvas)] border border-[var(--line)] px-2.5 py-1 rounded-full mb-4">
                            Administrator Workspace
                        </span>
                        <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight mb-2">System Health &amp; Governance</h1>
                        <p class="text-[var(--ink-soft)] text-sm max-w-md mb-6">
                            Welcome to the NeuroSync Analytics admin control center. Monitor platform-wide activity, manage clinical access rights, and ensure HIPAA-compliant data exports.
                        </p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="#" class="inline-flex items-center gap-1.5 border border-[var(--line)] text-sm font-semibold px-4 py-2.5 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M18 8v6M15 11h6"/></svg>
                                Add New User
                            </a>
                            <a href="#" class="inline-flex items-center gap-1.5 border border-[var(--line)] text-sm font-semibold px-4 py-2.5 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 109-9"/><path d="M3 4v5h5"/></svg>
                                View Full Audit
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:block bg-[var(--canvas)] skel"></div>
                </div>
            </section>

            {{-- Stat cards --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {{-- DATA: real platform metrics — never fabricate. Each card below is a skeleton until the backend supplies live values. --}}
                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] flex items-center justify-center">
                            <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M17 11a3 3 0 100-6 3 3 0 000 6zM21 21c0-3-2-5-4.5-5.5"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-[var(--ink-soft)] border border-[var(--line)] px-2 py-0.5 rounded-full">Real-time</span>
                    </div>
                    <p class="eyebrow uppercase mb-1.5">Total Active Users</p>
                    <div class="h-7 w-20 skel mb-1.5"></div>
                    <p class="text-[12.5px] text-[var(--ink-soft)]">vs last month</p>
                </div>

                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] flex items-center justify-center">
                            <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h4l2-7 4 14 2-7h6"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-[var(--ink-soft)] border border-[var(--line)] px-2 py-0.5 rounded-full">Real-time</span>
                    </div>
                    <p class="eyebrow uppercase mb-1.5">System Uptime</p>
                    <div class="h-7 w-20 skel mb-1.5"></div>
                    <p class="text-[12.5px] text-[var(--ink-soft)]">Status</p>
                </div>

                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] flex items-center justify-center">
                            <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-[var(--ink-soft)] border border-[var(--line)] px-2 py-0.5 rounded-full">Real-time</span>
                    </div>
                    <p class="eyebrow uppercase mb-1.5">Unresolved Logs</p>
                    <div class="h-7 w-14 skel mb-1.5"></div>
                    <p class="text-[12.5px] text-[var(--ink-soft)]">Requires review</p>
                </div>

                <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-9 h-9 rounded-lg bg-[var(--canvas)] flex items-center justify-center">
                            <svg class="w-4 h-4 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-[var(--ink-soft)] border border-[var(--line)] px-2 py-0.5 rounded-full">Real-time</span>
                    </div>
                    <p class="eyebrow uppercase mb-1.5">Data Capacity</p>
                    <div class="h-7 w-16 skel mb-1.5"></div>
                    <p class="text-[12.5px] text-[var(--ink-soft)]">Secure storage</p>
                </div>
            </section>

            {{-- Tabs + search/filter row --}}
            <section class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-1 bg-white border border-[var(--line)] rounded-xl p-1">
                    <span class="tab active">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6"/></svg>
                        User Management
                    </span>
                    <span class="tab">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>
                        Audit Logs
                    </span>
                    <span class="tab">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                        Governance
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <label class="relative block">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                        <input type="text" placeholder="Search records..." disabled
                               class="bg-white border border-[var(--line)] rounded-lg pl-8 pr-3 py-2 text-[13px] placeholder:text-[var(--ink-soft)] focus:outline-none w-48">
                    </label>
                    <button class="w-9 h-9 shrink-0 rounded-lg border border-[var(--line)] bg-white flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5h18l-7 8v6l-4 2v-8z"/></svg>
                    </button>
                </div>
            </section>

            {{-- Active Practitioners table --}}
            <section class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-lg font-bold tracking-tight">Active Practitioners</h2>
                        <p class="text-[13px] text-[var(--ink-soft)]">Manage user access levels and verification status for clinicians and caregivers.</p>
                    </div>
                    <button class="inline-flex items-center gap-1.5 text-[13px] font-semibold border border-[var(--line)] bg-white px-3.5 py-2 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 3v13M7 11l5 5 5-5"/><path d="M4 21h16"/></svg>
                        Export CSV
                    </button>
                </div>

                <div class="overflow-x-auto -mx-1">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-[12px] text-[var(--ink-soft)] font-semibold border-b border-[var(--line)]">
                                <th class="py-2.5 px-1 font-semibold">User</th>
                                <th class="py-2.5 px-1 font-semibold">Role</th>
                                <th class="py-2.5 px-1 font-semibold">Status</th>
                                <th class="py-2.5 px-1 font-semibold">Last Activity</th>
                                <th class="py-2.5 px-1 font-semibold">Account Security</th>
                                <th class="py-2.5 px-1 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DATA: @forelse($practitioners as $user) ... row per user ... @empty --}}
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="w-11 h-11 mx-auto rounded-full bg-[var(--canvas)] border border-[var(--line)] flex items-center justify-center mb-3">
                                        <svg class="w-5 h-5 text-[var(--ink-soft)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.5-6 7-6s7 2 7 6M17 11a3 3 0 100-6 3 3 0 000 6zM21 21c0-3-2-5-4.5-5.5"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold mb-1">No registered users yet</p>
                                    <p class="text-[13px] text-[var(--ink-soft)]">Add a user to start managing access levels and verification status.</p>
                                </td>
                            </tr>
                            {{-- @endforelse --}}
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between mt-5 pt-4 border-t border-[var(--line)]">
                    {{-- DATA: "Showing {count} of {total} registered users" --}}
                    <p class="text-[12.5px] text-[var(--ink-soft)]">Showing 0 of 0 registered users</p>
                    <div class="flex items-center gap-2">
                        <button disabled class="text-[13px] font-semibold border border-[var(--line)] px-3.5 py-1.5 rounded-lg text-[var(--ink-soft)] disabled:opacity-50">Previous</button>
                        <button disabled class="text-[13px] font-semibold border border-[var(--line)] px-3.5 py-1.5 rounded-lg text-[var(--ink-soft)] disabled:opacity-50">Next</button>
                    </div>
                </div>
            </section>

            {{-- Support strip --}}
            <section class="bg-white border border-[var(--line)] rounded-2xl p-5 sm:p-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-[15px] font-bold tracking-tight mb-1">Need Technical Assistance?</h2>
                    <p class="text-[13px] text-[var(--ink-soft)]">Our platform support team is available 24/7 for administrative and security concerns.</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="#" class="inline-flex items-center gap-1.5 border border-[var(--line)] text-sm font-semibold px-4 py-2.5 rounded-lg">
                        Documentation
                    </a>
                    <a href="#" class="inline-flex items-center gap-1.5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                        Contact System Ops
                    </a>
                </div>
            </section>
        </main>
    </div>
</div>
</body>
</html>