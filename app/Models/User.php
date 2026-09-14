<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'caregiver_id');
    }

    public function behaviouralRecords(): HasMany
    {
        return $this->hasMany(BehaviouralRecord::class, 'caregiver_id');
    }

    public function therapyGoals(): HasMany
    {
        return $this->hasMany(TherapyGoal::class, 'clinician_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'recorded_by');
    }
}