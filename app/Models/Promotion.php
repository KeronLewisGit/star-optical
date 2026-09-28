<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['title', 'kicker', 'body', 'cta_text', 'theme', 'image_path', 'sort_order', 'is_active', 'starts_at', 'ends_at'])]
class Promotion extends Model
{
    use LogsActivity;

    public const THEMES = ['blue', 'gold', 'dark', 'navy', 'teal'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    /** Promotions that should be visible on the public site right now. */
    public function scopeLive(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $today))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $today))
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function whatsappMessage(): string
    {
        return "Hi Star Optical! I'd like to claim the \"{$this->title}\" promotion.";
    }
}
