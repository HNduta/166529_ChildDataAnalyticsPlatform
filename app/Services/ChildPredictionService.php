<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ChildPredictionService
{
    private const FEATURES = [
        'avg_success_first_2',
        'age_years',
        'ados2_social_affect',
        'ados2_rrb',
        'cognitive_verbal_percentile',
        'cognitive_nonverbal_percentile',
        'cognitive_gca_percentile',
        'adaptive_composite_percentile',
        'num_current_services',
        'domain',
        'gender',
        'support_level',
        'primary_domain',
        'ados2_comparison_score',
    ];

    public function predict(array $input): array
    {
        $validator = Validator::make($input, [
            'avg_success_first_2' => ['required', 'numeric', 'between:0,100'],
            'age_years' => ['required', 'numeric', 'between:0,120'],
            'ados2_social_affect' => ['required', 'numeric'],
            'ados2_rrb' => ['required', 'numeric'],
            'cognitive_verbal_percentile' => ['required', 'numeric', 'between:0,100'],
            'cognitive_nonverbal_percentile' => ['required', 'numeric', 'between:0,100'],
            'cognitive_gca_percentile' => ['required', 'numeric', 'between:0,100'],
            'adaptive_composite_percentile' => ['required', 'numeric', 'between:0,100'],
            'num_current_services' => ['required', 'integer', 'min:0'],
            'domain' => ['required', 'string', 'min:1'],
            'gender' => ['required', 'string', 'min:1'],
            'support_level' => ['required', 'string', 'min:1'],
            'primary_domain' => ['required', 'string', 'min:1'],
            'ados2_comparison_score' => ['required', 'string', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages(
                $validator->errors()->toArray()
            );
        }

        $url = rtrim(
            (string) config('services.child_prediction.url'),
            '/'
        );

        if ($url === '') {
            throw new RuntimeException(
                'The Python prediction service URL is not configured.'
            );
        }

        $payload = [];

        foreach (self::FEATURES as $feature) {
            $payload[$feature] = $input[$feature];
        }

        $response = Http::acceptJson()
            ->asJson()
            ->connectTimeout(3)
            ->timeout(10)
            ->post($url . '/predict', $payload);

        if (!$response->successful()) {
            throw new RuntimeException(
                'The prediction service returned HTTP '
                . $response->status() . '.'
            );
        }

        $result = $response->json();

        if (
            !is_array($result)
            || !isset($result['predicted_class'])
            || !is_numeric($result['predicted_class'])
            || !isset($result['class_scores'])
            || !is_array($result['class_scores'])
            || !isset($result['disclaimer'])
            || !is_string($result['disclaimer'])
        ) {
            throw new RuntimeException(
                'The prediction service returned an invalid response.'
            );
        }

        $predictedClass = (int) $result['predicted_class'];

        if (!array_key_exists((string) $predictedClass, $result['class_scores'])) {
            throw new RuntimeException(
                'The prediction response does not contain a score for its predicted class.'
            );
        }

        foreach ($result['class_scores'] as $label => $score) {
            if (!is_numeric($score) || $score < 0 || $score > 1) {
                throw new RuntimeException(
                    'The prediction service returned an invalid class score.'
                );
            }
        }

        if (
            isset($result['score_interpretation'])
            && !is_string($result['score_interpretation'])
        ) {
            throw new RuntimeException(
                'The prediction service returned an invalid score interpretation.'
            );
        }

        return $result;
    }
}