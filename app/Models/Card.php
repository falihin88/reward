<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Card extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'title',
        'era',
        'rarity',
        'unlock_cost',
        'bio',
        'quote',
        'accent_color',
        'image_url',
        'is_active',
        'order',
    ];

    protected $casts = [
        'unlock_cost' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_cards')
                    ->withPivot('unlocked_at')
                    ->withTimestamps();
    }
}
