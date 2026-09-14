<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'caregiver_id',
        'first_name',
        'age_in_months',
        'sex',
        'born_with_jaundice',
        'family_member_with_asd',
        'diagnosis_confirmed',
    ];

    protected function casts(): array
    {
        return [
            'age_in_months' => 'integer',
            'born_with_jaundice' => 'boolean',
            'family_member_with_asd' => 'boolean',
            'diagnosis_confirmed' => 'boolean',
        ];
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }

    public function behaviouralRecords(): HasMany
    {
        return $this->hasMany(BehaviouralRecord::class, 'child_id');
    }

    public function therapyGoals(): HasMany
    {
        return $this->hasMany(TherapyGoal::class, 'child_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'child_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'child_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'child_id');
    }
}