<?php

use Bernskiold\LaravelBlame\Tests\Models\Post;
use Bernskiold\LaravelBlame\Tests\Models\Thing;
use Bernskiold\LaravelBlame\Tests\Models\User;

it('tracks both creator and updater through the Blameable trait', function () {
    $author = User::create(['name' => 'Ada']);
    $editor = User::create(['name' => 'Linus']);

    $this->actingAs($author);
    $post = Post::create(['title' => 'Hello']);

    expect($post->fresh()->created_by_id)->toBe($author->id)
        ->and($post->fresh()->updated_by_id)->toBe($author->id);

    $this->actingAs($editor);
    $post->update(['title' => 'Updated']);

    $post = $post->fresh();

    expect($post->created_by_id)->toBe($author->id)
        ->and($post->updated_by_id)->toBe($editor->id);
});

it('supports custom column names via constants', function () {
    $user = User::create(['name' => 'Ada']);
    $this->actingAs($user);

    $thing = Thing::create(['name' => 'Gadget']);

    expect($thing->getCreatedByColumn())->toBe('author_id')
        ->and($thing->getUpdatedByColumn())->toBe('editor_id')
        ->and($thing->fresh()->author_id)->toBe($user->id)
        ->and($thing->fresh()->editor_id)->toBe($user->id)
        ->and($thing->createdBy->is($user))->toBeTrue()
        ->and($thing->updatedBy->is($user))->toBeTrue();
});
