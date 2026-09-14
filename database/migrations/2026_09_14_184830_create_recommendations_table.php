<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prediction_id')
                ->constrained('predictions')
                ->cascadeOnDelete();

            $table->foreignId('child_id')
                ->constrained('children')
                ->cascadeOnDelete();

            $table->text('recommendation_text');
            $table->string('category');
            $table->date('date_generated');
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};