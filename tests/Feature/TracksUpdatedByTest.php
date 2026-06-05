<?php

use Bernskiold\LaravelBlame\Tests\Models\Post;
use Bernskiold\LaravelBlame\Tests\Models\User;

it('records the updater on creation so it persists with the insert', function () {
    $user = User::create(['name' => 'Ada']);
    $this->actingAs($user);

    $post = Post::create(['title' => 'Hello']);

    expect($post->fresh()->updated_by_id)->toBe($user->id);
});

it('records the updater on every update', function () {
    $author = User::create(['name' => 'Ada']);
    $editor = User::create(['name' => 'Linus']);

    $this->actingAs($author);
    $post = Post::create(['title' => 'Hello']);

    $this->actingAs($editor);
    $post->update(['title' => 'Updated']);

    expect($post->fresh()->updated_by_id)->toBe($editor->id);
});

it('keeps the previous updater when no user is authenticated on update', function () {
    $author = User::create(['name' => 'Ada']);
    $this->actingAs($author);
    $post = Post::create(['title' => 'Hello']);

    auth()->logout();
    $post->update(['title' => 'Updated by a job']);

    expect($post->fresh()->updated_by_id)->toBe($author->id);
});

it('exposes an updatedBy relation', function () {
    $user = User::create(['name' => 'Ada']);
    $this->actingAs($user);

    $post = Post::create(['title' => 'Hello']);

    expect($post->updatedBy)->toBeInstanceOf(User::class)
        ->and($post->updatedBy->is($user))->toBeTrue();
});
