<?php

namespace Bernskiold\LaravelBlame\Concerns;

use Bernskiold\LaravelBlame\Support\Blame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Records the user that last touched the model in an `updated_by_id` column,
 * both when it is created and on every update, and exposes an `updatedBy()`
 * relation.
 *
 * The value is written during the `creating` event so it persists with the
 * initial insert. When no acting user can be resolved (e.g. a console or queue
 * process) the existing value is left untouched rather than overwritten.
 *
 * @mixin Model
 */
trait TracksUpdatedBy
{
    public static function bootTracksUpdatedBy(): void
    {
        $apply = function (Model $model): void {
            if (($userId = Blame::resolveUserId()) !== null) {
                $model->{$model->getUpdatedByColumn()} = $userId;
            }
        };

        static::creating($apply);
        static::updating($apply);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Blame::userModel(), $this->getUpdatedByColumn());
    }

    public function getUpdatedByColumn(): string
    {
        return defined(static::class.'::UPDATED_BY_COLUMN')
            ? static::UPDATED_BY_COLUMN
            : config('blame.updated_by_column', 'updated_by_id');
    }
}
