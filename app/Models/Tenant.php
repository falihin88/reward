<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'domain',
        'logo_url',
        'accent_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class)->withoutGlobalScopes();
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'tenant_user');
    }

    public function cards()
    {
        return $this->hasMany(Card::class)->withoutGlobalScopes();
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class)->withoutGlobalScopes();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class)->withoutGlobalScopes();
    }

    public function settings()
    {
        return $this->hasMany(Setting::class)->withoutGlobalScopes();
    }
}
