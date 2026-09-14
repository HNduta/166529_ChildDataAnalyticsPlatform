<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caregiver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('first_name');
            $table->unsignedInteger('age_in_months');
            $table->string('sex');
            $table->boolean('born_with_jaundice')->nullable();
            $table->boolean('family_member_with_asd')->nullable();
            $table->boolean('diagnosis_confirmed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};