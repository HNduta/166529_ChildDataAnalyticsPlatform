<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TherapyGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'clinician_id',
        'goal_domain',
        'goal_description',
        'baseline_level',
        'target_level',
        'target_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function clinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'clinician_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'goal_id');
    }
}