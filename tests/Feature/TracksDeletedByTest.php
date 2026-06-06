<?php

use Bernskiold\LaravelBlame\Tests\Models\SoftPost;
use Bernskiold\LaravelBlame\Tests\Models\User;
use Illuminate\Support\Facades\Schema;

it('records the user that soft-deleted the model', function () {
    $author = User::create(['name' => 'Ada']);
    $remover = User::create(['name' => 'Linus']);

    $this->actingAs($author);
    $post = SoftPost::create(['title' => 'Hello']);

    $this->actingAs($remover);
    $post->delete();

    expect($post->fresh()->deleted_by_id)->toBe($remover->id)
        ->and($post->fresh()->trashed())->toBeTrue();
});

it('clears the deleter when the model is restored', function () {
    $user = User::create(['name' => 'Ada']);
    $this->actingAs($user);

    $post = SoftPost::create(['title' => 'Hello']);
    $post->delete();
    expect($post->fresh()->deleted_by_id)->toBe($user->id);

    $post->restore();

    expect($post->fresh()->deleted_by_id)->toBeNull();
});

it('exposes a deletedBy relation', function () {
    $user = User::create(['name' => 'Ada']);
    $this->actingAs($user);

    $post = SoftPost::create(['title' => 'Hello']);
    $post->delete();

    expect($post->fresh()->deletedBy->is($user))->toBeTrue();
});

it('leaves the deleter null when no user is authenticated', function () {
    $post = SoftPost::create(['title' => 'Hello']);

    $post->delete();

    expect($post->fresh()->deleted_by_id)->toBeNull();
});

it('adds the deleted_by column through the schema macro', function () {
    expect(Schema::hasColumn('soft_posts', 'deleted_by_id'))->toBeTrue();
});
