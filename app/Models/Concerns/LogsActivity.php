<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Writes an audit-log row for every create / update / delete on the model.
 *
 * Encrypted attributes are logged as "[changed]" rather than their value so
 * that sensitive data never lands in the audit table in plain text.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn (Model $model) => $model->recordActivity('created'));
        static::updated(fn (Model $model) => $model->recordActivity('updated'));
        static::deleted(fn (Model $model) => $model->recordActivity('deleted'));

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (Model $model) => $model->recordActivity('restored'));
        }
    }

    public function recordActivity(string $action, ?string $description = null): void
    {
        $changes = null;

        if ($action === 'updated') {
            $dirty = collect($this->getChanges())
                ->except(array_merge(['updated_at'], $this->auditExcept ?? []));

            if ($dirty->isEmpty()) {
                return; // nothing meaningful changed (e.g. only timestamps)
            }

            $changes = $dirty->mapWithKeys(function ($value, $key) {
                if ($this->isSensitiveAttribute($key)) {
                    return [$key => ['from' => '[hidden]', 'to' => '[changed]']];
                }

                return [$key => ['from' => $this->getOriginal($key), 'to' => $value]];
            })->all();
        }

        ActivityLog::record(
            action: $action,
            subject: $this,
            changes: $changes,
            description: $description ?? sprintf('%s %s #%s', ucfirst($action), class_basename($this), $this->getKey()),
        );
    }

    protected function isSensitiveAttribute(string $key): bool
    {
        $cast = $this->getCasts()[$key] ?? null;

        return in_array($key, $this->auditExcept ?? [], true)
            || ($cast !== null && str_starts_with($cast, 'encrypted'));
    }
}
