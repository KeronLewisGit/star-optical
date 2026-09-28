<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['body'])]
class PatientNote extends Model
{
    use LogsActivity;

    protected function casts(): array
    {
        return ['body' => 'encrypted'];
    }

    protected static function booted(): void
    {
        static::creating(fn (PatientNote $note) => $note->user_id ??= auth()->id());
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
