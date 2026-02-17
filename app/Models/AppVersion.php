<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $fillable = [
        'version',
        'platform',
        'release_notes',
        'download_path',
        'is_active',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'released_at' => 'datetime',
        ];
    }
}
