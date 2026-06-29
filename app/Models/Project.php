<?php

namespace App\Models;

use App\Services\TransactionLedgerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Project extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (self $project): void {
            app(TransactionLedgerService::class)->syncProject($project);
        });

        static::deleted(function (self $project): void {
            app(TransactionLedgerService::class)->removeSource($project);
        });
    }

    protected $fillable = [
        'name',
        'description',
        'invested_amount',
        'start_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'invested_amount' => 'decimal:2',
            'start_date' => 'date',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(ProjectIncome::class);
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'source');
    }
}
