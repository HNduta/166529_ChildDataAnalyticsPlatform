@php
    $isEditing = isset($record);

    $selectedChild = old(
        'child_id',
        $record->child_id ?? request('child_id')
    );

    $dateRecorded = old(
        'date_recorded',
        isset($record) && $record->date_recorded
            ? $record->date_recorded->format('Y-m-d')
            : now()->format('Y-m-d')
    );
@endphp

<div class="space-y-8">

    {{-- Child and date --}}
    <div>
        <p class="eyebrow uppercase mb-1">
            Observation Details
        </p>

        <h2 class="text-lg font-bold tracking-tight">
            Child and Session Information
        </h2>

        <p class="mt-1 text-[13px] text-[var(--ink-soft)]">
            Select the child and provide the date for this behavioural observation.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">

            {{-- Child --}}
            <div>
                <label
                    for="child_id"
                    class="block text-sm font-semibold mb-2"
                >
                    Child <span class="text-red-500">*</span>
                </label>

                <select
                    id="child_id"
                    name="child_id"
                    required
                    class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2.5 text-sm focus:border-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-green-100"
                >
                    <option value="">
                        Select child
                    </option>

                    @foreach ($children as $child)
                        <option
                            value="{{ $child->id }}"
                            {{ (string) $selectedChild === (string) $child->id ? 'selected' : '' }}
                        >
                            {{ $child->first_name }}
                        </option>
                    @endforeach
                </select>

                @error('child_id')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Date --}}
            <div>
                <label
                    for="date_recorded"
                    class="block text-sm font-semibold mb-2"
                >
                    Observation Date <span class="text-red-500">*</span>
                </label>

                <input
                    id="date_recorded"
                    type="date"
                    name="date_recorded"
                    value="{{ $dateRecorded }}"
                    max="{{ now()->format('Y-m-d') }}"
                    required
                    class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2.5 text-sm focus:border-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-green-100"
                >

                @error('date_recorded')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>


    {{-- Behavioural scores --}}
    <div class="border-t border-[var(--line)] pt-8">

        <p class="eyebrow uppercase mb-1">
            Behavioural Indicators
        </p>

        <h2 class="text-lg font-bold tracking-tight">
            Behavioural Observation Scores
        </h2>

        <p class="mt-1 text-[13px] text-[var(--ink-soft)]">
            Rate each area from 1 to 5 based on the child's observed behaviour
            during the session.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">

            {{-- Communication --}}
            <div>
                <label
                    for="communication_score"
                    class="block text-sm font-semibold mb-2"
                >
                    Communication Score <span class="text-red-500">*</span>
                </label>

                <select
                    id="communication_score"
                    name="communication_score"
                    required
                    class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2.5 text-sm focus:border-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-green-100"
                >
                    <option value="">Select score</option>

                    @for ($score = 1; $score <= 5; $score++)
                        <option
                            value="{{ $score }}"
                            {{ old('communication_score', $record->communication_score ?? '') == $score ? 'selected' : '' }}
                        >
                            {{ $score }}
                        </option>
                    @endfor
                </select>

                @error('communication_score')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Social Interaction --}}
            <div>
                <label
                    for="social_interaction_score"
                    class="block text-sm font-semibold mb-2"
                >
                    Social Interaction Score <span class="text-red-500">*</span>
                </label>

                <select
                    id="social_interaction_score"
                    name="social_interaction_score"
                    required
                    class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2.5 text-sm focus:border-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-green-100"
                >
                    <option value="">Select score</option>

                    @for ($score = 1; $score <= 5; $score++)
                        <option
                            value="{{ $score }}"
                            {{ old('social_interaction_score', $record->social_interaction_score ?? '') == $score ? 'selected' : '' }}
                        >
                            {{ $score }}
                        </option>
                    @endfor
                </select>

                @error('social_interaction_score')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Engagement --}}
            <div>
                <label
                    for="engagement_level"
                    class="block text-sm font-semibold mb-2"
                >
                    Engagement Level <span class="text-red-500">*</span>
                </label>

                <select
                    id="engagement_level"
                    name="engagement_level"
                    required
                    class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2.5 text-sm focus:border-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-green-100"
                >
                    <option value="">Select level</option>

                    @for ($score = 1; $score <= 5; $score++)
                        <option
                            value="{{ $score }}"
                            {{ old('engagement_level', $record->engagement_level ?? '') == $score ? 'selected' : '' }}
                        >
                            {{ $score }}
                        </option>
                    @endfor
                </select>

                @error('engagement_level')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Repetitive behaviour --}}
            <div>
                <label
                    for="repetitive_behaviour_score"
                    class="block text-sm font-semibold mb-2"
                >
                    Repetitive Behaviour Score <span class="text-red-500">*</span>
                </label>

                <select
                    id="repetitive_behaviour_score"
                    name="repetitive_behaviour_score"
                    required
                    class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2.5 text-sm focus:border-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-green-100"
                >
                    <option value="">Select score</option>

                    @for ($score = 1; $score <= 5; $score++)
                        <option
                            value="{{ $score }}"
                            {{ old('repetitive_behaviour_score', $record->repetitive_behaviour_score ?? '') == $score ? 'selected' : '' }}
                        >
                            {{ $score }}
                        </option>
                    @endfor
                </select>

                @error('repetitive_behaviour_score')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

        <div class="mt-4 rounded-lg bg-[var(--canvas)] border border-[var(--line)] px-4 py-3">
            <p class="text-xs text-[var(--ink-soft)]">
                <strong>Scoring guide:</strong>
                1 = Very low,
                2 = Low,
                3 = Moderate,
                4 = Good,
                5 = Very good.
            </p>
        </div>

    </div>


    {{-- Session information --}}
    <div class="border-t border-[var(--line)] pt-8">

        <p class="eyebrow uppercase mb-1">
            Session Information
        </p>

        <h2 class="text-lg font-bold tracking-tight">
            Session Details
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">

            {{-- Duration --}}
            <div>
                <label
                    for="session_duration"
                    class="block text-sm font-semibold mb-2"
                >
                    Session Duration
                </label>

                <div class="relative">
                    <input
                        id="session_duration"
                        type="number"
                        name="session_duration"
                        value="{{ old('session_duration', $record->session_duration ?? '') }}"
                        min="1"
                        max="480"
                        placeholder="e.g. 30"
                        class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-2.5 pr-16 text-sm focus:border-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-green-100"
                    >

                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[var(--ink-soft)]">
                        minutes
                    </span>
                </div>

                @error('session_duration')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

    </div>


    {{-- Notes --}}
    <div class="border-t border-[var(--line)] pt-8">

        <label
            for="notes"
            class="block text-sm font-semibold mb-2"
        >
            Observation Notes
        </label>

        <textarea
            id="notes"
            name="notes"
            rows="5"
            maxlength="2000"
            placeholder="Record any relevant observations about the child's behaviour during the session..."
            class="w-full rounded-lg border border-[var(--line)] bg-white px-3 py-3 text-sm focus:border-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-green-100"
        >{{ old('notes', $record->notes ?? '') }}</textarea>

        @error('notes')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror

        <p class="mt-1 text-xs text-[var(--ink-soft)]">
            Maximum 2,000 characters.
        </p>

    </div>

</div>