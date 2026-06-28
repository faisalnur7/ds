<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberShareHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'changed_by',
        'previous_share_number',
        'share_number',
        'share_value_per_share',
        'share_cost_per_share',
        'monthly_amount',
        'changed_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'previous_share_number' => 'integer',
            'share_number' => 'integer',
            'share_value_per_share' => 'decimal:2',
            'share_cost_per_share' => 'decimal:2',
            'monthly_amount' => 'decimal:2',
            'changed_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
