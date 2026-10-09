<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Behavioural Records · AutiSync</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        :root {
            --ink:#12735A;
            --ink-soft:#6f6a62;
            --line:#e6dfd4;
            --surface:#ffffff;
            --canvas:#faf6f0;
            --accent:#e8845c;
            --accent-soft:#f4b79c;
            --accent-tint:#fdf1ea;
        }

        html,
        body {
            font-family:'Inter',ui-sans-serif,system-ui,sans-serif;
            background:var(--canvas);
            color:var(--ink);
        }

        .eyebrow {
            font-size:11px;
            letter-spacing:.06em;
            color:var(--ink-soft);
            font-weight:600;
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

        .filter-input {
            border:1px solid var(--line);
            border-radius:10px;
            padding:9px 12px;
            font-size:13px;
            background:#fff;
            color:var(--ink);
            outline:none;
        }

        .filter-input:focus {
            border-color:var(--ink);
            box-shadow:0 0 0 2px rgba(18,115,90,.12);
        }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible,
        select:focus-visible {
            outline:2px solid var(--ink);
            outline-offset:2px;
        }

    </style>

</head>


<body class="min-h-screen">

<div class="flex min-h-screen">


    {{-- SIDEBAR --}}

    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-[var(--line)] h-screen sticky top-0 overflow-y-auto">

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


        <nav class="px-3 py-5 space-y-6 flex-1">

            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Caregiver
                </p>

                <div class="space-y-1">

                    <a
                        href="{{ route('caregiver.dashboard') }}"
                        class="navlink"
                    >
                        Dashboard
                    </a>

                    <a
                        href="{{ route('children.index') }}"
                        class="navlink"
                    >
                        Child Profile
                    </a>

                </div>

            </div>


            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Behavior
                </p>

                <div class="space-y-1">

                    <a
                        href="{{ route('behavioural-records.create') }}"
                        class="navlink"
                    >
                        Observation Form
                    </a>

                    <a
                        href="{{ route('behavioural-records.index') }}"
                        class="navlink active"
                    >
                        Records
                    </a>

                </div>

            </div>


            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Progress
                </p>

                <div class="space-y-1">

                    <a href="#" class="navlink">
                        Goals &amp; Milestones
                    </a>

                </div>

            </div>


            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Insights
                </p>

                <div class="space-y-1">

                    <a href="#" class="navlink">
                        ML Predictions
                    </a>

                    <a href="#" class="navlink">
                        Analytics
                    </a>

                </div>

            </div>


            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Support
                </p>

                <div class="space-y-1">

                    <a href="#" class="navlink">
                        Recommendations
                    </a>

                </div>

            </div>

        </nav>

    </aside>


    {{-- MAIN --}}

    <div class="flex-1 min-w-0">


        {{-- TOPBAR --}}

        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white sticky top-0 z-10">

            <p class="hidden sm:block font-semibold text-[15px]">
                Behavioural Records
            </p>


            <div class="ml-auto flex items-center gap-3">

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


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="text-[13px] font-semibold text-[var(--accent)] hover:underline"
                    >
                        Logout
                    </button>

                </form>

            </div>

        </header>


        {{-- PAGE CONTENT --}}

        <main class="px-4 sm:px-8 py-8">

            <div class="max-w-7xl mx-auto">


                {{-- SUCCESS MESSAGE --}}

                @if (session('success'))

                    <div
                        class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
                        role="status"
                    >
                        {{ session('success') }}
                    </div>

                @endif


                {{-- PAGE HEADING --}}

                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6 pb-6 border-b border-[var(--line)]">

                    <div>

                        <p class="eyebrow uppercase mb-1">
                            Behavior
                        </p>

                        <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">
                            Behavioural Records
                        </h1>

                        <p class="text-[13px] text-[var(--ink-soft)] mt-1 max-w-3xl">
                            Review behavioural observations recorded for your children
                            and monitor their developmental information over time.
                        </p>

                    </div>


                    {{-- REQUEST 1: New Observation goes to Observation Form --}}

                    <a
                        href="{{ route('behavioural-records.create') }}"
                        class="inline-flex items-center justify-center gap-2 bg-[var(--ink)] text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:opacity-90 transition whitespace-nowrap"
                    >

                        <span class="text-lg leading-none">
                            +
                        </span>

                        New Observation

                    </a>

                </div>


                {{-- SUMMARY CARDS --}}

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">


                    {{-- Total Records --}}

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">

                        <p class="eyebrow uppercase">
                            Total Records
                        </p>

                        <p class="mt-2 text-3xl font-extrabold tracking-tight">
                            {{ $totalRecords }}
                        </p>

                    </div>


                    {{-- Average Success Rate --}}

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">

                        <p class="eyebrow uppercase">
                            Average Observation Score
                        </p>

                        <p class="mt-2 text-3xl font-extrabold tracking-tight">

                            {{ $averageSuccessRate !== null
                                ? number_format($averageSuccessRate, 1) . '%'
                                : '—' }}

                        </p>

                        <p class="mt-1 text-xs text-[var(--ink-soft)]">
                            Based on the four behavioural indicators
                        </p>

                    </div>


                    {{-- Average Duration --}}

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">

                        <p class="eyebrow uppercase">
                            Avg. Session Duration
                        </p>

                        <p class="mt-2 text-3xl font-extrabold tracking-tight">

                            {{ $averageDuration !== null
                                ? number_format($averageDuration, 0) . ' min'
                                : '—' }}

                        </p>

                    </div>


                    {{-- Children Tracked --}}

                    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">

                        <p class="eyebrow uppercase">
                            Children Tracked
                        </p>

                        <p class="mt-2 text-3xl font-extrabold tracking-tight">
                            {{ $childrenTracked }}
                        </p>

                    </div>

                </div>


                {{-- RECORDS SECTION --}}

                <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">


                    {{-- SECTION HEADER --}}

                    <div class="border-b border-[var(--line)] px-5 sm:px-6 py-5">

                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                            <div>

                                <h3 class="text-lg font-bold tracking-tight">
                                    Observation Records
                                </h3>

                                <p class="mt-1 text-[13px] text-[var(--ink-soft)]">
                                    Review previous observations and manage recorded data.
                                </p>

                            </div>


                            {{-- FILTERS --}}

                            <form
                                method="GET"
                                action="{{ route('behavioural-records.index') }}"
                                class="flex flex-col gap-2 sm:flex-row sm:items-center"
                            >

                                <select
                                    name="child_id"
                                    class="filter-input"
                                >

                                    <option value="">
                                        All Children
                                    </option>

                                    @foreach ($children as $child)

                                        <option
                                            value="{{ $child->id }}"
                                            {{ request('child_id') == $child->id ? 'selected' : '' }}
                                        >
                                            {{ $child->first_name }}
                                        </option>

                                    @endforeach

                                </select>


                                <input
                                    type="date"
                                    name="observation_date"
                                    value="{{ request('observation_date') }}"
                                    class="filter-input"
                                >


                                <button
                                    type="submit"
                                    class="rounded-lg bg-[var(--ink)] px-4 py-2 text-[13px] font-semibold text-white hover:opacity-90 transition"
                                >
                                    Filter
                                </button>


                                <a
                                    href="{{ route('behavioural-records.index') }}"
                                    class="rounded-lg border border-[var(--accent-soft)] bg-white px-4 py-2 text-center text-[13px] font-semibold text-[var(--accent)] hover:bg-[var(--accent-tint)] transition"
                                >
                                    Clear
                                </a>

                            </form>

                        </div>

                    </div>


                    {{-- TABLE --}}

                    @if ($records->count())

                        <div class="overflow-x-auto">

                            <table class="min-w-full text-sm">

                                <thead>

                                    <tr class="border-b border-[var(--line)] bg-[var(--canvas)]">

                                        <th class="px-5 sm:px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                            Record
                                        </th>

                                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                            Child
                                        </th>

                                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                            Observation Date
                                        </th>

                                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                            Score
                                        </th>

                                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                            Engagement
                                        </th>

                                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                            Duration
                                        </th>

                                        <th class="px-5 sm:px-6 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-[var(--ink-soft)]">
                                            Actions
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach ($records as $record)

                                        @php

                                            $averageScore = (
                                                $record->communication_score +
                                                $record->social_interaction_score +
                                                $record->engagement_level +
                                                $record->repetitive_behaviour_score
                                            ) / 4;

                                            $successRate = ($averageScore / 5) * 100;

                                        @endphp


                                        {{-- MAIN ROW --}}

                                        <tr
                                            id="record-row-{{ $record->id }}"
                                            class="border-b border-[var(--line)] hover:bg-[var(--canvas)] transition"
                                        >

                                            <td class="px-5 sm:px-6 py-4 whitespace-nowrap">

                                                <span class="text-[13px] font-semibold text-[var(--ink-soft)]">
                                                    #{{ $record->id }}
                                                </span>

                                            </td>


                                            <td class="px-5 py-4 whitespace-nowrap">

                                                <span class="text-sm font-semibold">
                                                    {{ $record->child->first_name }}
                                                </span>

                                            </td>


                                            {{-- Correct database field: date_recorded --}}

                                            <td class="px-5 py-4 whitespace-nowrap">

                                                <div class="text-sm">
                                                    {{ $record->observation_date?->format('d/m/Y') ?? 'Date not recorded' }}
                                                </div>

                                            </td>


                                            <td class="px-5 py-4 whitespace-nowrap">

                                                <span class="text-sm font-semibold">
                                                    {{ number_format($successRate, 1) }}%
                                                </span>

                                            </td>


                                            <td class="px-5 py-4 whitespace-nowrap">

                                                <span class="inline-flex rounded-full bg-[var(--ink)] px-3 py-1 text-[11px] font-bold text-white">
                                                    {{ $record->engagement_level }}/5
                                                </span>

                                            </td>


                                            <td class="px-5 py-4 whitespace-nowrap">

                                                @if ($record->session_duration)

                                                    {{ $record->session_duration }} min

                                                @else

                                                    <span class="text-[var(--ink-soft)]">
                                                        —
                                                    </span>

                                                @endif

                                            </td>


                                            {{-- ACTIONS --}}

                                            <td class="px-5 sm:px-6 py-4 whitespace-nowrap text-right">

                                                <div class="flex items-center justify-end gap-4">


                                                    {{-- REQUEST 2: View directly on records page --}}

                                                    <button
                                                        type="button"
                                                        onclick="toggleRecordDetails({{ $record->id }})"
                                                        class="text-[13px] font-semibold text-[var(--ink)] hover:underline"
                                                        id="view-button-{{ $record->id }}"
                                                    >
                                                        View
                                                    </button>


                                                    {{-- REQUEST 3: Edit uses same Observation Form --}}

                                                    <a
                                                        href="{{ route('behavioural-records.edit', $record) }}"
                                                        class="text-[13px] font-semibold text-[var(--accent)] hover:underline"
                                                    >
                                                        Edit
                                                    </a>


                                                    {{-- Delete --}}

                                                    <form
                                                        method="POST"
                                                        action="{{ route('behavioural-records.destroy', $record) }}"
                                                        onsubmit="return confirm('Are you sure you want to delete this observation?');"
                                                        class="inline"
                                                    >

                                                        @csrf

                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="text-[13px] font-semibold text-red-600 hover:text-red-800 hover:underline"
                                                        >
                                                            Delete
                                                        </button>

                                                    </form>

                                                </div>

                                            </td>

                                        </tr>


                                        {{-- DETAILS ROW --}}

                                        <tr
                                            id="record-details-{{ $record->id }}"
                                            class="hidden border-b border-[var(--line)] bg-[var(--canvas)]"
                                        >

                                            <td
                                                colspan="7"
                                                class="px-5 sm:px-6 py-6"
                                            >

                                                <div class="bg-white border border-[var(--line)] rounded-xl p-5">


                                                    {{-- Details heading --}}

                                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">

                                                        <div>

                                                            <p class="eyebrow uppercase">
                                                                Observation Details
                                                            </p>

                                                            <h4 class="text-base font-bold">
                                                                {{ $record->child->first_name }}
                                                                ·
                                                                {{ $record->observation_date?->format('d/m/Y') ?? 'Date not recorded' }}
                                                            </h4>

                                                        </div>

                                                        <span class="inline-flex w-fit rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] px-3 py-1 text-xs font-semibold text-[var(--accent)]">
                                                            Record #{{ $record->id }}
                                                        </span>

                                                    </div>


                                                    {{-- Score cards --}}

                                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">


                                                        <div class="rounded-lg border border-[var(--line)] p-4">

                                                            <p class="text-xs text-[var(--ink-soft)]">
                                                                Communication
                                                            </p>

                                                            <p class="mt-1 text-xl font-bold">
                                                                {{ $record->communication_score }}/5
                                                            </p>

                                                        </div>


                                                        <div class="rounded-lg border border-[var(--line)] p-4">

                                                            <p class="text-xs text-[var(--ink-soft)]">
                                                                Social Interaction
                                                            </p>

                                                            <p class="mt-1 text-xl font-bold">
                                                                {{ $record->social_interaction_score }}/5
                                                            </p>

                                                        </div>


                                                        <div class="rounded-lg border border-[var(--line)] p-4">

                                                            <p class="text-xs text-[var(--ink-soft)]">
                                                                Engagement
                                                            </p>

                                                            <p class="mt-1 text-xl font-bold">
                                                                {{ $record->engagement_level }}/5
                                                            </p>

                                                        </div>


                                                        <div class="rounded-lg border border-[var(--line)] p-4">

                                                            <p class="text-xs text-[var(--ink-soft)]">
                                                                Repetitive Behaviour
                                                            </p>

                                                            <p class="mt-1 text-xl font-bold">
                                                                {{ $record->repetitive_behaviour_score }}/5
                                                            </p>

                                                        </div>

                                                    </div>


                                                    {{-- Additional information --}}

                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">

                                                        <div>

                                                            <p class="text-xs text-[var(--ink-soft)]">
                                                                Observation Date
                                                            </p>

                                                            <p class="mt-1 text-sm font-semibold">
                                                                {{ $record->observation_date?->format('d/m/Y') ?? 'Date not recorded' }}
                                                            </p>

                                                        </div>


                                                        <div>

                                                            <p class="text-xs text-[var(--ink-soft)]">
                                                                Session Duration
                                                            </p>

                                                            <p class="mt-1 text-sm font-semibold">

                                                                @if ($record->session_duration)

                                                                    {{ $record->session_duration }} minutes

                                                                @else

                                                                    Not recorded

                                                                @endif

                                                            </p>

                                                        </div>


                                                        <div>

                                                            <p class="text-xs text-[var(--ink-soft)]">
                                                                Overall Observation Score
                                                            </p>

                                                            <p class="mt-1 text-sm font-semibold">
                                                                {{ number_format($successRate, 1) }}%
                                                            </p>

                                                        </div>

                                                    </div>


                                                    {{-- Notes --}}

                                                    @if ($record->notes)

                                                        <div class="mt-5 border-t border-[var(--line)] pt-5">

                                                            <p class="text-xs font-semibold text-[var(--ink-soft)] uppercase tracking-wide">
                                                                Observation Notes
                                                            </p>

                                                            <p class="mt-2 text-sm leading-6">
                                                                {{ $record->notes }}
                                                            </p>

                                                        </div>

                                                    @else

                                                        <div class="mt-5 border-t border-[var(--line)] pt-5">

                                                            <p class="text-sm text-[var(--ink-soft)]">
                                                                No additional notes were recorded for this observation.
                                                            </p>

                                                        </div>

                                                    @endif


                                                    {{-- Detail actions --}}

                                                    <div class="flex justify-end gap-3 mt-5">

                                                        <a
                                                            href="{{ route('behavioural-records.edit', $record) }}"
                                                            class="rounded-lg bg-[var(--ink)] px-4 py-2 text-xs font-semibold text-white hover:opacity-90"
                                                        >
                                                            Edit Observation
                                                        </a>

                                                    </div>

                                                </div>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>


                        {{-- PAGINATION --}}

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-t border-[var(--line)] px-5 sm:px-6 py-4">

                            <p class="text-[13px] text-[var(--ink-soft)]">

                                Showing

                                <span class="font-semibold text-[var(--ink)]">
                                    {{ $records->firstItem() }}
                                </span>

                                to

                                <span class="font-semibold text-[var(--ink)]">
                                    {{ $records->lastItem() }}
                                </span>

                                of

                                <span class="font-semibold text-[var(--ink)]">
                                    {{ $records->total() }}
                                </span>

                                records

                            </p>


                            <div>
                                {{ $records->links() }}
                            </div>

                        </div>

                    @else

                        {{-- EMPTY STATE --}}

                        <div class="px-6 py-16 text-center">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-[var(--accent)]">

                                <svg
                                    class="w-7 h-7"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                                    <path d="M9 12h6M9 16h6"/>
                                </svg>

                            </div>


                            <h3 class="mt-4 text-base font-bold">
                                No behavioural records found
                            </h3>


                            <p class="mt-1 text-[13px] text-[var(--ink-soft)]">
                                No observations match the selected filters.
                            </p>


                            <a
                                href="{{ route('behavioural-records.create') }}"
                                class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[var(--ink)] px-4 py-2.5 text-sm font-semibold text-white hover:opacity-90 transition"
                            >

                                <span class="text-lg leading-none">
                                    +
                                </span>

                                Record Observation

                            </a>

                        </div>

                    @endif

                </div>

            </div>

        </main>

    </div>

</div>


{{-- VIEW DETAILS JAVASCRIPT --}}

<script>

    function toggleRecordDetails(recordId) {

        const details = document.getElementById(
            'record-details-' + recordId
        );

        const button = document.getElementById(
            'view-button-' + recordId
        );

        if (!details || !button) {
            return;
        }

        const isHidden = details.classList.contains('hidden');

        // Close all other expanded records.
        document
            .querySelectorAll('[id^="record-details-"]')
            .forEach(function (element) {

                if (element.id !== 'record-details-' + recordId) {
                    element.classList.add('hidden');
                }

            });

        document
            .querySelectorAll('[id^="view-button-"]')
            .forEach(function (element) {

                if (element.id !== 'view-button-' + recordId) {
                    element.textContent = 'View';
                }

            });


        // Toggle selected record.

        if (isHidden) {

            details.classList.remove('hidden');

            button.textContent = 'Hide';

        } else {

            details.classList.add('hidden');

            button.textContent = 'View';

        }

    }

</script>


</body>
</html>