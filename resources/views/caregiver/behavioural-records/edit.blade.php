<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Observation · AutiSync</title>

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
    </style>
</head>

<body class="min-h-screen">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-[var(--line)] h-screen sticky top-0">

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
                        class="navlink active"
                    >
                        Observation Form
                    </a>

                    <a
                        href="{{ route('behavioural-records.index') }}"
                        class="navlink"
                    >
                        Records
                    </a>

                </div>

            </div>


            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Progress
                </p>

                <a href="#" class="navlink">
                    Goals &amp; Milestones
                </a>

            </div>


            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Insights
                </p>

                <a href="#" class="navlink">
                    ML Predictions
                </a>

                <a href="#" class="navlink">
                    Analytics
                </a>

            </div>


            <div>

                <p class="eyebrow px-3 mb-2 uppercase">
                    Support
                </p>

                <a href="#" class="navlink">
                    Recommendations
                </a>

            </div>

        </nav>

    </aside>


    {{-- MAIN --}}
    <div class="flex-1 min-w-0">

        <header class="min-h-16 flex items-center gap-4 px-4 sm:px-8 py-2 border-b border-[var(--line)] bg-white">

            <p class="font-semibold text-[15px]">
                Edit Observation
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


        <main class="px-4 sm:px-8 py-8">

            <div class="max-w-4xl mx-auto">

                <div class="mb-6 pb-6 border-b border-[var(--line)]">

                    <p class="eyebrow uppercase mb-1">
                        Behavior
                    </p>

                    <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight">
                        Observation Form
                    </h1>

                    <p class="text-[13px] text-[var(--ink-soft)] mt-1">
                        Update the behavioural observation for
                        <span class="font-semibold">
                            {{ $record->child->first_name }}
                        </span>.
                    </p>

                </div>


                @if ($errors->any())

                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3">

                        <p class="text-sm font-semibold text-red-700">
                            Please correct the following errors:
                        </p>

                        <ul class="mt-2 list-disc list-inside text-xs text-red-600">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                @endif


                <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">

                    <form
                        method="POST"
                        action="{{ route('behavioural-records.update', $record) }}"
                    >

                        @csrf
                        @method('PUT')

                        <div class="p-6 sm:p-8">

                            @include(
                                'caregiver.behavioural-records.form',
                                ['record' => $record]
                            )

                        </div>


                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 border-t border-[var(--line)] bg-[var(--canvas)] px-6 sm:px-8 py-5">

                            <a
                                href="{{ route('behavioural-records.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-[var(--line)] bg-white px-5 py-2.5 text-sm font-semibold text-[var(--ink)] hover:bg-gray-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-[var(--ink)] px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90"
                            >
                                Update Observation
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