<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ShareSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_value',
        'share_cost',
        'effective_from',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'share_value' => 'decimal:2',
            'share_cost' => 'decimal:2',
            'effective_from' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function current(): ?self
    {
        return static::query()
            ->active()
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first()
            ?? static::query()
                ->orderByDesc('effective_from')
                ->orderByDesc('id')
                ->first();
    }
}
