<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_code',
        'full_name',
        'share_number',
        'father_name',
        'mother_name',
        'spouse_name',
        'spouse_phone',
        'phone_search',
        'blood_group',
        'religion',
        'education',
        'emergency_contact_name',
        'emergency_contact_phone',
        'phone',
        'nid_number',
        'date_of_birth',
        'occupation',
        'address',
        'present_address',
        'permanent_address',
        'nominee_name',
        'nominee_relation',
        'nominee_phone',
        'reference_name',
        'reference_phone',
        'remarks',
        'join_date',
        'membership_status',
    ];

    protected function casts(): array
    {
        return [
            'share_number' => 'integer',
            'phone' => 'encrypted',
            'phone_search' => 'string',
            'nid_number' => 'encrypted',
            'address' => 'encrypted',
            'present_address' => 'encrypted',
            'permanent_address' => 'encrypted',
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

    public function shareHistories(): HasMany
    {
        return $this->hasMany(MemberShareHistory::class)->latest('changed_at');
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany('payment_month');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function getCheckoutEligibleOnAttribute(): ?Carbon
    {
        $months = (int) app(\App\Services\SettingsService::class)->get('checkout_eligible_months', 12);

        if (! $this->join_date) {
            return null;
        }

        return Carbon::parse($this->join_date)->copy()->addMonths($months);
    }

    public static function nextMemberCode(): string
    {
        $lastCode = static::query()
            ->orderByDesc('id')
            ->value('member_code');

        $nextNumber = 1;

        if (is_string($lastCode) && preg_match('/^DS-(\d+)$/', $lastCode, $matches) === 1) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return sprintf('DS-%04d', $nextNumber);
    }
}
