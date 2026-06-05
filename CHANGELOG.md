# Changelog

All notable changes to `laravel-blame` will be documented in this file.

## 1.0.0 - Unreleased

- Initial release.
- `TracksCreatedBy` trait recording the creating user in `created_by_id` with a `createdBy()` relation.
- `TracksUpdatedBy` trait recording the updating user in `updated_by_id` (on create and update) with an `updatedBy()` relation.
- `Blameable` trait combining both.
- Configurable user model, column names and foreign key behaviour.
- Overridable acting-user resolution via `Blame::resolveUserIdUsing()`.
- `createdBy()`, `updatedBy()`, `blameable()` and `dropBlameable()` schema blueprint macros.
