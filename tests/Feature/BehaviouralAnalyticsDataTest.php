<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        // Verification of real data & caregiver-friendly labels (Class 1 -> May Need Additional Support)
        $response->assertSee('2 Sessions Logged');
        $response->assertSee('Developmental Progress Indicator');
        $response->assertSee('May Need Additional Support');
        $response->assertDontSee('Run ML Service Prediction');
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
        $recsResponse->assertSee('Activities to Try');
        $recsResponse->assertDontSee('Plus Jakarta Sans');

        $predResponse = $this->actingAs($user)->get(route('children.predictions-analytics', $child));
        $predResponse->assertStatus(200);
        $predResponse->assertSee('Behavioral Intelligence Dashboard');
        $predResponse->assertSee('Developmental Progress Indicator');
        $predResponse->assertSee('Developmental Progress Indicator Pending Clinician Assessment');
        $predResponse->assertDontSee('Run ML Service Prediction');
        $predResponse->assertSee('Feature Contribution');
        $predResponse->assertSee('child_intervention_pipeline');
        $predResponse->assertDontSee('Plus Jakarta Sans');
    }

    public function test_automatic_prediction_generation_and_duplicate_prevention_on_refresh(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*/predict' => \Illuminate\Support\Facades\Http::response([
                'predicted_class' => 0,
                'class_scores' => [
                    '0' => 0.78,
                    '1' => 0.22,
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

        // Record observation with complete 14-feature assessment inputs
        $record = $child->behaviouralRecords()->create([
            'caregiver_id' => $user->id,
            'observation_date' => now()->toDateString(),
            'communication_score' => 7,
            'social_interaction_score' => 7,
            'engagement_level' => 8,
            'repetitive_behaviour_score' => 2,
            'session_duration_minutes' => 40,
            'success_rate' => 75.0,
            'notes' => 'Assessed session',
            'ml_assessment_inputs' => [
                'avg_success_first_2' => 75.0,
                'age_years' => 4.0,
                'ados2_social_affect' => 7.0,
                'ados2_rrb' => 3.0,
                'cognitive_verbal_percentile' => 45.0,
                'cognitive_nonverbal_percentile' => 55.0,
                'cognitive_gca_percentile' => 50.0,
                'adaptive_composite_percentile' => 48.0,
                'num_current_services' => 2,
                'domain' => 'Communication',
                'gender' => 'Female',
                'support_level' => 'Level 1',
                'primary_domain' => 'Communication',
                'ados2_comparison_score' => 'low',
            ],
        ]);

        // First page open: automatically generates prediction without button click
        $firstResponse = $this->actingAs($user)->get(route('children.predictions-analytics', $child));
        $firstResponse->assertStatus(200);
        $firstResponse->assertSee('Developmental Progress Indicator');
        $firstResponse->assertSee('On-Track');
        $firstResponse->assertSee('Model Assessment Scores');
        $firstResponse->assertSee('78.0%');
        $firstResponse->assertSee('22.0%');
        $firstResponse->assertSee('Prediction History');
        $firstResponse->assertDontSee('Run ML Service Prediction');

        $this->assertSame(1, $child->predictions()->count());
        $this->assertGreaterThan(0, $child->recommendations()->count());
        \Illuminate\Support\Facades\Http::assertSentCount(1);

        // Second page open (refresh without assessment/observation changes): does NOT call FastAPI again or duplicate prediction
        $secondResponse = $this->actingAs($user)->get(route('children.predictions-analytics', $child));
        $secondResponse->assertStatus(200);
        $secondResponse->assertSee('On-Track');

        $this->assertSame(1, $child->predictions()->count());
        \Illuminate\Support\Facades\Http::assertSentCount(1);
    }

    public function test_unavailable_ml_service_displays_friendly_message_and_no_fabricated_prediction(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*/predict' => \Illuminate\Support\Facades\Http::response([
                'detail' => 'Service unavailable',
            ], 503),
        ]);

        $user = User::factory()->create(['role' => 'caregiver']);
        $child = $user->children()->create([
            'first_name' => 'Amani Angel',
            'age_in_months' => 48,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);

        $child->behaviouralRecords()->create([
            'caregiver_id' => $user->id,
            'observation_date' => now()->toDateString(),
            'communication_score' => 5,
            'social_interaction_score' => 5,
            'engagement_level' => 6,
            'repetitive_behaviour_score' => 3,
            'session_duration_minutes' => 30,
            'success_rate' => 55.0,
            'notes' => 'Session while ML service down',
            'ml_assessment_inputs' => [
                'avg_success_first_2' => 55.0,
                'age_years' => 4.0,
                'ados2_social_affect' => 9.0,
                'ados2_rrb' => 4.0,
                'cognitive_verbal_percentile' => 35.0,
                'cognitive_nonverbal_percentile' => 40.0,
                'cognitive_gca_percentile' => 38.0,
                'adaptive_composite_percentile' => 36.0,
                'num_current_services' => 2,
                'domain' => 'Communication',
                'gender' => 'Female',
                'support_level' => 'Level 2',
                'primary_domain' => 'Social Interaction',
                'ados2_comparison_score' => 'moderate',
            ],
        ]);

        $response = $this->actingAs($user)->get(route('children.predictions-analytics', $child));
        $response->assertStatus(200);
        $response->assertSee('Developmental Progress Analysis Temporarily Unavailable');
        $this->assertSame(0, $child->predictions()->count());
    }

    public function test_role_based_access_control_for_caregiver_clinician_and_unauthorized_users(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*/predict' => \Illuminate\Support\Facades\Http::response([
                'predicted_class' => 1,
                'class_scores' => ['0' => 0.19, '1' => 0.81],
                'score_interpretation' => 'Uncalibrated model scores.',
                'disclaimer' => 'For exploratory decision support only.',
            ], 200),
        ]);

        $caregiver1 = User::factory()->create(['role' => 'caregiver']);
        $caregiver2 = User::factory()->create(['role' => 'caregiver']);
        $assignedClinician = User::factory()->create(['role' => 'clinician']);
        $unassignedClinician = User::factory()->create(['role' => 'clinician']);

        $child1 = $caregiver1->children()->create([
            'first_name' => 'Amani Angel',
            'age_in_months' => 48,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);
        $child1->clinicians()->attach($assignedClinician->id);

        $record1 = $child1->behaviouralRecords()->create([
            'caregiver_id' => $caregiver1->id,
            'observation_date' => now()->toDateString(),
            'communication_score' => 4,
            'social_interaction_score' => 4,
            'engagement_level' => 5,
            'repetitive_behaviour_score' => 4,
            'session_duration_minutes' => 30,
            'success_rate' => 45.0,
            'notes' => 'Observation for clinician review',
        ]);

        // 1. Caregiver 2 cannot access Caregiver 1's child profile, predictions, or recommendations
        $this->actingAs($caregiver2)->get(route('children.show', $child1))->assertStatus(404);
        $this->actingAs($caregiver2)->get(route('children.predictions-analytics', $child1))->assertStatus(404);
        $this->actingAs($caregiver2)->get(route('children.recommendations', $child1))->assertStatus(404);

        // 2. Caregiver cannot access clinician assessment or prediction trigger routes
        $this->actingAs($caregiver1)->get(route('clinician.assessments.edit', $record1))->assertStatus(403);
        $this->actingAs($caregiver1)->post(route('behavioural-records.predict', $record1))->assertStatus(403);
        $this->actingAs($caregiver1)->get(route('clinician.predictions.index'))->assertStatus(403);
        $this->actingAs($caregiver1)->get(route('administrator.dashboard'))->assertStatus(403);

        // 3. Unassigned clinician cannot access child1's assessment or prediction trigger
        $this->actingAs($unassignedClinician)->get(route('clinician.assessments.edit', $record1))->assertStatus(403);
        $this->actingAs($unassignedClinician)->post(route('behavioural-records.predict', $record1))->assertStatus(403);

        // 4. Assigned clinician CAN view assessment form, save 14 inputs, and generate prediction
        $this->actingAs($assignedClinician)->get(route('clinician.assessments.edit', $record1))->assertStatus(200);

        $assessmentPayload = [
            'avg_success_first_2' => 45.0,
            'age_years' => 4.0,
            'ados2_social_affect' => 11.0,
            'ados2_rrb' => 5.0,
            'cognitive_verbal_percentile' => 25.0,
            'cognitive_nonverbal_percentile' => 35.0,
            'cognitive_gca_percentile' => 30.0,
            'adaptive_composite_percentile' => 28.0,
            'num_current_services' => 2,
            'domain' => 'Social Interaction',
            'gender' => 'Female',
            'support_level' => 'Level 2',
            'primary_domain' => 'Social Interaction',
            'ados2_comparison_score' => 'moderate',
        ];

        $this->actingAs($assignedClinician)
            ->put(route('clinician.assessments.update', $record1), $assessmentPayload)
            ->assertRedirect(route('clinician.assessments.edit', $record1));

        $predictResponse = $this->actingAs($assignedClinician)
            ->postJson(route('behavioural-records.predict', $record1));
        $predictResponse->assertStatus(201);
        $predictResponse->assertJsonPath('prediction.predicted_outcome', '1');

        // Verify stored prediction & generated recommendations for Class 1 (May Need Additional Support)
        $this->assertSame(1, $child1->predictions()->count());
        $this->assertSame(2, $child1->recommendations()->count());
    }

    public function test_user_and_child_profile_pictures_upload_replace_default_and_validation(): void
    {
        Storage::fake('public');

        // Minimal valid 1x1 PNG binary (does not require GD extension in CLI PHP)
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        // 1. Caregiver registration with optional profile picture + automatic caregiver role assignment
        $regAvatar = UploadedFile::fake()->createWithContent('caregiver.png', $pngBytes);
        $regResponse = $this->post(route('register'), [
            'name' => 'Grace Caregiver',
            'email' => 'grace@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'profile_picture' => $regAvatar,
        ]);
        $regResponse->assertRedirect(route('caregiver.dashboard'));

        $caregiver = User::where('email', 'grace@example.com')->firstOrFail();
        $this->assertSame('caregiver', $caregiver->role);
        $this->assertNotNull($caregiver->profile_picture);
        Storage::disk('public')->assertExists($caregiver->profile_picture);

        // 2. Replace caregiver profile picture via ProfileController@update and verify old file is cleaned up
        $oldCaregiverPic = $caregiver->profile_picture;
        $newCaregiverPic = UploadedFile::fake()->createWithContent('grace-updated.png', $pngBytes);
        $this->actingAs($caregiver)
            ->patch(route('profile.update'), [
                'name' => 'Grace Updated',
                'email' => 'grace@example.com',
                'profile_picture' => $newCaregiverPic,
            ])
            ->assertRedirect(route('profile.edit'));

        $caregiver->refresh();
        $this->assertNotSame($oldCaregiverPic, $caregiver->profile_picture);
        Storage::disk('public')->assertMissing($oldCaregiverPic);
        Storage::disk('public')->assertExists($caregiver->profile_picture);

        // 3. Clinician and Administrator profile picture upload & update
        foreach (['clinician', 'administrator'] as $role) {
            $roleUser = User::factory()->create([
                'name' => ucfirst($role) . ' User',
                'email' => $role . '@example.com',
                'role' => $role,
            ]);
            // Default avatar when no picture is uploaded
            $this->assertNull($roleUser->profile_picture);

            $rolePic = UploadedFile::fake()->createWithContent($role . '.png', $pngBytes);
            $this->actingAs($roleUser)
                ->patch(route('profile.update'), [
                    'name' => $roleUser->name,
                    'email' => $roleUser->email,
                    'profile_picture' => $rolePic,
                ])
                ->assertRedirect(route('profile.edit'));

            $roleUser->refresh();
            $this->assertNotNull($roleUser->profile_picture);
            Storage::disk('public')->assertExists($roleUser->profile_picture);
        }

        // 4. Child profile picture upload on creation and replacement on update
        $childPic1 = UploadedFile::fake()->createWithContent('child-initial.png', $pngBytes);
        $this->actingAs($caregiver)
            ->post(route('children.store'), [
                'first_name' => 'Baraka',
                'age_in_months' => 42,
                'sex' => 'Male',
                'diagnosis_confirmed' => '1',
                'profile_picture' => $childPic1,
            ])
            ->assertRedirect(route('children.index'));

        $child = $caregiver->children()->where('first_name', 'Baraka')->firstOrFail();
        $this->assertNotNull($child->profile_picture);
        Storage::disk('public')->assertExists($child->profile_picture);

        $oldChildPic = $child->profile_picture;
        $childPic2 = UploadedFile::fake()->createWithContent('child-replaced.png', $pngBytes);
        $this->actingAs($caregiver)
            ->put(route('children.update', $child), [
                'first_name' => 'Baraka',
                'age_in_months' => 43,
                'sex' => 'Male',
                'diagnosis_confirmed' => '1',
                'profile_picture' => $childPic2,
            ])
            ->assertRedirect(route('children.show', $child));

        $child->refresh();
        $this->assertNotSame($oldChildPic, $child->profile_picture);
        Storage::disk('public')->assertMissing($oldChildPic);
        Storage::disk('public')->assertExists($child->profile_picture);

        // 5. Reject invalid image format (.pdf) and oversized file (> 2048 KB)
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $this->actingAs($caregiver)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), [
                'name' => $caregiver->name,
                'email' => $caregiver->email,
                'profile_picture' => $invalidFile,
            ])
            ->assertSessionHasErrors('profile_picture');

        $oversizedImage = UploadedFile::fake()->create('huge.png', 3000, 'image/png');
        $this->actingAs($caregiver)
            ->from(route('children.edit', $child))
            ->put(route('children.update', $child), [
                'first_name' => 'Baraka',
                'age_in_months' => 43,
                'sex' => 'Male',
                'profile_picture' => $oversizedImage,
            ])
            ->assertSessionHasErrors('profile_picture');

        // 6. Reject unauthorised child profile picture update by another caregiver
        $otherCaregiver = User::factory()->create(['role' => 'caregiver']);
        $unauthorizedPic = UploadedFile::fake()->createWithContent('hack.png', $pngBytes);
        $this->actingAs($otherCaregiver)
            ->put(route('children.update', $child), [
                'first_name' => 'Hacked',
                'age_in_months' => 43,
                'sex' => 'Male',
                'profile_picture' => $unauthorizedPic,
            ])
            ->assertStatus(404);
    }

    public function test_login_and_registration_pages_render_image_placeholders_and_clinician_and_recommendations_workflows(): void
    {
        // 1. Login and Registration pages render user-supplied image when present, or clearly identified placeholders otherwise
        $loginResponse = $this->get(route('login'))->assertStatus(200);
        if (
            is_file(public_path('images/auth/login.png'))
            || is_file(public_path('images/auth/login.jpg'))
            || is_file(public_path('images/auth/login.jpeg'))
            || is_file(public_path('images/auth/login.webp'))
            || is_file(public_path('images/auth/login-illustration.png'))
            || is_file(public_path('images/auth/login-illustration.jpg'))
            || is_file(public_path('images/auth/login-illustration.jpeg'))
            || is_file(public_path('images/auth/login-illustration.webp'))
        ) {
            $loginResponse->assertSee('images/auth/', false);
        } else {
            $loginResponse->assertSee('Login Image Placeholder', false);
        }

        $registerResponse = $this->get(route('register'))->assertStatus(200)->assertSee('Profile Picture', false);
        if (
            is_file(public_path('images/auth/register.png'))
            || is_file(public_path('images/auth/register.jpg'))
            || is_file(public_path('images/auth/register.jpeg'))
            || is_file(public_path('images/auth/register.webp'))
            || is_file(public_path('images/auth/register-illustration.png'))
            || is_file(public_path('images/auth/register-illustration.jpg'))
            || is_file(public_path('images/auth/register-illustration.jpeg'))
            || is_file(public_path('images/auth/register-illustration.webp'))
        ) {
            $registerResponse->assertSee('images/auth/', false);
        } else {
            $registerResponse->assertSee('Registration Image Placeholder', false);
        }

        // 2. Clinician Dashboard renders real caseload, pending predictions, and supports clinician validation
        $caregiver = User::factory()->create(['name' => 'Neema Caregiver', 'role' => 'caregiver']);
        $clinician = User::factory()->create(['name' => 'Dr. Kamau', 'role' => 'clinician']);

        // Verify redesigned Profile Settings page renders with AutiSync theme
        $this->actingAs($caregiver)
            ->get(route('profile.edit'))
            ->assertStatus(200)
            ->assertSee('Account &amp; Profile Settings', false)
            ->assertSee('Choose New Photo', false)
            ->assertSee('Save Profile Changes', false);

        $child = $caregiver->children()->create([
            'first_name' => 'Tunu',
            'age_in_months' => 50,
            'sex' => 'Female',
            'diagnosis_confirmed' => true,
        ]);
        $child->clinicians()->attach($clinician->id);

        $obs = $child->behaviouralRecords()->create([
            'caregiver_id' => $caregiver->id,
            'observation_date' => '2026-10-20',
            'communication_score' => 6,
            'social_interaction_score' => 5,
            'engagement_level' => 7,
            'repetitive_behaviour_score' => 4,
            'session_duration_minutes' => 30,
            'success_rate' => 65.0,
            'notes' => 'Steady home communication practice',
        ]);

        $prediction = $obs->predictions()->create([
            'child_id' => $child->id,
            'model_used' => 'child_intervention_pipeline',
            'predicted_outcome' => '0',
            'confidence_score' => 0.85,
            'date_generated' => now(),
            'validated_by_clinician' => false,
        ]);
        $prediction->syncSupportRecommendations(['domain' => 'Communication']);

        $clinicianDash = $this->actingAs($clinician)->get(route('clinician.dashboard'));
        $clinicianDash->assertStatus(200);
        $clinicianDash->assertSee('Welcome, Dr. Kamau', false);
        $clinicianDash->assertSee('Tunu', false);
        $clinicianDash->assertSee('Predictions Awaiting Review (1)', false);

        // Validate prediction from Clinician Dashboard
        $this->actingAs($clinician)
            ->patch(route('clinician.predictions.validate', $prediction), [
                'validated_by_clinician' => '1',
                'clinician_feedback' => 'Confirmed steady progress in communication routines.',
            ])
            ->assertRedirect(route('clinician.dashboard'));

        $prediction->refresh();
        $this->assertTrue($prediction->validated_by_clinician);
        $this->assertSame('Confirmed steady progress in communication routines.', $prediction->clinician_feedback);

        // 3. Caregiver Recommendations page renders structured, non-technical caregiver guidance & supports status toggle
        $recPage = $this->actingAs($caregiver)->get(route('children.recommendations', $child));
        $recPage->assertStatus(200);
        $recPage->assertSee('Area: Communication', false);
        $recPage->assertSee('Why this matters', false);
        $recPage->assertSee('What you can do at home', false);
        $recPage->assertSee('How to practise it', false);
        $recPage->assertSee('What to look out for', false);
        $recPage->assertSee('Helpful reminder:', false);
        $recPage->assertSee('Supportive Home Guidance — Not a Medical Diagnosis', false);
        $recPage->assertDontSee('#REC-', false);

        $firstRec = $child->recommendations()->firstOrFail();
        $this->assertSame('active', $firstRec->status);

        $this->actingAs($caregiver)
            ->patch(route('children.recommendations.update-status', [$child, $firstRec]), [
                'status' => 'implemented',
            ])
            ->assertRedirect(route('children.recommendations', $child));

        $firstRec->refresh();
        $this->assertSame('implemented', $firstRec->status);
    }
}

