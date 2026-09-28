<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'exam_date', 'od_sphere', 'od_cylinder', 'od_axis', 'od_add',
    'os_sphere', 'os_cylinder', 'os_axis', 'os_add', 'pd', 'examined_by', 'notes',
])]
class Prescription extends Model
{
    use LogsActivity;

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'notes' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (Prescription $rx) => $rx->created_by ??= auth()->id());
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
