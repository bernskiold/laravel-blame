# Laravel Blame

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bernskiold/laravel-blame.svg?style=flat-square)](https://packagist.org/packages/bernskiold/laravel-blame)
[![Tests](https://img.shields.io/github/actions/workflow/status/bernskiold/laravel-blame/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/bernskiold/laravel-blame/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/bernskiold/laravel-blame.svg?style=flat-square)](https://packagist.org/packages/bernskiold/laravel-blame)

Automatically track which user created and last updated your Eloquent models —
with `createdBy()` / `updatedBy()` relations and schema macros.

## Installation

```bash
composer require bernskiold/laravel-blame
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag=blame-config
```

## Schema

Add the columns with the blueprint macros:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->blameable();   // created_by_id + updated_by_id (nullable, nullOnDelete)
    $table->timestamps();
});
```

You can also add them individually with `$table->createdBy()`,
`$table->updatedBy()` and `$table->deletedBy()`. Each macro returns the column
definition for further chaining, and accepts an explicit column name and a
referenced table (handy for cross-database schemas):

```php
$table->createdBy('author_id', 'reporting.users');
```

## Usage

Pick the trait that fits the model:

```php
use Bernskiold\LaravelBlame\Concerns\Blameable;       // created + updated
use Bernskiold\LaravelBlame\Concerns\TracksCreatedBy; // creator only
use Bernskiold\LaravelBlame\Concerns\TracksUpdatedBy; // updater only
use Bernskiold\LaravelBlame\Concerns\TracksDeletedBy; // soft-delete remover

class Post extends Model
{
    use Blameable;
}
```

That gives you:

```php
$post->created_by_id;   // set once, on creation
$post->updated_by_id;   // set on creation and every update

$post->createdBy;       // BelongsTo User
$post->updatedBy;       // BelongsTo User
```

The creating user is only written when the column is empty, so you can override
it explicitly. The updating user is **not** overwritten when no authenticated
user can be resolved (e.g. a queue or console process), preserving the last
known editor.

### Tracking the soft-delete remover

`TracksDeletedBy` records who soft-deleted a row in `deleted_by_id` and clears
it on restore. It only acts on models that also use Laravel's `SoftDeletes`
(there is no row to annotate after a hard delete), and it is **not** bundled
into `Blameable`, since most models don't soft-delete:

```php
use Bernskiold\LaravelBlame\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes, TracksDeletedBy;
}

$post->deletedBy;   // BelongsTo User
```

Add the column with `$table->deletedBy();`.

### A note on model events

The creating / updating / deleting user is captured through Eloquent model
events, so it works for `create()`, `save()`, `update()`, `delete()` and
`restore()`. Mass operations that bypass model events — e.g.
`Post::query()->update([...])` or `Post::query()->delete()` — will **not** set
the blame columns. Set them explicitly in those queries if you need them.

## Resolving the user

By default the acting user is `auth()->id()`. Override this for contexts without
an authenticated user — imports, queues, commands:

```php
use Bernskiold\LaravelBlame\Support\Blame;

Blame::resolveUserIdUsing(fn () => $importJob->triggeredByUserId);
```

The relations point at `config('auth.providers.users.model')` by default; set
`blame.user_model` to override.

## Custom column names

Globally in `config/blame.php`, or per model:

```php
class Post extends Model
{
    use Blameable;

    public const CREATED_BY_COLUMN = 'author_id';
    public const UPDATED_BY_COLUMN = 'editor_id';
}
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
