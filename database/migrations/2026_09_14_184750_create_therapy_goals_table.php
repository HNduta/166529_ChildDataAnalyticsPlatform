<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapy_goals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')
                ->constrained('children')
                ->cascadeOnDelete();

            $table->foreignId('clinician_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('goal_domain');
            $table->text('goal_description');
            $table->string('baseline_level');
            $table->string('target_level');
            $table->date('target_date')->nullable();
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapy_goals');
    }
};