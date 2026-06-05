<?php

return [

    /*
     * The user model that the `createdBy` / `updatedBy` relations point to.
     * Leave null to use the application's configured auth user model
     * (config('auth.providers.users.model')).
     */
    'user_model' => null,

    /*
     * The foreign key columns used to store the creating / updating user.
     * Individual models may override these with `CREATED_BY_COLUMN` /
     * `UPDATED_BY_COLUMN` constants.
     */
    'created_by_column' => 'created_by_id',
    'updated_by_column' => 'updated_by_id',

    /*
     * Foreign key behaviour for the `createdBy()`, `updatedBy()` and
     * `blameable()` schema macros.
     */
    'foreign_keys' => [

        // Whether the schema macros should add a foreign key constraint.
        'constrained' => true,

        // The referenced table. Leave null to let Laravel guess ("users").
        // Pass a fully-qualified name per call for cross-database schemas.
        'table' => null,

        // The on-delete behaviour: 'null', 'cascade' or 'none'.
        'on_delete' => 'null',

    ],

];
