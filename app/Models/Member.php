<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_code',
        'full_name',
        'phone',
        'nid_number',
        'date_of_birth',
        'occupation',
        'address',
        'nominee_name',
        'nominee_relation',
        'nominee_phone',
        'join_date',
        'membership_status',
        'checkout_eligible_after_months',
    ];

    protected function casts(): array
    {
        return [
            'phone' => 'encrypted',
            'nid_number' => 'encrypted',
            'address' => 'encrypted',
            'date_of_birth' => 'date',
            'join_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MemberDocument::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
