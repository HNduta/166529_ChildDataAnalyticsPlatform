<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('behavioural_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')
                ->constrained('children')
                ->cascadeOnDelete();

            $table->foreignId('caregiver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('date_recorded');

            $table->unsignedTinyInteger('communication_score');
            $table->unsignedTinyInteger('social_interaction_score');
            $table->unsignedTinyInteger('engagement_level');
            $table->unsignedTinyInteger('repetitive_behaviour_score');

            $table->unsignedInteger('session_duration')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('behavioural_records');
    }
};