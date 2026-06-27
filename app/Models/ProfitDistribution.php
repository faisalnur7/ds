<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfitDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'member_id',
        'profit_amount',
        'distribution_date',
        'reference_no',
    ];

    protected function casts(): array
    {
        return [
            'profit_amount' => 'decimal:2',
            'distribution_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
