<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('behavioural_records', function (Blueprint $table) {
            $table->renameColumn('date_recorded', 'observation_date');

            $table->decimal('success_rate', 5, 2)
                ->after('observation_date');

            $table->decimal('prompts_required', 5, 2)
                ->nullable()
                ->after('engagement_level');

            $table->unsignedTinyInteger('eye_contact_rating')
                ->nullable()
                ->after('prompts_required');

            $table->unsignedInteger('session_duration_minutes')
                ->nullable()
                ->after('eye_contact_rating');
        });

        Schema::table('behavioural_records', function (Blueprint $table) {
            $table->dropColumn('session_duration');
        });
    }

    public function down(): void
    {
        Schema::table('behavioural_records', function (Blueprint $table) {
            $table->dropColumn([
                'success_rate',
                'prompts_required',
                'eye_contact_rating',
                'session_duration_minutes',
            ]);
        });

        Schema::table('behavioural_records', function (Blueprint $table) {
            $table->renameColumn('observation_date', 'date_recorded');

            $table->unsignedInteger('session_duration')
                ->nullable()
                ->after('repetitive_behaviour_score');
        });
    }
};