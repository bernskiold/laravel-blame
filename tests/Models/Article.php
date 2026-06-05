<?php

namespace Bernskiold\LaravelBlame\Tests\Models;

use Bernskiold\LaravelBlame\Concerns\TracksCreatedBy;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use TracksCreatedBy;

    protected $guarded = [];
}
