<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'child_id',
        'milestone_description',
        'date_achieved',
        'achieved_flag',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'date_achieved' => 'date',
            'achieved_flag' => 'boolean',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(TherapyGoal::class, 'goal_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}