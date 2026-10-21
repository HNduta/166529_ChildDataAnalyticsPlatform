<?php

namespace Tests\Unit;

use App\Services\ChildPredictionService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChildPredictionServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.child_prediction.url' => 'http://127.0.0.1:8001',
        ]);
    }

    public function test_it_sends_valid_inputs_to_the_prediction_service(): void
    {
        Http::fake([
            '127.0.0.1:8001/predict' => Http::response([
                'predicted_class' => 1,
                'class_scores' => [
                    '0' => 0.3,
                    '1' => 0.7,
                ],
                'score_interpretation' =>
                    'Uncalibrated model scores.',
                'disclaimer' =>
                    'Exploratory decision support only.',
            ], 200),
        ]);

        $input = [
            'avg_success_first_2' => 70,
            'age_years' => 5,
            'ados2_social_affect' => 8,
            'ados2_rrb' => 4,
            'cognitive_verbal_percentile' => 45,
            'cognitive_nonverbal_percentile' => 50,
            'cognitive_gca_percentile' => 48,
            'adaptive_composite_percentile' => 42,
            'num_current_services' => 2,
            'domain' => 'communication',
            'gender' => 'female',
            'support_level' => 'moderate',
            'primary_domain' => 'communication',
            'ados2_comparison_score' => 'moderate',
        ];

        $result = app(ChildPredictionService::class)->predict($input);

        $this->assertSame(1, $result['predicted_class']);
        $this->assertSame('0.7', (string) $result['class_scores']['1']);

        Http::assertSent(function ($request) {
            return $request->url() ===
                'http://127.0.0.1:8001/predict'
                && $request->method() === 'POST'
                && count($request->data()) === 14;
        });
    }

    public function test_it_rejects_missing_model_inputs(): void
    {
        $this->expectException(
            \Illuminate\Validation\ValidationException::class
        );

        app(ChildPredictionService::class)->predict([]);
    }

    public function test_it_handles_an_unavailable_prediction_service(): void
    {
        Http::fake([
            '127.0.0.1:8001/predict' => Http::response([
                'detail' => 'Service unavailable',
            ], 503),
        ]);

        $this->expectException(\RuntimeException::class);

        app(ChildPredictionService::class)->predict([
            'avg_success_first_2' => 70,
            'age_years' => 5,
            'ados2_social_affect' => 8,
            'ados2_rrb' => 4,
            'cognitive_verbal_percentile' => 45,
            'cognitive_nonverbal_percentile' => 50,
            'cognitive_gca_percentile' => 48,
            'adaptive_composite_percentile' => 42,
            'num_current_services' => 2,
            'domain' => 'communication',
            'gender' => 'female',
            'support_level' => 'moderate',
            'primary_domain' => 'communication',
            'ados2_comparison_score' => 'moderate',
        ]);
    }
}