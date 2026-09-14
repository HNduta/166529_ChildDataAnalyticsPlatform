<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')
                ->constrained('children')
                ->cascadeOnDelete();

            $table->foreignId('observation_id')
                ->constrained('behavioural_records')
                ->cascadeOnDelete();

            $table->string('model_used');
            $table->string('predicted_outcome');
            $table->decimal('confidence_score', 5, 4)->nullable();

            $table->timestamp('date_generated');

            $table->boolean('validated_by_clinician')->default(false);
            $table->text('clinician_feedback')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};