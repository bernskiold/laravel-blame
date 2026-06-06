<?php

namespace Bernskiold\LaravelBlame\Tests\Models;

use Bernskiold\LaravelBlame\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoftPost extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $guarded = [];
}
