<?php

use Bernskiold\LaravelBlame\Tests\Models\Article;
use Bernskiold\LaravelBlame\Tests\Models\User;

it('records the authenticated user as the creator', function () {
    $user = User::create(['name' => 'Ada']);
    $this->actingAs($user);

    $article = Article::create(['title' => 'Hello']);

    expect($article->created_by_id)->toBe($user->id)
        ->and($article->fresh()->created_by_id)->toBe($user->id);
});

it('does not overwrite an explicitly provided creator', function () {
    $acting = User::create(['name' => 'Ada']);
    $explicit = User::create(['name' => 'Linus']);
    $this->actingAs($acting);

    $article = Article::create(['title' => 'Hello', 'created_by_id' => $explicit->id]);

    expect($article->created_by_id)->toBe($explicit->id);
});

it('leaves the creator null when no user is authenticated', function () {
    $article = Article::create(['title' => 'Hello']);

    expect($article->created_by_id)->toBeNull();
});

it('exposes a createdBy relation', function () {
    $user = User::create(['name' => 'Ada']);
    $this->actingAs($user);

    $article = Article::create(['title' => 'Hello']);

    expect($article->createdBy)->toBeInstanceOf(User::class)
        ->and($article->createdBy->is($user))->toBeTrue();
});
