<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Children · AutiSync</title>

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

        .scrollbar-thin::-webkit-scrollbar {
            width:6px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background:#e6dfd4;
            border-radius:999px;
        }

        .navlink {
            display:flex;
            align-items:center;
            gap:10px;
            padding:8px 12px;
            border-radius:8px;
            font-size:14px;
            color:#5b574f;
            font-weight:500;
        }

        .navlink:hover {
            background:#f3ede3;
            color:var(--ink);
        }

        .navlink.active {
            background:var(--ink);
            color:#fff;
        }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline:2px solid var(--ink);
            outline-offset:2px;
        }
    </style>
</head>

<body class="min-h-screen">

<div class="flex min-h-screen">

    {{-- ============================= SIDEBAR ============================= --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-[var(--line)] h-screen sticky top-0 overflow-y-auto scrollbar-thin">

        {{-- Logo --}}
        <div class="flex items-center gap-2 px-5 h-16 border-b border-[var(--line)]">

            <div class="w-8 h-8 rounded-lg bg-[var(--ink)] flex items-center justify-center text-white text-sm font-bold">
                A
            </div>

            <div class="leading-tight">
                <p class="text-[15px] font-bold tracking-tight">
                    AutiSync
                </p>

                <p class="text-[10px] tracking-[.12em] text-[var(--ink-soft)] font-semibold">
                    ANALYTICS
                </p>
            </div>

        </div>


        {{-- Navigation --}}
        <nav class="px-3 py-5 space-y-6 flex-1">

            {{-- Caregiver --}}
            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Caregiver
                </p>

                <div class="space-y-1">

                    <a
                        href="{{ route('caregiver.dashboard') }}"
                        class="navlink"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>

                        Dashboard
                    </a>


                    <a
                        href="{{ route('children.index') }}"
                        class="navlink active"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>

                        Child Profile
                    </a>

                </div>

            </div>


            {{-- Behavior --}}
            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Behavior
                </p>

                <div class="space-y-1">

                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 12h6M9 16h6"/></svg>

                        Observation Form
                    </a>


                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19V6"/><path d="M10 19V10"/><path d="M16 19V4"/><path d="M22 19V13"/></svg>

                        Records
                    </a>

                </div>

            </div>


            {{-- Progress --}}
            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Progress
                </p>

                <div class="space-y-1">

                    <a href="#" class="navlink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r=".5"/></svg>

                        Goals &amp; Milestones
                    </a>

                </div>

            </div>


            {{-- Insights --}}
            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Insights
                </p>

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


            {{-- Support --}}
            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Support
                </p>

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


        {{-- ============================= TOPBAR ============================= --}}
        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white sticky top-0 z-10">

            <p class="hidden sm:block font-semibold text-[15px]">
                Child Profile
            </p>


            {{-- Search --}}
            <div class="flex-1 max-w-md ml-0 sm:ml-6">

                <label class="relative block">

                    <svg
                        class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--ink-soft)]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="M21 21l-4.3-4.3"/>
                    </svg>

                    <input
                        type="text"
                        placeholder="Search records..."
                        disabled
                        class="w-full bg-[var(--canvas)] border border-[var(--line)] rounded-lg pl-9 pr-3 py-2 text-sm placeholder:text-[var(--ink-soft)] focus:outline-none"
                    >

                </label>

            </div>


            {{-- Right side --}}
            <div class="ml-auto flex items-center gap-3">


                {{-- Notifications --}}
                <button
                    type="button"
                    class="relative w-9 h-9 rounded-lg border border-[var(--line)] flex items-center justify-center"
                    aria-label="Notifications"
                >

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 8a6 6 0 1112 0c0 5 2 6 2 6H4s2-1 2-6z"/><path d="M10 21a2 2 0 004 0"/></svg>

                </button>


                {{-- Registered Caregiver --}}
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


                {{-- Logout --}}
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


        {{-- ============================= PAGE CONTENT ============================= --}}
        <main class="px-4 sm:px-8 py-8">


            {{-- Page Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">

                <div>

                    <p class="eyebrow uppercase mb-1">
                        Caregiver
                    </p>

                    <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">
                        My Children
                    </h1>

                    <p class="text-[13px] text-[var(--ink-soft)] mt-1">
                        Select a child to view their individual profile and developmental information.
                    </p>

                </div>


                {{-- Add Child --}}
                <a
                    href="{{ route('children.create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition"
                >
                    <span class="text-lg leading-none">+</span>
                    Add Child
                </a>

            </div>


            {{-- ============================= CHILDREN ============================= --}}
            @if ($children->count())


                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">


                    @foreach ($children as $child)

                        <div class="bg-white border border-[var(--line)] rounded-2xl p-5 hover:border-[var(--accent-soft)] transition">

                            {{-- Child Header --}}
                            <div class="flex items-start justify-between gap-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-12 h-12 rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center">

                                        <svg
                                            class="w-6 h-6 text-[var(--accent)]"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle cx="12" cy="8" r="4"/>
                                            <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                                        </svg>

                                    </div>


                                    <div>

                                        <h2 class="text-base font-bold">
                                            {{ $child->first_name }}
                                        </h2>

                                        <p class="text-[12px] text-[var(--ink-soft)]">
                                            {{ $child->age_in_months }} months old
                                        </p>

                                    </div>

                                </div>


                                @if ($child->diagnosis_confirmed)

                                    <span class="text-[9px] font-bold tracking-wide bg-[var(--ink)] text-white px-2 py-1 rounded-full">
                                        ASD
                                    </span>

                                @endif

                            </div>


                            {{-- Child Information --}}
                            <div class="mt-5 space-y-3">

                                <div class="flex items-center justify-between text-[12px]">

                                    <span class="text-[var(--ink-soft)]">
                                        Sex
                                    </span>

                                    <span class="font-semibold">
                                        {{ $child->sex }}
                                    </span>

                                </div>


                                <div class="flex items-center justify-between text-[12px]">

                                    <span class="text-[var(--ink-soft)]">
                                        Diagnosis
                                    </span>

                                    <span class="font-semibold">
                                        {{ $child->diagnosis_confirmed ? 'Confirmed' : 'Not confirmed' }}
                                    </span>

                                </div>


                                <div class="flex items-center justify-between text-[12px]">

                                    <span class="text-[var(--ink-soft)]">
                                        Observations
                                    </span>

                                    <span class="font-semibold">
                                        {{ $child->behaviouralRecords()->count() }}
                                    </span>

                                </div>

                            </div>


                            {{-- Actions --}}
                            <div class="mt-5 pt-4 border-t border-[var(--line)] flex items-center justify-between">

                                <a
                                    href="{{ route('children.show', $child) }}"
                                    class="text-[13px] font-semibold hover:underline"
                                >
                                    View Profile
                                </a>


                                <a
                                    href="{{ route('children.edit', $child) }}"
                                    class="text-[13px] font-semibold text-[var(--accent)] hover:underline transition"
                                >
                                    Edit
                                </a>

                            </div>

                        </div>

                    @endforeach


                    {{-- Add Another Child --}}
                    <a
                        href="{{ route('children.create') }}"
                        class="border border-dashed border-[var(--accent-soft)] rounded-2xl bg-white p-6 flex items-center justify-center min-h-[220px] hover:border-[var(--accent)] hover:bg-[var(--accent-tint)] transition"
                    >

                        <div class="text-center">

                            <div class="w-11 h-11 mx-auto rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)] flex items-center justify-center mb-3">

                                <span class="text-xl leading-none">
                                    +
                                </span>

                            </div>

                            <p class="text-sm font-semibold">
                                Add another child
                            </p>

                            <p class="text-[12px] text-[var(--ink-soft)] mt-1">
                                Create a new child profile
                            </p>

                        </div>

                    </a>

                </div>


            @else


                {{-- Empty State --}}
                <div class="bg-white border border-dashed border-[var(--accent-soft)] rounded-2xl p-10 text-center">

                    <div class="w-14 h-14 mx-auto rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] flex items-center justify-center mb-4">

                        <svg
                            class="w-7 h-7 text-[var(--accent)]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24"
                        >
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                        </svg>

                    </div>


                    <h2 class="text-base font-bold">
                        No child profiles yet
                    </h2>

                    <p class="text-[13px] text-[var(--ink-soft)] mt-1 max-w-md mx-auto">
                        Add a child profile to begin recording behavioural observations and monitoring developmental progress.
                    </p>


                    <a
                        href="{{ route('children.create') }}"
                        class="inline-flex items-center gap-2 mt-5 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition"
                    >
                        <span class="text-lg leading-none">+</span>
                        Add Child
                    </a>

                </div>

            @endif

        </main>

    </div>

</div>

</body>
</html>