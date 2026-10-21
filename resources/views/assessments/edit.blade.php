@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Child Assessment Inputs</h1>

    <p>
        Child: {{ $record->child->first_name }}
    </p>

    <p>
        Observation date: {{ $record->observation_date }}
    </p>

    <p>
        Enter verified assessment results. Do not estimate specialist
        assessment scores or substitute behavioural ratings.
    </p>

    @if (session('status'))
        <div role="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert">
            <p>Please correct the following errors:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('clinician.assessments.update', $record) }}"
    >
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

        @foreach ($fields as $name => $label)
            <div class="mb-3">
                <label for="{{ $name }}">
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
                >
            </div>
        @endforeach

        <button type="submit">
            Save assessment
        </button>
    </form>
</div>
@endsection