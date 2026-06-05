<?php

namespace Bernskiold\LaravelBlame\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Convenience trait combining {@see TracksCreatedBy} and {@see TracksUpdatedBy}
 * for models that want to track both the creating and the updating user.
 *
 * @mixin Model
 */
trait Blameable
{
    use TracksCreatedBy;
    use TracksUpdatedBy;
}
