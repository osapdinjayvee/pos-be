<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class License extends Model
{
    use HasApiTokens;
    protected $fillable = [
        'license_key',
        'license_type',
        'business_name',
        'expiry_date',
        'is_active',
        'last_heartbeat_at',
        'metadata',
        'plan_id',
        'max_terminals',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function terminals(): HasMany
    {
        return $this->hasMany(Terminal::class);
    }

    public function activeTerminalCount(): int
    {
        return $this->terminals()->where('is_active', true)->count();
    }

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'is_active' => 'boolean',
            'last_heartbeat_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
