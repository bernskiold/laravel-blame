<?php

namespace Bernskiold\LaravelBlame\Concerns;

use Bernskiold\LaravelBlame\Support\Blame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Records the user that created the model in a `created_by_id` column when it is
 * first persisted, and exposes a `createdBy()` relation.
 *
 * @mixin Model
 */
trait TracksCreatedBy
{
    public static function bootTracksCreatedBy(): void
    {
        static::creating(function (Model $model): void {
            $column = $model->getCreatedByColumn();

            if ($model->{$column} === null && ($userId = Blame::resolveUserId()) !== null) {
                $model->{$column} = $userId;
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Blame::userModel(), $this->getCreatedByColumn());
    }

    public function getCreatedByColumn(): string
    {
        return defined(static::class.'::CREATED_BY_COLUMN')
            ? static::CREATED_BY_COLUMN
            : config('blame.created_by_column', 'created_by_id');
    }
}
