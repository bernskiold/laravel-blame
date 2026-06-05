<?php

namespace Bernskiold\LaravelBlame\Tests\Models;

use Bernskiold\LaravelBlame\Concerns\Blameable;
use Illuminate\Database\Eloquent\Model;

class Thing extends Model
{
    use Blameable;

    public const CREATED_BY_COLUMN = 'author_id';

    public const UPDATED_BY_COLUMN = 'editor_id';

    protected $guarded = [];
}
