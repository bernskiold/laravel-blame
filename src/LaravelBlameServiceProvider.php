<?php

namespace Bernskiold\LaravelBlame;

use Bernskiold\LaravelBlame\Support\Blame;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class LaravelBlameServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        AboutCommand::add('Laravel Blame', fn () => ['Version' => '1.0.0']);

        $this->publishes([
            __DIR__.'/../config/blame.php' => config_path('blame.php'),
        ], 'blame-config');

        $this->registerBlueprintMacros();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/blame.php', 'blame'
        );
    }

    protected function registerBlueprintMacros(): void
    {
        $userColumn = function (Blueprint $table, string $column, ?string $constrainedTable): ForeignIdColumnDefinition {
            $definition = $table->foreignId($column)->nullable();

            if (config('blame.foreign_keys.constrained', true)) {
                $foreignKey = $definition->constrained(
                    $constrainedTable ?? config('blame.foreign_keys.table') ?? Blame::userTable()
                );

                match (config('blame.foreign_keys.on_delete', 'null')) {
                    'cascade' => $foreignKey->cascadeOnDelete(),
                    'none' => null,
                    default => $foreignKey->nullOnDelete(),
                };
            }

            return $definition;
        };

        Blueprint::macro('createdBy', function (?string $column = null, ?string $constrainedTable = null) use ($userColumn): ForeignIdColumnDefinition {
            /** @var Blueprint $this */
            return $userColumn($this, $column ?? config('blame.created_by_column', 'created_by_id'), $constrainedTable);
        });

        Blueprint::macro('updatedBy', function (?string $column = null, ?string $constrainedTable = null) use ($userColumn): ForeignIdColumnDefinition {
            /** @var Blueprint $this */
            return $userColumn($this, $column ?? config('blame.updated_by_column', 'updated_by_id'), $constrainedTable);
        });

        Blueprint::macro('blameable', function (?string $constrainedTable = null) use ($userColumn): Blueprint {
            /** @var Blueprint $this */
            $userColumn($this, config('blame.created_by_column', 'created_by_id'), $constrainedTable);
            $userColumn($this, config('blame.updated_by_column', 'updated_by_id'), $constrainedTable);

            return $this;
        });

        Blueprint::macro('dropBlameable', function (): void {
            /** @var Blueprint $this */
            $columns = [
                config('blame.created_by_column', 'created_by_id'),
                config('blame.updated_by_column', 'updated_by_id'),
            ];
            $constrained = config('blame.foreign_keys.constrained', true);

            foreach ($columns as $column) {
                $constrained ? $this->dropConstrainedForeignId($column) : $this->dropColumn($column);
            }
        });
    }
}
