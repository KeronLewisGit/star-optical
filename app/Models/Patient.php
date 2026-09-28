<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'first_name', 'last_name', 'phone', 'phone_alt', 'email', 'date_of_birth', 'gender', 'address',
    'emergency_contact_name', 'emergency_contact_phone', 'medical_notes', 'allergies',
    'insurance_provider', 'status',
])]
class Patient extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUSES = ['active', 'inactive'];

    public const GENDERS = ['female', 'male', 'other', 'prefer_not_to_say'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'medical_notes' => 'encrypted',
            'allergies' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            $patient->patient_number ??= self::nextPatientNumber();
            $patient->created_by ??= auth()->id();
            $patient->phone = self::normalisePhone($patient->phone);
        });

        static::updating(function (Patient $patient) {
            $patient->phone = self::normalisePhone($patient->phone);
        });
    }

    public static function nextPatientNumber(): string
    {
        $last = static::withTrashed()->max('id') ?? 0;

        return sprintf('SO-%06d', $last + 1);
    }

    /**
     * Canonical digits-only form so the same number always matches regardless of
     * how it was typed. Local Trinidad & Tobago numbers become 1868XXXXXXX.
     */
    public static function normalisePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?: '';

        if (strlen($digits) === 7) {
            return '1868'.$digits;
        }
        if (strlen($digits) === 10 && str_starts_with($digits, '868')) {
            return '1'.$digits;
        }

        return $digits;
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getFormattedPhoneAttribute(): string
    {
        $p = $this->phone;
        if (preg_match('/^1?(868)(\d{3})(\d{4})$/', $p, $m)) {
            return "({$m[1]}) {$m[2]}-{$m[3]}";
        }

        return $p;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $digits = self::normalisePhone($term);

        return $query->where(function (Builder $q) use ($term, $digits) {
            $q->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('patient_number', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
            if ($digits !== '') {
                $q->orWhere('phone', 'like', "%{$digits}%");
            }
        });
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class)->latest();
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class)->latest('exam_date');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(PatientNote::class)->latest();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
