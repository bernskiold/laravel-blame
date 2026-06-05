<?php

namespace Bernskiold\LaravelBlame\Support;

use Illuminate\Database\Eloquent\Model;

class Blame
{
    /**
     * @var (callable(): (int|string|null))|null
     */
    protected static $userIdResolver = null;

    /**
     * Override how the acting user id is resolved. Useful for queues, console
     * commands or import processes where there is no authenticated user.
     *
     * @param  (callable(): (int|string|null))|null  $resolver
     */
    public static function resolveUserIdUsing(?callable $resolver): void
    {
        static::$userIdResolver = $resolver;
    }

    public static function resolveUserId(): int|string|null
    {
        if (static::$userIdResolver !== null) {
            return call_user_func(static::$userIdResolver);
        }

        return auth()->guard()->id();
    }

    /**
     * @return class-string<Model>
     */
    public static function userModel(): string
    {
        return config('blame.user_model')
            ?? config('auth.providers.users.model')
            ?? 'App\\Models\\User';
    }

    /**
     * The database table the user model lives in. Used as the default
     * referenced table for the schema macros.
     */
    public static function userTable(): string
    {
        $model = static::userModel();

        return class_exists($model) ? (new $model)->getTable() : 'users';
    }
}
