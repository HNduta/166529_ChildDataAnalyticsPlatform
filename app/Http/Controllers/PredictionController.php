<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use App\Models\Prediction;
use App\Services\ChildPredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class PredictionController extends Controller
{
    public function generate(
        Request $request,
        BehaviouralRecord $behaviouralRecord,
        ChildPredictionService $predictionService
    ): JsonResponse {
        abort_unless(
            $request->user()
                && $request->user()->role === 'clinician',
            403
        );

        $assigned = $behaviouralRecord->child()
            ->whereHas('clinicians', function ($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })
            ->exists();

        abort_unless($assigned, 403);

        $input = $behaviouralRecord->ml_assessment_inputs;

        if (!is_array($input)) {
            return response()->json([
                'message' => 'Complete and save all 14 assessment inputs first.',
            ], 422);
        }

        $requiredFields = [
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

        $missingFields = array_diff(
            $requiredFields,
            array_keys($input)
        );

        if (!empty($missingFields)) {
            return response()->json([
                'message' => 'Complete all required assessment inputs first.',
                'missing_fields' => array_values($missingFields),
            ], 422);
        }

        try {
            $result = $predictionService->predict($input);
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => 'The assessment inputs are invalid.',
                'errors' => $exception->errors(),
            ], 422);
        } catch (Throwable $exception) {
            Log::warning(
                'Prediction request failed.',
                [
                    'observation_id' => $behaviouralRecord->id,
                    'exception' => $exception::class,
                ]
            );

            return response()->json([
                'message' => 'The prediction service is unavailable or returned an invalid response.',
            ], 503);
        }

        if (
            !is_array($result)
            || !isset($result['predicted_class'])
            || !in_array($result['predicted_class'], [0, 1], true)
            || !isset($result['class_scores'])
            || !is_array($result['class_scores'])
            || !array_key_exists(
                (string) $result['predicted_class'],
                $result['class_scores']
            )
            || !isset($result['disclaimer'])
            || !is_string($result['disclaimer'])
        ) {
            return response()->json([
                'message' => 'The prediction service returned an invalid response.',
            ], 502);
        }

        $prediction = $behaviouralRecord->predictions()->create([
            'child_id' => $behaviouralRecord->child_id,
            'model_used' => 'child_intervention_pipeline',
            'predicted_outcome' => (string) $result['predicted_class'],
            'confidence_score' => null,
            'date_generated' => now(),
            'validated_by_clinician' => false,
        ]);

        return response()->json([
            'message' => 'Prediction generated successfully.',
            'prediction' => $prediction,
            'class_scores' => $result['class_scores'],
            'score_interpretation' => $result['score_interpretation'] ?? null,
            'disclaimer' => $result['disclaimer'],
        ], 201);
    }

    public function caregiverIndex(Request $request): JsonResponse
    {
        abort_unless(
            $request->user()
                && $request->user()->role === 'caregiver',
            403
        );

        $predictions = Prediction::query()
            ->whereHas('child', function ($query) use ($request) {
                $query->where(
                    'caregiver_id',
                    $request->user()->id
                );
            })
            ->with(['child', 'observation'])
            ->latest('date_generated')
            ->paginate(10);

        return response()->json($predictions);
    }

    public function clinicianIndex(Request $request): JsonResponse
    {
        abort_unless(
            $request->user()
                && $request->user()->role === 'clinician',
            403
        );

        $predictions = Prediction::query()
            ->whereHas('child.clinicians', function ($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })
            ->with(['child', 'observation'])
            ->latest('date_generated')
            ->paginate(10);

        return response()->json($predictions);
    }
}