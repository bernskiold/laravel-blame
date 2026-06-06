# Know who created, updated and deleted your Eloquent models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bernskiold/laravel-blame.svg?style=flat-square)](https://packagist.org/packages/bernskiold/laravel-blame)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/bernskiold/laravel-blame/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/bernskiold/laravel-blame/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/bernskiold/laravel-blame/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/bernskiold/laravel-blame/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bernskiold/laravel-blame.svg?style=flat-square)](https://packagist.org/packages/bernskiold/laravel-blame)

"Who changed this?" is a question every serious application eventually has to answer. This package quietly records the user behind each create, update and soft-delete — no manual `created_by_id = auth()->id()` scattered through your controllers, no forgetting to set it on that one form.

```php
class Post extends Model
{
    use Blameable;
}

$post->createdBy;   // the user who created it
$post->updatedBy;   // the user who last touched it
```

Add a trait, add the columns, and the right user id is captured automatically on every save — with relations ready for eager loading and display.

## Why you'll like it

- **Set-and-forget.** The acting user is captured through model events on create, update, soft-delete and restore. You never wire `auth()->id()` by hand again.
- **Pick exactly what you need.** Track the creator, the updater, the deleter — or all three with the combined `Blameable` trait.
- **Relations included.** `createdBy()`, `updatedBy()` and `deletedBy()` are ready to eager-load and render.
- **Sensible, safe defaults.** The creator is only set when empty (so you can override it), and the updater is left untouched when no user is authenticated — a background job won't wipe the last known editor.
- **Works anywhere.** A pluggable resolver lets you record the right user from queues, commands and imports, not just web requests.
- **Configurable to the column.** Bring your own column names, user model, and foreign key behaviour — globally or per model.
- **Tidy migrations.** `$table->blameable()` adds the columns (and foreign keys) in one line.

## Installation

You can install the package via Composer:

```bash
composer require bernskiold/laravel-blame
```

If you'd like to change the column names, the user model, or the foreign key behaviour, publish the config:

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

You can also add columns individually with `$table->createdBy()`, `$table->updatedBy()` and `$table->deletedBy()`. Each macro returns the column definition for further chaining, and accepts an explicit column name and a referenced table (handy for cross-database schemas):

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

The creating user is only written when the column is empty, so you can override it explicitly. The updating user is **not** overwritten when no authenticated user can be resolved (for example, a queue or console process), preserving the last known editor.

### Tracking the soft-delete remover

`TracksDeletedBy` records who soft-deleted a row in `deleted_by_id` and clears it again on restore. It only acts on models that also use Laravel's `SoftDeletes` — there's no row to annotate after a hard delete — and it is intentionally **not** bundled into `Blameable`, since most models don't soft-delete:

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

### Resolving the acting user

By default the acting user is `auth()->id()`. Override this for contexts without an authenticated user — imports, queues, scheduled commands:

```php
use Bernskiold\LaravelBlame\Support\Blame;

Blame::resolveUserIdUsing(fn () => $importJob->triggeredByUserId);
```

The relations point at `config('auth.providers.users.model')` by default; set `blame.user_model` to override.

### Custom column names

Globally in `config/blame.php`, or per model with constants:

```php
class Post extends Model
{
    use Blameable;

    public const CREATED_BY_COLUMN = 'author_id';
    public const UPDATED_BY_COLUMN = 'editor_id';
}
```

### A note on model events

The creating / updating / deleting user is captured through Eloquent model events, so it works for `create()`, `save()`, `update()`, `delete()` and `restore()`. Mass operations that bypass model events — such as `Post::query()->update([...])` or `Post::query()->delete()` — will **not** set the blame columns. Set them explicitly in those queries if you need them.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Erik Bernskiöld](https://bernskiold.com)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.
