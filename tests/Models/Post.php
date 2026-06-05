<?php

namespace Bernskiold\LaravelBlame\Tests\Models;

use Bernskiold\LaravelBlame\Concerns\Blameable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Blameable;

    protected $guarded = [];
}
