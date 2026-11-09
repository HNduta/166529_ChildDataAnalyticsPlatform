<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
    ];

    public function profilePictureUrl(): ?string
    {
        if (!empty($this->profile_picture)) {
            $cleanPath = ltrim(str_replace('\\', '/', $this->profile_picture), '/');
            return asset('storage/' . $cleanPath);
        }

        $roleKey = strtolower((string) ($this->role ?: 'user'));
        $candidates = [
            "images/avatars/default-{$roleKey}.png",
            "images/avatars/default-{$roleKey}.jpg",
            "images/avatars/default-{$roleKey}.jpeg",
            "images/avatars/default-{$roleKey}.webp",
            'images/avatars/default-user.png',
            'images/avatars/default-user.jpg',
            'images/avatars/default-user.jpeg',
            'images/avatars/default-user.webp',
        ];

        foreach ($candidates as $relPath) {
            if (is_file(public_path($relPath))) {
                return asset($relPath);
            }
        }

        return null;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
        }

        return strtoupper(substr((string) $this->name, 0, 2) ?: 'US');
    }

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

    public function assignedChildren(): BelongsToMany
    {
        return $this->belongsToMany(
            Child::class,
            'child_clinician',
            'clinician_id',
            'child_id'
        )->withTimestamps();
    }
}