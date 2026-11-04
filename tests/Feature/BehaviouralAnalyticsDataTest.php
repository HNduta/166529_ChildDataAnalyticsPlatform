<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BehaviouralAnalyticsDataTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_caregiver_child_profile_page_renders_with_updated_tabs_and_sidebar(): void
    {
        $user = User::where('role', 'caregiver')->first() ?? User::factory()->create(['role' => 'caregiver']);
        $child = $user->children()->first() ?? $user->children()->create([
            'first_name' => 'Amani Angel',
            'age_in_months' => 48,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);

        $response = $this->actingAs($user)->get(route('children.show', $child));

        $response->assertStatus(200);

        // Verify tabs
        $response->assertSee('Overview');
        $response->assertSee('Behavioral Logs');
        $response->assertSee('ML Predictions and Analytics');
        $response->assertSee('Goals and Milestones');
        $response->assertSee('Recommendations');
        $response->assertDontSee('AI Insights');

        // Verify sidebar navigation does not contain removed items
        $response->assertDontSee('Goals &amp; Milestones');

        // Verify behavioral logs tab content is present
        $response->assertSee('tab-content-behavioral-logs', false);
    }

    public function test_behavioural_records_page_renders(): void
    {
        $user = User::where('role', 'caregiver')->first() ?? User::factory()->create(['role' => 'caregiver']);

        $response = $this->actingAs($user)->get(route('behavioural-records.index'));

        $response->assertStatus(200);
        $response->assertSee('Behavioural Records');
        $response->assertDontSee('Goals &amp; Milestones');
    }

    public function test_behavioral_logs_tab_shows_only_records_for_selected_child(): void
    {
        $user = User::factory()->create(['role' => 'caregiver']);
        $child1 = $user->children()->create([
            'first_name' => 'Amani Angel',
            'age_in_months' => 48,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);
        $child2 = $user->children()->create([
            'first_name' => 'Baraka Sam',
            'age_in_months' => 60,
            'sex' => 'Male',
            'diagnosis_confirmed' => false,
        ]);

        $record1 = $child1->behaviouralRecords()->create([
            'caregiver_id' => $user->id,
            'observation_date' => now()->toDateString(),
            'communication_score' => 4,
            'social_interaction_score' => 3,
            'engagement_level' => 4,
            'repetitive_behaviour_score' => 2,
            'session_duration_minutes' => 35,
            'success_rate' => 65.0,
            'notes' => 'Amani specific observation note',
        ]);

        $record2 = $child2->behaviouralRecords()->create([
            'caregiver_id' => $user->id,
            'observation_date' => now()->toDateString(),
            'communication_score' => 2,
            'social_interaction_score' => 1,
            'engagement_level' => 2,
            'repetitive_behaviour_score' => 4,
            'session_duration_minutes' => 20,
            'success_rate' => 45.0,
            'notes' => 'Baraka specific observation note',
        ]);

        $response = $this->actingAs($user)->get(route('children.show', ['child' => $child1, 'tab' => 'behavioral-logs']));

        $response->assertStatus(200);
        $response->assertSee('Amani Angel');
        $response->assertSee('Amani specific observation note');
        $response->assertDontSee('Baraka specific observation note');
    }

    public function test_ml_predictions_and_analytics_tab_renders_accurate_model_features_and_placeholders(): void
    {
        $user = User::factory()->create(['role' => 'caregiver']);
        $child = $user->children()->create([
            'first_name' => 'Amani Angel',
            'age_in_months' => 48,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);

        $response = $this->actingAs($user)->get(route('children.show', ['child' => $child, 'tab' => 'ml-predictions-analytics']));

        $response->assertStatus(200);

        // Header & KPI indicators
        $response->assertSee('Behavioral Intelligence Dashboard');
        $response->assertSee('Observation Density');
        $response->assertSee('Prediction Status');
        $response->assertSee('Average Session');

        // Removed badges must NOT be present
        $response->assertDontSee('Clinical Analytics v2.4');
        $response->assertDontSee('Dedicated Intelligence Dashboard');

        // Developmental Trajectories & Goal Progression
        $response->assertSee('Developmental Trajectories');
        $response->assertSee('Goal Progression');
        $response->assertSee('No Active Therapy Goals');

        // AI-Driven Behavioral Insights placeholder when 0 records
        $response->assertSee('AI-Driven Behavioral Insights');
        $response->assertSee('No Behavioral Records Yet');

        // Feature Contribution: accurate 14 features from child_intervention_pipeline.joblib
        $response->assertSee('Feature Contribution');
        $response->assertSee('child_intervention_pipeline');
        $response->assertSee('avg_success_first_2');
        $response->assertSee('age_years');
        $response->assertSee('gender');
        $response->assertSee('primary_domain');
        $response->assertSee('ados2_rrb');
        $response->assertSee('cognitive_verbal_percentile');
        $response->assertSee('adaptive_composite_percentile');

        // Dummy wireframe doctors must NOT be present
        $response->assertDontSee('Dr. Prudence Mawazo');
        $response->assertDontSee('Dr. Marcus Juma');
        $response->assertDontSee('Dr. Emily Watamu');

        // Faithful placeholder for Clinical Validations
        $response->assertSee('Recent Clinical Validations');
        $response->assertSee('No clinical validations recorded yet');
    }

    public function test_ml_predictions_and_analytics_tab_with_real_observations_and_validations(): void
    {
        $user = User::factory()->create(['role' => 'caregiver']);
        $child = $user->children()->create([
            'first_name' => 'Amani Angel',
            'age_in_months' => 48,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);

        // Create 2 records
        $child->behaviouralRecords()->create([
            'caregiver_id' => $user->id,
            'observation_date' => now()->subDay()->toDateString(),
            'communication_score' => 8,
            'social_interaction_score' => 7,
            'engagement_level' => 8,
            'repetitive_behaviour_score' => 2,
            'session_duration_minutes' => 45,
            'success_rate' => 80.0,
            'notes' => 'First session',
        ]);

        $record2 = $child->behaviouralRecords()->create([
            'caregiver_id' => $user->id,
            'observation_date' => now()->toDateString(),
            'communication_score' => 6,
            'social_interaction_score' => 5,
            'engagement_level' => 7,
            'repetitive_behaviour_score' => 3,
            'session_duration_minutes' => 50,
            'success_rate' => 50.0,
            'notes' => 'Second session',
        ]);

        // Create a clinician-validated prediction
        $clinician = User::factory()->create(['role' => 'clinician', 'name' => 'Dr. Sarah Connor']);
        $child->predictions()->create([
            'observation_id' => $record2->id,
            'predicted_outcome' => '1',
            'confidence_score' => 0.8850,
            'date_generated' => now(),
            'model_used' => 'child_intervention_pipeline',
            'validated_by_clinician' => true,
            'clinician_feedback' => 'Child shows strong positive trajectory',
        ]);

        $response = $this->actingAs($user)->get(route('children.show', ['child' => $child, 'tab' => 'ml-predictions-analytics']));

        $response->assertStatus(200);

        // Verification of real data
        $response->assertSee('2 Sessions Logged');
        $response->assertSee('Class 1');
        $response->assertSee('88.5%');
        $response->assertSee('65.0%'); // avg_success_first_2 = (80 + 50) / 2 = 65%

        // Dynamic AI insights
        $response->assertSee('Consistent Communication Response');

        // Real validation record
        $response->assertSee('Child shows strong positive trajectory');
        $response->assertDontSee('No clinical validations recorded yet');
    }

    public function test_dedicated_goals_recommendations_and_predictions_pages_render_with_consistent_ui(): void
    {
        $user = User::factory()->create(['role' => 'caregiver']);
        $child = $user->children()->create([
            'first_name' => 'Amani Angel',
            'age_in_months' => 48,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);

        $goalsResponse = $this->actingAs($user)->get(route('children.goals-milestones', $child));
        $goalsResponse->assertStatus(200);
        $goalsResponse->assertSee('Goals &amp; Milestones', false);
        $goalsResponse->assertSee('Active Goals');
        $goalsResponse->assertDontSee('Plus Jakarta Sans');

        $recsResponse = $this->actingAs($user)->get(route('children.recommendations', $child));
        $recsResponse->assertStatus(200);
        $recsResponse->assertSee('Support Recommendations');
        $recsResponse->assertSee('Active Actions');
        $recsResponse->assertDontSee('Plus Jakarta Sans');

        $predResponse = $this->actingAs($user)->get(route('children.predictions-analytics', $child));
        $predResponse->assertStatus(200);
        $predResponse->assertSee('Behavioral Intelligence Dashboard');
        $predResponse->assertSee('ML Prediction Service Output');
        $predResponse->assertSee('Feature Contribution');
        $predResponse->assertSee('child_intervention_pipeline');
        $predResponse->assertDontSee('Plus Jakarta Sans');
    }

    public function test_recommendations_link_search_and_ml_service_prediction_display_on_predictions_analytics_page(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*/predict' => \Illuminate\Support\Facades\Http::response([
                'predicted_class' => 1,
                'class_scores' => [
                    '0' => 0.22,
                    '1' => 0.78,
                ],
                'score_interpretation' => 'Uncalibrated model scores; they are not validated clinical probabilities.',
                'disclaimer' => 'For exploratory decision support only.',
            ], 200),
        ]);

        $user = User::factory()->create(['role' => 'caregiver']);
        $child = $user->children()->create([
            'first_name' => 'Amani Angel',
            'age_in_months' => 48,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);

        // 1. Verify caregiver recommendations sidebar redirect
        $recRedirect = $this->actingAs($user)->get(route('caregiver.recommendations'));
        $recRedirect->assertRedirect(route('children.recommendations', $child));

        // 2. Verify active search on child profile page
        $searchIndex = $this->actingAs($user)->get(route('children.index', ['search' => 'Amani']));
        $searchIndex->assertStatus(200);
        $searchIndex->assertSee('Amani Angel');

        // 3. Generate prediction from ML service and verify it displays on ML Predictions and Analytics page
        $postResponse = $this->actingAs($user)->post(route('children.predictions-analytics.generate', $child), [
            'avg_success_first_2' => 65,
            'age_years' => 4,
            'ados2_social_affect' => 8,
            'ados2_rrb' => 4,
            'cognitive_verbal_percentile' => 45,
            'cognitive_nonverbal_percentile' => 55,
            'cognitive_gca_percentile' => 48,
            'adaptive_composite_percentile' => 50,
            'num_current_services' => 2,
            'domain' => 'communication',
            'gender' => 'female',
            'support_level' => 'moderate',
            'primary_domain' => 'social',
            'ados2_comparison_score' => 'moderate',
        ]);

        $postResponse->assertRedirect(route('children.predictions-analytics', $child));

        $analyticsPage = $this->actingAs($user)->get(route('children.predictions-analytics', $child));
        $analyticsPage->assertStatus(200);
        $analyticsPage->assertSee('ML Prediction Service Output');
        $analyticsPage->assertSee('Class 1');
        $analyticsPage->assertSee('78.0%');
        $analyticsPage->assertSee('22.0%');
        $analyticsPage->assertSee('Uncalibrated Class Scores');
        $analyticsPage->assertSee('Prediction History');
    }
}

