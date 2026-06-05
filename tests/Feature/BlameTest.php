<?php

use Bernskiold\LaravelBlame\Support\Blame;
use Bernskiold\LaravelBlame\Tests\Models\Article;
use Bernskiold\LaravelBlame\Tests\Models\User;

it('resolves the user model from the auth config', function () {
    expect(Blame::userModel())->toBe(User::class);
});

it('prefers the explicitly configured user model', function () {
    config()->set('blame.user_model', 'App\\Custom\\Person');

    expect(Blame::userModel())->toBe('App\\Custom\\Person');
});

it('uses a custom user id resolver when set', function () {
    $user = User::create(['name' => 'Ada']);

    Blame::resolveUserIdUsing(fn () => $user->id);

    $article = Article::create(['title' => 'Hello']);

    expect($article->created_by_id)->toBe($user->id);
});

it('falls back to the auth id when the resolver is cleared', function () {
    $user = User::create(['name' => 'Ada']);
    $this->actingAs($user);

    Blame::resolveUserIdUsing(null);

    $article = Article::create(['title' => 'Hello']);

    expect($article->created_by_id)->toBe($user->id);
});
