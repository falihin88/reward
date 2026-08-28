<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'teacher_id',
        'points',
        'total_points_earned',
        'current_streak',
        'last_activity_date',
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
            'points' => 'integer',
            'total_points_earned' => 'integer',
            'current_streak' => 'integer',
            'last_activity_date' => 'date',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'teacher_id');
    }

    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'user_cards')
                    ->withPivot('unlocked_at')
                    ->withTimestamps();
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'user_id')->latest();
    }

    public function teacherTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'teacher_id')->latest();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Tenants this user is granted access to manage (in addition to their
     * primary tenant_id). Admin users are handled separately in availableTenants().
     */
    public function managedTenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user');
    }

    /**
     * Tenants the user can currently switch between.
     * Admins see all active tenants; teachers see their primary tenant plus any
     * explicitly assigned managed tenants; students get none.
     */
    public function availableTenants(): Collection
    {
        if ($this->isAdmin()) {
            return Tenant::where('is_active', true)->get();
        }

        if (! $this->isTeacher()) {
            return collect();
        }

        $ids = $this->managedTenants()
            ->pluck('tenants.id')
            ->push($this->tenant_id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Tenant::where('is_active', true)->whereIn('id', $ids)->get();
    }

    /**
     * Whether the user may operate within the given tenant context.
     */
    public function canAccessTenant(Tenant|int $tenant): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        if ($this->tenant_id !== null && (int) $this->tenant_id === (int) $tenantId) {
            return true;
        }

        return $this->managedTenants()->whereKey($tenantId)->exists();
    }
}
