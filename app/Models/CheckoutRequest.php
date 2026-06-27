<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'requested_at',
        'checkout_type',
        'partial_percentage',
        'refundable_amount',
        'outstanding_loan_deducted',
        'status',
        'approved_by',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'partial_percentage' => 'decimal:2',
            'refundable_amount' => 'decimal:2',
            'outstanding_loan_deducted' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
