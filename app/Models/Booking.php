<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'name', 'phone', 'email', 'service', 'preferred_date', 'preferred_time', 'notes',
    'status', 'source', 'appointment_at', 'internal_notes', 'patient_id', 'assigned_to', 'contacted_at', 'ip_hash', 'user_agent',
])]
class Booking extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUSES = ['new', 'contacted', 'scheduled', 'completed', 'cancelled'];

    public const SOURCES = ['website', 'whatsapp', 'phone', 'walk-in'];

    public const SERVICES = [
        'Free eye examination',
        'New frames',
        'Sunglasses',
        'Kids eye exam & frames',
        'Repair / adjustment',
        'Something else',
    ];

    public const TIMES = ['Morning', 'Midday', 'Afternoon'];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'appointment_at' => 'datetime',
            'contacted_at' => 'datetime',
            'notes' => 'encrypted',
            'internal_notes' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            $booking->reference ??= self::generateReference();
            $booking->phone = Patient::normalisePhone($booking->phone);
        });

        static::updating(function (Booking $booking) {
            $booking->phone = Patient::normalisePhone($booking->phone);
        });
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'SO'.strtoupper(Str::random(6));
        } while (static::withTrashed()->where('reference', $ref)->exists());

        return $ref;
    }

    public function getFormattedPhoneAttribute(): string
    {
        if (preg_match('/^1?(868)(\d{3})(\d{4})$/', $this->phone, $m)) {
            return "({$m[1]}) {$m[2]}-{$m[3]}";
        }

        return $this->phone;
    }

    public function isConverted(): bool
    {
        return $this->patient_id !== null;
    }

    /** Patients that look like the same person (same phone or email). */
    public function matchingPatients()
    {
        return Patient::query()
            ->where('phone', $this->phone)
            ->when($this->email, fn ($q) => $q->orWhere('email', $this->email))
            ->limit(5)
            ->get();
    }

    /** The text the customer sends us on WhatsApp after booking. */
    public function whatsappMessage(): string
    {
        $lines = [
            "Hi Star Optical! I've just booked online (ref {$this->reference}).",
            'Name: '.$this->name,
            'Service: '.$this->service,
        ];
        if ($this->preferred_date) {
            $lines[] = 'Preferred day: '.$this->preferred_date->format('D j M Y');
        }
        if ($this->preferred_time) {
            $lines[] = 'Preferred time: '.$this->preferred_time;
        }

        return implode("\n", $lines);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }
        $digits = Patient::normalisePhone($term);

        return $query->where(function (Builder $q) use ($term, $digits) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
            if ($digits !== '') {
                $q->orWhere('phone', 'like', "%{$digits}%");
            }
        });
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status && in_array($status, self::STATUSES, true) ? $query->where('status', $status) : $query;
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
