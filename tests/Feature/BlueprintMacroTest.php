<?php

use Illuminate\Support\Facades\Schema;

it('adds both columns through the blameable macro', function () {
    expect(Schema::hasColumn('posts', 'created_by_id'))->toBeTrue()
        ->and(Schema::hasColumn('posts', 'updated_by_id'))->toBeTrue();
});

it('adds only the created_by column through the createdBy macro', function () {
    expect(Schema::hasColumn('articles', 'created_by_id'))->toBeTrue()
        ->and(Schema::hasColumn('articles', 'updated_by_id'))->toBeFalse();
});
