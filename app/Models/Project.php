<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

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
}
