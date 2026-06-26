<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_value',
        'share_cost',
        'fine_amount',
        'fine_percent',
        'effective_from',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'share_value' => 'decimal:2',
            'share_cost' => 'decimal:2',
            'fine_amount' => 'decimal:2',
            'fine_percent' => 'decimal:2',
            'effective_from' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
