<?php

namespace Bernskiold\LaravelBlame\Concerns;

use Bernskiold\LaravelBlame\Support\Blame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Records the user that soft-deleted the model in a `deleted_by_id` column, and
 * clears it again when the model is restored. Exposes a `deletedBy()` relation.
 *
 * This trait only does anything on models that also use {@see SoftDeletes} —
 * there is no row left to annotate after a hard delete. The value is written in
 * the `deleted` event (after `deleted_at` is set) and cleared in `restoring`.
 *
 * @mixin Model
 */
trait TracksDeletedBy
{
    public static function bootTracksDeletedBy(): void
    {
        static::deleted(function (Model $model): void {
            if (! in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
                return;
            }

            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            if (($userId = Blame::resolveUserId()) !== null) {
                $model->{$model->getDeletedByColumn()} = $userId;
                $model->saveQuietly();
            }
        });

        static::restoring(function (Model $model): void {
            $model->{$model->getDeletedByColumn()} = null;
        });
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(Blame::userModel(), $this->getDeletedByColumn());
    }

    public function getDeletedByColumn(): string
    {
        return defined(static::class.'::DELETED_BY_COLUMN')
            ? static::DELETED_BY_COLUMN
            : config('blame.deleted_by_column', 'deleted_by_id');
    }
}
