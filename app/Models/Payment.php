<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'payment_month',
        'due_date',
        'share_value',
        'share_cost',
        'fine_amount',
        'is_late',
        'total_amount',
        'amount_paid',
        'payment_status_detail',
        'payment_method',
        'transaction_no',
        'status',
        'approved_by',
        'approved_at',
        'receipt_no',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_month' => 'date',
            'due_date' => 'date',
            'share_value' => 'decimal:2',
            'share_cost' => 'decimal:2',
            'fine_amount' => 'decimal:2',
            'is_late' => 'boolean',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'approved_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
