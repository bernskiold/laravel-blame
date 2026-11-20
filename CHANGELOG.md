# Changelog

All notable changes to `laravel-blame` will be documented in this file.

## 1.0.1 - 2026-11-20

### What's Changed

* Add PHP 8.5 and 8.6 support and drop Laravel 11 by @ErikBernskiold in https://github.com/bernskiold/laravel-blame/pull/3
* Make PHP 8.6 a required part of the test matrix by @ErikBernskiold in https://github.com/bernskiold/laravel-blame/pull/4

### New Contributors

* @ErikBernskiold made their first contribution in https://github.com/bernskiold/laravel-blame/pull/3

**Full Changelog**: https://github.com/bernskiold/laravel-blame/compare/1.0.0...1.0.1

## 1.0.0 - 2026-06-06

**Full Changelog**: https://github.com/bernskiold/laravel-blame/commits/1.0.0

## Unreleased

- Initial release.
- `TracksCreatedBy` trait recording the creating user in `created_by_id` with a `createdBy()` relation.
- `TracksUpdatedBy` trait recording the updating user in `updated_by_id` (on create and update) with an `updatedBy()` relation.
- `TracksDeletedBy` trait recording the soft-delete remover in `deleted_by_id` (cleared on restore) with a `deletedBy()` relation.
- `Blameable` trait combining created + updated tracking.
- Configurable user model, column names and foreign key behaviour.
- Overridable acting-user resolution via `Blame::resolveUserIdUsing()`.
- `createdBy()`, `updatedBy()`, `deletedBy()`, `blameable()` and `dropBlameable()` schema blueprint macros.
